@props(['type' => 'neutral'])
@php $map = ['success' => 'badge-success', 'warning' => 'badge-warning', 'danger' => 'badge-danger', 'info' => 'badge-info']; @endphp
<span {{ $attributes->merge(['class' => 'badge '.($map[$type] ?? '')]) }}>{{ $slot }}</span>
