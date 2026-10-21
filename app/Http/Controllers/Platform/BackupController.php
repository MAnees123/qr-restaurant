<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Services\Platform\BackupService;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    public function __construct(private BackupService $svc) {}

    private function rid(): int
    {
        return (int) auth()->user()->restaurant_id;
    }

    public function index()
    {
        return view('platform.backup.index', ['backups' => $this->svc->list($this->rid())]);
    }

    public function store()
    {
        $res = $this->svc->create($this->rid());
        return redirect()->route('admin.backups.index')
            ->with('success', 'Backup created (' . array_sum($res['counts']) . ' rows).');
    }

    public function download(string $name)
    {
        $p = $this->svc->path($this->rid(), $name);
        abort_unless($p, 404);
        return response()->download($p, $name);
    }

    public function destroy(string $name)
    {
        abort_unless($this->svc->delete($this->rid(), $name), 404);
        return redirect()->route('admin.backups.index')->with('success', 'Backup deleted.');
    }

    public function restore(Request $request, string $name)
    {
        abort_unless($this->svc->path($this->rid(), $name), 404);
        $request->validate(['confirm' => 'required|in:RESTORE']);
        try {
            $this->svc->restore($this->rid(), $name);
        } catch (\Throwable $e) {
            return redirect()->route('admin.backups.index')->withErrors(['restore' => 'Restore failed: ' . $e->getMessage()]);
        }
        return redirect()->route('admin.backups.index')->with('success', 'Data restored from ' . $name . '.');
    }
}
