@props(['label', 'value', 'hint' => null, 'tone' => 'brand'])
@php $tones = ['brand' => 'bg-indigo-50 text-indigo-600', 'success' => 'bg-emerald-50 text-emerald-600', 'warning' => 'bg-amber-50 text-amber-600', 'danger' => 'bg-red-50 text-red-600']; @endphp
<div {{ $attributes->merge(['class' => 'card card-hover p-5 flex items-start gap-4']) }}>
    @isset($icon)<div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 {{ $tones[$tone] ?? $tones['brand'] }}">{{ $icon }}</div>@endisset
    <div class="min-w-0">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $label }}</p>
        <p class="mt-1 text-2xl font-bold text-slate-800 truncate">{{ $value }}</p>
        @if($hint)<p class="mt-0.5 text-xs text-slate-500">{{ $hint }}</p>@endif
    </div>
</div>
