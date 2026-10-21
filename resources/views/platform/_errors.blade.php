@if($errors->any())
<div class="mb-4 rounded-2xl bg-red-50 border border-red-100 text-red-700 p-4 text-sm font-semibold">
    <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif
