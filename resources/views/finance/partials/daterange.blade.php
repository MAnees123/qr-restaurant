<form method="GET" class="card flex flex-wrap items-end gap-4 p-4 mb-6">
    @foreach(($keep ?? []) as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
    <div><label class="block text-sm font-medium text-slate-700 mb-1">From</label>
        <input type="date" name="from" value="{{ $from }}" class="input"></div>
    <div><label class="block text-sm font-medium text-slate-700 mb-1">To</label>
        <input type="date" name="to" value="{{ $to }}" class="input"></div>
    <button class="btn btn-primary">Apply</button>
</form>
