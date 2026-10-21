@if($errors->any())
<div class="mb-6 bg-red-50 border border-red-100 text-red-700 rounded-2xl p-4 text-sm font-bold">
    <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif
