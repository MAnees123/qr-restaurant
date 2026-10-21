@extends('layouts.admin')

@section('title', 'Printer')
@section('header', 'Printer & Receipts')

@section('content')
@include('admin.comms._flash')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
<div class="xl:col-span-2 bg-white rounded-[2rem] shadow-sm border p-8">
    <form action="{{ route('admin.printer.update') }}" method="POST" class="space-y-6">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Paper width</label>
            <select name="paper_width" class="w-full rounded-xl border-slate-200 font-bold">
                <option value="58" @selected(old('paper_width', $settings['paper_width']) == 58)>58 mm</option>
                <option value="80" @selected(old('paper_width', $settings['paper_width']) == 80)>80 mm</option>
            </select>
        </div>
        <div><label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Header text</label>
            <input type="text" name="header" value="{{ old('header', $settings['header']) }}" class="w-full rounded-xl border-slate-200 font-bold"></div>
        <div><label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Footer text</label>
            <input type="text" name="footer" value="{{ old('footer', $settings['footer']) }}" class="w-full rounded-xl border-slate-200 font-bold"></div>
        <label class="flex items-center gap-2"><input type="checkbox" name="show_logo" value="1" class="rounded" {{ old('show_logo', $settings['show_logo']) ? 'checked' : '' }}><span class="text-sm font-bold">Show branding logo on customer bill (needs Custom Branding)</span></label>
        <label class="flex items-center gap-2"><input type="checkbox" name="auto_print" value="1" class="rounded" {{ old('auto_print', $settings['auto_print']) ? 'checked' : '' }}><span class="text-sm font-bold">Auto-print new orders</span></label>
        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-2"><label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Network printer host (optional)</label>
                <input type="text" name="printer_host" value="{{ old('printer_host', $settings['printer_host']) }}" class="w-full rounded-xl border-slate-200 font-bold" placeholder="192.168.1.50"></div>
            <div><label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Port</label>
                <input type="number" name="printer_port" value="{{ old('printer_port', $settings['printer_port']) }}" class="w-full rounded-xl border-slate-200 font-bold"></div>
        </div>
        <p class="text-xs text-slate-400">Browser printing (window.print) works with any printer, including USB thermal printers installed in the OS. A host/port enables raw ESC/POS over TCP (port 9100) for new orders when auto-print is on; this needs a real network thermal printer. Without it, open the Auto-print monitor below on a PC attached to the printer.</p>
        <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-xl">Save</button>
    </form>
</div>
<div class="space-y-6">
    <div class="bg-white rounded-[2rem] shadow-sm border p-6 space-y-3">
        <h3 class="font-black text-slate-700 uppercase tracking-widest text-sm">Test print</h3>
        <a target="_blank" href="{{ route('admin.printer.test', ['type' => 'kitchen']) }}" class="block text-center bg-slate-800 text-white font-bold py-2 rounded-xl">Test kitchen ticket</a>
        <a target="_blank" href="{{ route('admin.printer.test', ['type' => 'bill']) }}" class="block text-center bg-slate-800 text-white font-bold py-2 rounded-xl">Test customer bill</a>
        <form action="{{ route('admin.printer.network-test') }}" method="POST">@csrf
            <button class="w-full bg-white border border-slate-300 text-slate-700 font-bold py-2 rounded-xl">Test network printer</button></form>
    </div>
    @if($settings['auto_print'])
    <div class="bg-white rounded-[2rem] shadow-sm border p-6" x-data="autoPrint({{ $lastId }})">
        <h3 class="font-black text-slate-700 uppercase tracking-widest text-sm mb-2">Auto-print monitor</h3>
        <p class="text-xs text-slate-500">Keep this page open: new orders open a kitchen ticket for printing (allow pop-ups).</p>
        <p class="text-xs mt-2 font-bold text-emerald-600" x-text="'Watching... printed: ' + printed"></p>
    </div>
    @endif
</div>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden mt-6">
    <div class="px-8 py-5 border-b font-black text-slate-700 uppercase tracking-widest text-sm">Recent orders</div>
    <table class="w-full text-left">
        <tbody class="divide-y divide-slate-100">
        @forelse($orders as $o)
            <tr>
                <td class="px-6 py-3 font-black">{{ $o->order_number }}</td>
                <td class="px-6 py-3 text-sm text-slate-500">Table {{ $o->table->table_number ?? '-' }} &middot; {{ ucfirst($o->status) }}</td>
                <td class="px-6 py-3 text-right space-x-3">
                    <a target="_blank" class="text-blue-500 font-black text-sm" href="{{ route('admin.printer.receipt', [$o, 'kitchen']) }}">Kitchen ticket</a>
                    <a target="_blank" class="text-emerald-600 font-black text-sm" href="{{ route('admin.printer.receipt', [$o, 'bill']) }}">Customer bill</a>
                </td>
            </tr>
        @empty
            <tr><td class="px-6 py-8 text-center text-slate-400">No orders yet.</td></tr>
        @endforelse
        </tbody>
    </table>
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
