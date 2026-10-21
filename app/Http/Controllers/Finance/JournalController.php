<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Account;
use App\Models\Finance\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JournalController extends Controller
{
    use Concerns;

    public function index()
    {
        return view('finance.journals.index', ['entries' => JournalEntry::with('lines.account')->where('restaurant_id', $this->rid())->latest('entry_date')->latest('id')->paginate(15)]);
    }

    public function create()
    {
        return view('finance.journals.create', ['accounts' => Account::where('restaurant_id', $this->rid())->orderBy('code')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'entry_date' => 'required|date',
            'memo' => 'required|string|max:255',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|integer',
            'lines.*.debit' => 'nullable|numeric|min:0|max:9999999999',
            'lines.*.credit' => 'nullable|numeric|min:0|max:9999999999',
        ]);
        $lines = collect($data['lines'])->map(fn ($l) => [
            'account_id' => (int) $l['account_id'], 'debit' => round((float) ($l['debit'] ?? 0), 2), 'credit' => round((float) ($l['credit'] ?? 0), 2),
        ])->filter(fn ($l) => $l['debit'] > 0 || $l['credit'] > 0)->values();

        if ($lines->count() < 2) {
            throw ValidationException::withMessages(['lines' => 'Enter at least two lines with an amount.']);
        }
        if ($lines->contains(fn ($l) => $l['debit'] > 0 && $l['credit'] > 0)) {
            throw ValidationException::withMessages(['lines' => 'A line cannot have both a debit and a credit.']);
        }
        $ids = $lines->pluck('account_id')->unique();
        if (Account::where('restaurant_id', $this->rid())->whereIn('id', $ids)->count() !== $ids->count()) {
            throw ValidationException::withMessages(['lines' => 'One or more accounts are invalid.']);
        }
        if (abs($lines->sum('debit') - $lines->sum('credit')) > 0.004) {
            throw ValidationException::withMessages(['lines' => 'Entry is not balanced: total debits must equal total credits.']);
        }

        DB::transaction(function () use ($data, $lines) {
            $entry = JournalEntry::create(['restaurant_id' => $this->rid(), 'entry_date' => $data['entry_date'], 'memo' => $data['memo']]);
            foreach ($lines as $l) {
                $entry->lines()->create($l);
            }
        });
        return redirect()->route('admin.journals.index')->with('success', 'Journal entry posted.');
    }

    public function destroy(JournalEntry $journal)
    {
        $this->own($journal)->delete();
        return redirect()->route('admin.journals.index')->with('success', 'Journal entry deleted.');
    }
}
