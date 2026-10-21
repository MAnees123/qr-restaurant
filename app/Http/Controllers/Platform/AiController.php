<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Platform\AiSetting;
use App\Services\Platform\SmartTools;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function __construct(private SmartTools $tools) {}

    private function rid(): int { return (int) auth()->user()->restaurant_id; }

    public function index()
    {
        $rid = $this->rid();
        return view('platform.ai.index', [
            'settings' => AiSetting::forRestaurant($rid),
            'items'    => MenuItem::with('category')->where('restaurant_id', $rid)->orderBy('name')->get(),
            'forecast' => $this->tools->forecast($rid),
            'pairs'    => $this->tools->topPairs($rid),
        ]);
    }

    public function describe(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:120', 'category' => 'nullable|string|max:120']);
        $r = $this->tools->description($data['name'], $data['category'] ?? null, AiSetting::forRestaurant($this->rid()));
        return back()->withInput()->with('ai_description', $r);
    }

    public function price(Request $request)
    {
        $data = $request->validate([
            'cost' => 'required|numeric|min:0.01|max:1000000', 'margin' => 'required|numeric|min:1|max:95', 'item_id' => 'nullable|integer',
        ]);
        $itemId = null;
        if (! empty($data['item_id'])) {
            $item = MenuItem::where('restaurant_id', $this->rid())->find($data['item_id']);
            abort_unless($item, 404);
            $itemId = $item->id;
        }
        return back()->withInput()->with('ai_price', $this->tools->priceSuggestion((float) $data['cost'], (float) $data['margin'], $itemId, $this->rid()));
    }

    public function recommendations(Request $request)
    {
        $item = MenuItem::where('restaurant_id', $this->rid())->find((int) $request->query('item_id'));
        abort_unless($item, 404);
        return response()->json([
            'item'  => ['id' => $item->id, 'name' => $item->name],
            'bought_together' => $this->tools->boughtWith($item->id, $this->rid()),
            'method' => 'co-occurrence statistics over past orders (no AI model)',
        ]);
    }

    public function settings(Request $request)
    {
        $data = $request->validate([
            'llm_url' => 'nullable|url:https|max:255', 'llm_key' => 'nullable|string|max:500',
            'llm_model' => 'nullable|string|max:100', 'target_margin' => 'required|integer|min:1|max:95',
        ]);
        $s = AiSetting::forRestaurant($this->rid());
        $s->restaurant_id = $this->rid();
        $s->llm_url = $data['llm_url'] ?? null;
        $s->llm_model = $data['llm_model'] ?? null;
        $s->target_margin = $data['target_margin'];
        if ($request->boolean('clear_key') || empty($data['llm_url'])) {
            $s->llm_key = null;
        } elseif (! empty($data['llm_key'])) {
            $s->llm_key = $data['llm_key'];
        }
        $s->save();
        return redirect()->route('admin.ai.index')->with('success', 'AI settings saved.');
    }
}
