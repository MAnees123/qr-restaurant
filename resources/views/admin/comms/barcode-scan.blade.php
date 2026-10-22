@extends('layouts.admin')

@section('title', 'Barcode Scan')
@section('header', 'Scan / Quick Order')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="quickScan()">
    <div class="lg:col-span-2 card p-6 md:p-8">
        <label class="block text-sm font-medium text-slate-700 mb-2">Scan or type a barcode, then press Enter</label>
        <input type="text" x-model="code" @keydown.enter.prevent="lookup()" x-ref="scan" autofocus autocomplete="off"
               class="input font-mono text-lg" placeholder="USB scanners type here automatically">
        <p class="text-sm mt-2" :class="ok ? 'text-emerald-600' : 'text-red-600'" x-text="msg"></p>

        <div class="overflow-x-auto mt-6"><table class="w-full text-left">
            <thead><tr><th class="px-4 py-3">Item</th><th class="px-4 py-3">Qty</th><th class="px-4 py-3 text-right">Subtotal</th><th class="px-4 py-3"></th></tr></thead>
            <tbody>
            <template x-for="(l, i) in lines" :key="l.id">
                <tr>
                    <td class="px-4 py-3 font-semibold text-slate-800"><a :href="l.url" class="hover:underline" x-text="l.name"></a></td>
                    <td class="px-4 py-3"><input type="number" min="1" x-model.number="l.qty" class="input !w-20"></td>
                    <td class="px-4 py-3 text-right font-semibold" x-text="(l.price * l.qty).toFixed(2)"></td>
                    <td class="px-4 py-3 text-right"><button type="button" @click="lines.splice(i,1)" class="btn btn-sm btn-ghost text-red-500">&times;</button></td>
                </tr>
            </template>
            </tbody>
        </table></div>
    </div>
    <div class="card p-6 self-start">
        <h3 class="text-base font-semibold text-slate-800 mb-2">Quick order total</h3>
        <div class="text-3xl font-bold text-slate-900" x-text="'Rs ' + total().toFixed(2)"></div>
        <button type="button" @click="lines = []" class="btn btn-secondary mt-4 w-full">Clear</button>
        <p class="text-xs text-slate-400 mt-3">Quick order is a running tally for counter use. Click an item name to open it in the menu editor.</p>
    </div>
</div>

<script>
function quickScan() {
    return {
        code: '', lines: [], msg: '', ok: true,
        total() { return this.lines.reduce((s, l) => s + l.price * l.qty, 0); },
        async lookup() {
            const c = this.code.trim(); this.code = '';
            if (!c) return;
            try {
                const r = await fetch(@json(route('admin.barcodes.lookup')) + '?code=' + encodeURIComponent(c), {headers: {'Accept': 'application/json'}});
                const d = await r.json();
                if (!r.ok || !d.found) { this.ok = false; this.msg = d.message || 'Not found: ' + c; return; }
                const ex = this.lines.find(l => l.id === d.item.id);
                if (ex) ex.qty++; else this.lines.push({id: d.item.id, name: d.item.name, price: d.item.price, qty: 1, url: d.item.url});
                this.ok = true; this.msg = 'Added ' + d.item.name;
            } catch (e) { this.ok = false; this.msg = 'Lookup failed.'; }
            this.$refs.scan.focus();
        }
    };
}
</script>
@endsection
