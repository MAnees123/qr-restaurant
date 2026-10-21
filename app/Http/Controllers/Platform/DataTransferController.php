<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Services\Platform\CsvTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DataTransferController extends Controller
{
    public function __construct(private CsvTransfer $svc) {}

    private function rid(): int { return (int) auth()->user()->restaurant_id; }

    private function entity(string $e): string
    {
        abort_unless($this->svc->has($e), 404);
        return $e;
    }

    private function dir(): string { return storage_path('app/imports/' . $this->rid()); }

    public function index()
    {
        return view('platform.data.index', ['entities' => $this->svc->entities()]);
    }

    public function export(string $entity)
    {
        $this->entity($entity);
        return response($this->svc->csv($this->svc->exportRows($entity, $this->rid())), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$entity}-" . now()->format('Ymd') . '.csv"',
        ]);
    }

    public function template(string $entity)
    {
        $this->entity($entity);
        return response($this->svc->template($entity), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$entity}-template.csv\"",
        ]);
    }

    public function preview(Request $request, string $entity)
    {
        $this->entity($entity);
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:2048']);
        File::ensureDirectoryExists($this->dir());
        $token = bin2hex(random_bytes(16));
        $path = $this->dir() . "/$token.csv";
        File::copy($request->file('file')->getRealPath(), $path);

        $result = $this->svc->parse($entity, $path, $this->rid());
        if ($result['header_error']) {
            File::delete($path);
            return back()->withErrors(['file' => $result['header_error']]);
        }
        return view('platform.data.preview', [
            'entity' => $entity, 'label' => $this->svc->entities()[$entity]['label'], 'columns' => $this->svc->entities()[$entity]['columns'],
            'rows' => $result['rows'], 'token' => $token,
            'valid' => collect($result['rows'])->where('errors', [])->count(),
        ]);
    }

    public function commit(Request $request, string $entity)
    {
        $this->entity($entity);
        $data = $request->validate(['token' => ['required', 'regex:/^[0-9a-f]{32}$/']]);
        $path = $this->dir() . "/{$data['token']}.csv";
        abort_unless(File::exists($path), 404);
        $result = $this->svc->parse($entity, $path, $this->rid());
        File::delete($path);
        abort_if($result['header_error'], 422, $result['header_error']);
        $r = $this->svc->commit($entity, $result['rows'], $this->rid());

        return redirect()->route('admin.data.index')
            ->with('success', "Import finished: {$r['created']} created, {$r['updated']} updated, {$r['skipped']} skipped with errors.");
    }
}
