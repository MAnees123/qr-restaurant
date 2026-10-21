<form method="GET" class="flex flex-wrap items-end gap-3 mb-6">
    @foreach(($keep ?? []) as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
    <div><label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-1">From</label>
        <input type="date" name="from" value="{{ $from }}" class="rounded-xl border-slate-200 font-bold text-sm"></div>
    <div><label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-1">To</label>
        <input type="date" name="to" value="{{ $to }}" class="rounded-xl border-slate-200 font-bold text-sm"></div>
    <button class="bg-slate-800 hover:bg-slate-900 text-white font-bold py-2 px-5 rounded-xl text-sm">Apply</button>
</form>
