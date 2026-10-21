<?php

namespace App\Services\Platform;

use App\Models\Platform\AiSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Statistics and template based "smart" helpers. They need no external service.
 * Only the description generator may call an LLM, and only when the restaurant configured an endpoint.
 */
class SmartTools
{
    private const OPENERS = [
        'A guest favourite: %s', 'Our take on %s', 'Freshly prepared %s', 'Simple, honest and satisfying: %s', 'Made to order: %s',
    ];
    private const CLOSERS = [
        'Perfect to share or enjoy on your own.', 'Served hot and full of flavour.', 'A great pick any time of day.',
        'Prepared with care by our kitchen team.', 'Pairs well with your favourite drink.',
    ];
    private const CATEGORY_HINTS = [
        'starter' => 'a light way to begin your meal', 'appetizer' => 'a light way to begin your meal',
        'main' => 'a hearty main course', 'dessert' => 'a sweet finish', 'drink' => 'refreshing and ice cold',
        'beverage' => 'refreshing and ice cold', 'pizza' => 'baked until golden', 'burger' => 'stacked high and juicy',
        'salad' => 'crisp, fresh and light', 'soup' => 'warm and comforting',
    ];

    /** @return array{text:string, source:string} */
    public function description(string $name, ?string $category, AiSetting $settings, ?string $tone = null): array
    {
        if ($settings->llmConfigured()) {
            try {
                $text = $this->llm($settings, "Write a mouth-watering menu description (max 30 words, no quotes) for the dish \"$name\"" . ($category ? " in the category \"$category\"" : '') . '.');
                if ($text !== null) return ['text' => $text, 'source' => 'llm'];
            } catch (\Throwable $e) {
                // fall through to the template generator
            }
        }
        return ['text' => $this->templateDescription($name, $category), 'source' => 'template'];
    }

    public function templateDescription(string $name, ?string $category): string
    {
        $h = crc32(mb_strtolower($name));
        $opener = sprintf(self::OPENERS[$h % count(self::OPENERS)], trim($name));
        $hint = '';
        foreach (self::CATEGORY_HINTS as $k => $v) {
            if ($category && str_contains(mb_strtolower($category), $k)) { $hint = ', ' . $v; break; }
        }
        return $opener . $hint . '. ' . self::CLOSERS[($h >> 3) % count(self::CLOSERS)];
    }

    private function llm(AiSetting $s, string $prompt): ?string
    {
        $res = Http::withToken($s->llm_key)->timeout(20)->acceptJson()->post($s->llm_url, [
            'model' => $s->llm_model ?: 'default',
            'messages' => [['role' => 'user', 'content' => $prompt]],
        ]);
        if (! $res->successful()) return null;
        $text = $res->json('choices.0.message.content');
        return is_string($text) && trim($text) !== '' ? trim($text) : null;
    }

    /** Price that reaches the target margin (percent of price) plus context from sales history. */
    public function priceSuggestion(float $cost, float $margin, ?int $itemId, int $rid): array
    {
        $price = $cost / (1 - $margin / 100);
        $out = ['cost' => $cost, 'margin' => $margin, 'suggested' => round($price, 2), 'rounded' => round($price * 2) / 2, 'history' => null];
        if ($itemId) {
            $row = DB::table('order_items')->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.restaurant_id', $rid)->where('order_items.menu_item_id', $itemId)
                ->where('orders.status', '!=', 'cancelled')->where('orders.created_at', '>=', now()->subDays(30))
                ->selectRaw('COALESCE(SUM(order_items.quantity),0) as units, COALESCE(AVG(order_items.unit_price),0) as avg_price')->first();
            $out['history'] = ['units_30d' => (int) $row->units, 'avg_price' => round((float) $row->avg_price, 2)];
        }
        return $out;
    }

    /** Daily revenue for the last 28 days, then a 7-day moving-average forecast. */
    public function forecast(int $rid, int $days = 7, int $window = 7): array
    {
        $from = now()->subDays(27)->startOfDay();
        $orders = DB::table('orders')->where('restaurant_id', $rid)->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $from)->get(['created_at', 'total_amount']);
        $daily = [];
        for ($i = 27; $i >= 0; $i--) $daily[now()->subDays($i)->toDateString()] = 0.0;
        foreach ($orders as $o) {
            $d = substr((string) $o->created_at, 0, 10);
            if (isset($daily[$d])) $daily[$d] += (float) $o->total_amount;
        }
        $series = array_values($daily);
        $fc = [];
        $work = $series;
        for ($i = 1; $i <= $days; $i++) {
            $slice = array_slice($work, -$window);
            $v = round(array_sum($slice) / max(1, count($slice)), 2);
            $work[] = $v;
            $fc[] = ['date' => now()->addDays($i)->toDateString(), 'revenue' => $v];
        }
        return ['history' => $daily, 'forecast' => $fc, 'total' => round(array_sum(array_column($fc, 'revenue')), 2), 'has_data' => array_sum($series) > 0];
    }

    /** Items bought together with $itemId (co-occurrence in non-cancelled orders). */
    public function boughtWith(int $itemId, int $rid, int $limit = 5): array
    {
        $base = DB::table('order_items as a')->join('orders as o', 'o.id', '=', 'a.order_id')
            ->where('o.restaurant_id', $rid)->where('o.status', '!=', 'cancelled')->where('a.menu_item_id', $itemId);
        $ordersWithItem = (clone $base)->distinct()->count('a.order_id');
        if ($ordersWithItem === 0) return [];

        return DB::table('order_items as a')->join('orders as o', 'o.id', '=', 'a.order_id')
            ->join('order_items as b', function ($j) { $j->on('b.order_id', '=', 'a.order_id')->whereColumn('b.menu_item_id', '!=', 'a.menu_item_id'); })
            ->join('menu_items as m', 'm.id', '=', 'b.menu_item_id')
            ->where('o.restaurant_id', $rid)->where('m.restaurant_id', $rid)->where('o.status', '!=', 'cancelled')->where('a.menu_item_id', $itemId)
            ->groupBy('m.id', 'm.name')
            ->selectRaw('m.id as item_id, m.name, COUNT(DISTINCT a.order_id) as together')
            ->orderByDesc('together')->orderBy('m.id')->limit($limit)->get()
            ->map(fn ($r) => ['item_id' => (int) $r->item_id, 'name' => $r->name, 'orders_together' => (int) $r->together,
                'confidence' => round($r->together / $ordersWithItem, 2)])->all();
    }

    /** Most frequent item pairs for the whole restaurant. */
    public function topPairs(int $rid, int $limit = 8): array
    {
        return DB::table('order_items as a')->join('orders as o', 'o.id', '=', 'a.order_id')
            ->join('order_items as b', function ($j) { $j->on('b.order_id', '=', 'a.order_id')->whereColumn('b.menu_item_id', '>', 'a.menu_item_id'); })
            ->join('menu_items as ma', 'ma.id', '=', 'a.menu_item_id')->join('menu_items as mb', 'mb.id', '=', 'b.menu_item_id')
            ->where('o.restaurant_id', $rid)->where('ma.restaurant_id', $rid)->where('mb.restaurant_id', $rid)->where('o.status', '!=', 'cancelled')
            ->groupBy('ma.id', 'ma.name', 'mb.id', 'mb.name')
            ->selectRaw('ma.name as a_name, mb.name as b_name, COUNT(DISTINCT a.order_id) as together')
            ->orderByDesc('together')->limit($limit)->get()->map(fn ($r) => (array) $r)->all();
    }
}
