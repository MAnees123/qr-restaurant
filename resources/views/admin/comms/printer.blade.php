@extends('layouts.admin')

@section('title', 'Printer')
@section('header', 'Printer & Receipts')

@section('content')
@include('admin.comms._flash')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
<div class="xl:col-span-2 card p-6 md:p-8">
    <form action="{{ route('admin.printer.update') }}" method="POST" class="space-y-6">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Paper width</label>
            <select name="paper_width" class="input">
                <option value="58" @selected(old('paper_width', $settings['paper_width']) == 58)>58 mm</option>
                <option value="80" @selected(old('paper_width', $settings['paper_width']) == 80)>80 mm</option>
            </select>
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-2">Header text</label>
            <input type="text" name="header" value="{{ old('header', $settings['header']) }}" class="input"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-2">Footer text</label>
            <input type="text" name="footer" value="{{ old('footer', $settings['footer']) }}" class="input"></div>
        <label class="flex items-center gap-2"><input type="checkbox" name="show_logo" value="1" class="rounded" {{ old('show_logo', $settings['show_logo']) ? 'checked' : '' }}><span class="text-sm font-medium text-slate-700">Show branding logo on customer bill (needs Custom Branding)</span></label>
        <label class="flex items-center gap-2"><input type="checkbox" name="auto_print" value="1" class="rounded" {{ old('auto_print', $settings['auto_print']) ? 'checked' : '' }}><span class="text-sm font-medium text-slate-700">Auto-print new orders</span></label>
        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-2"><label class="block text-sm font-medium text-slate-700 mb-2">Network printer host (optional)</label>
                <input type="text" name="printer_host" value="{{ old('printer_host', $settings['printer_host']) }}" class="input" placeholder="192.168.1.50"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-2">Port</label>
                <input type="number" name="printer_port" value="{{ old('printer_port', $settings['printer_port']) }}" class="input"></div>
        </div>
        <p class="text-xs text-slate-400">Browser printing (window.print) works with any printer, including USB thermal printers installed in the OS. A host/port enables raw ESC/POS over TCP (port 9100) for new orders when auto-print is on; this needs a real network thermal printer. Without it, open the Auto-print monitor below on a PC attached to the printer.</p>
        <button class="btn btn-primary">Save</button>
    </form>
</div>
<div class="space-y-6">
    <div class="card p-6 space-y-3 self-start">
        <h3 class="text-base font-semibold text-slate-800">Test print</h3>
        <a target="_blank" href="{{ route('admin.printer.test', ['type' => 'kitchen']) }}" class="btn btn-secondary w-full">Test kitchen ticket</a>
        <a target="_blank" href="{{ route('admin.printer.test', ['type' => 'bill']) }}" class="btn btn-secondary w-full">Test customer bill</a>
        <form action="{{ route('admin.printer.network-test') }}" method="POST">@csrf
            <button class="btn btn-secondary w-full">Test network printer</button></form>
    </div>
    @if($settings['auto_print'])
    <div class="card p-6" x-data="autoPrint({{ $lastId }})">
        <h3 class="text-base font-semibold text-slate-800 mb-2">Auto-print monitor</h3>
        <p class="text-xs text-slate-500">Keep this page open: new orders open a kitchen ticket for printing (allow pop-ups).</p>
        <p class="text-xs mt-2 font-semibold text-indigo-600" x-text="'Watching... printed: ' + printed"></p>
    </div>
    @endif
</div>
</div>

<div class="card overflow-hidden mt-6">
    <div class="px-6 py-4 border-b border-slate-100 text-base font-semibold text-slate-800">Recent orders</div>
    <div class="overflow-x-auto"><table class="w-full text-left">
        <tbody>
        @forelse($orders as $o)
            <tr>
                <td class="px-6 py-4 font-semibold text-slate-800">{{ $o->order_number }}</td>
                <td class="px-6 py-4 text-sm text-slate-500">Table {{ $o->table->table_number ?? '-' }} &middot; {{ ucfirst($o->status) }}</td>
                <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                    <a target="_blank" class="btn btn-sm btn-ghost" href="{{ route('admin.printer.receipt', [$o, 'kitchen']) }}">Kitchen ticket</a>
                    <a target="_blank" class="btn btn-sm btn-secondary" href="{{ route('admin.printer.receipt', [$o, 'bill']) }}">Customer bill</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="3"><x-ui.empty-state title="No orders yet" /></td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>

<script>
function autoPrint(last) {
    return {
        printed: 0,
        init() {
            setInterval(async () => {
                try {
                    const r = await fetch(@json(route('admin.printer.poll')) + '?after=' + last, {headers: {'Accept': 'application/json'}});
                    const d = await r.json();
                    last = d.last;
                    d.ids.forEach(id => { window.open(@json(url('/admin/printer/orders')) + '/' + id + '/kitchen?autoprint=1', '_blank'); this.printed++; });
                } catch (e) {}
            }, 10000);
        }
    };
}
</script>
@endsection
