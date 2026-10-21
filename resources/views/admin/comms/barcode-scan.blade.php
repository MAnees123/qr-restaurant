@extends('layouts.admin')

@section('title', 'Barcode Scan')
@section('header', 'Scan / Quick Order')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="quickScan()">
    <div class="lg:col-span-2 bg-white rounded-[2rem] shadow-sm border p-8">
        <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Scan or type a barcode, then press Enter</label>
        <input type="text" x-model="code" @keydown.enter.prevent="lookup()" x-ref="scan" autofocus autocomplete="off"
               class="w-full rounded-xl border-slate-200 font-mono text-lg" placeholder="USB scanners type here automatically">
        <p class="text-sm mt-2" :class="ok ? 'text-emerald-600' : 'text-red-600'" x-text="msg"></p>

        <table class="w-full mt-6 text-left">
            <thead><tr class="text-xs uppercase text-slate-400"><th class="py-2">Item</th><th>Qty</th><th class="text-right">Subtotal</th><th></th></tr></thead>
            <tbody>
            <template x-for="(l, i) in lines" :key="l.id">
                <tr class="border-t">
                    <td class="py-2 font-bold"><a :href="l.url" class="hover:underline" x-text="l.name"></a></td>
                    <td><input type="number" min="1" x-model.number="l.qty" class="w-20 rounded-lg border-slate-200"></td>
                    <td class="text-right" x-text="(l.price * l.qty).toFixed(2)"></td>
                    <td class="text-right"><button type="button" @click="lines.splice(i,1)" class="text-red-500 font-black">&times;</button></td>
                </tr>
            </template>
            </tbody>
        </table>
    </div>
    <div class="bg-white rounded-[2rem] shadow-sm border p-6">
        <h3 class="font-black text-slate-700 uppercase tracking-widest text-sm mb-2">Quick order total</h3>
        <div class="text-3xl font-black" x-text="'Rs ' + total().toFixed(2)"></div>
        <button type="button" @click="lines = []" class="mt-4 w-full bg-slate-100 font-bold py-2 rounded-xl">Clear</button>
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
