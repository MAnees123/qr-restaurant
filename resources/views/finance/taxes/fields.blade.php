@php $in = 'w-full rounded-xl border-slate-200 font-bold'; $lb = 'block text-xs font-black text-slate-700 uppercase tracking-widest mb-1'; @endphp
<div class="space-y-4 mb-4">
    <div><label class="{{ $lb }}">Name</label><input name="name" required value="{{ old('name', $rate->name) }}" placeholder="e.g. GST" class="{{ $in }}"></div>
    <div><label class="{{ $lb }}">Rate (%)</label><input type="number" step="0.001" min="0" max="100" name="rate" required value="{{ old('rate', $rate->rate) }}" class="{{ $in }}"></div>
    <div><label class="{{ $lb }}">Type</label><select name="type" class="{{ $in }}"><option value="exclusive" @selected(old('type', $rate->type) === 'exclusive')>Exclusive (added on top)</option><option value="inclusive" @selected(old('type', $rate->type) === 'inclusive')>Inclusive (included in price)</option></select></div>
    <label class="flex items-center gap-2 font-bold text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $rate->is_active ?? true)) class="rounded"> Active</label>
    <label class="flex items-center gap-2 font-bold text-sm"><input type="checkbox" name="is_default" value="1" @checked(old('is_default', $rate->is_default)) class="rounded"> Default for invoices</label>
</div>
