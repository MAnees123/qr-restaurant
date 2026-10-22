@if(session('error'))
    <div class="mb-6 bg-red-50 text-red-700 px-4 py-3 rounded-xl border border-red-200 text-sm">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="mb-6 bg-red-50 text-red-700 px-4 py-3 rounded-xl border border-red-200 text-sm">
        <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif
