@props(['title' => null, 'hover' => false, 'padding' => true])
<div {{ $attributes->merge(['class' => 'card'.($hover ? ' card-hover' : '')]) }}>
    @if($title || isset($actions))
        <div class="flex items-center justify-between gap-3 px-6 py-4 border-b border-slate-100">
            <h3 class="text-base font-semibold text-slate-800">{{ $title }}</h3>
            @isset($actions)<div class="flex items-center gap-2">{{ $actions }}</div>@endisset
        </div>
    @endif
    <div @class(['p-6' => $padding])>{{ $slot }}</div>
</div>
