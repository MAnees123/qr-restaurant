{{-- Include inside <head> of guest menu / admin layouts: @include('partials.pwa-head', ['restaurant' => $restaurant ?? null])
     Renders nothing unless the restaurant has the mobile_app feature. --}}
@php
    $__pwaRestaurant = (isset($restaurant) && $restaurant instanceof \App\Models\Restaurant) ? $restaurant : auth()->user()?->restaurant;
    $__pwaOn = $__pwaRestaurant && $__pwaRestaurant->hasFeature('mobile_app');
    $__pwaSetting = $__pwaOn ? \App\Models\Platform\PwaSetting::forRestaurant($__pwaRestaurant->id) : null;
@endphp
@if($__pwaOn)
<link rel="manifest" href="{{ route('pwa.manifest', $__pwaRestaurant->id) }}">
<meta name="theme-color" content="{{ $__pwaSetting->theme_color }}">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="apple-touch-icon" href="{{ route('pwa.icon', $__pwaRestaurant->id) }}">
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('{{ route('pwa.sw', $__pwaRestaurant->id, false) }}', { scope: '/' }).catch(function () {});
    });
}
</script>
@endif
