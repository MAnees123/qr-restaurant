@if(session('error'))
    <div class="mb-6 bg-red-50 text-red-600 p-4 rounded-xl border border-red-200">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="mb-6 bg-red-50 text-red-600 p-4 rounded-xl border border-red-200">
        <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif
