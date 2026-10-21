{{-- Injected before </head> by App\Http\Middleware\Comms\InjectFeatureHead. Checks the features itself. --}}
@php
    $__branding = $restaurant->hasFeature('custom_branding') ? \App\Models\Comms\CommsSetting::read($restaurant->id, 'branding') : null;
    $__dark = $restaurant->hasFeature('dark_mode');
    $__p = $__branding['primary'] ?? null;
    $__s = $__branding['secondary'] ?? null;
@endphp
<meta data-comms-feature-head>
@if($__branding)
    @if(!empty($__branding['favicon']))
        <link rel="icon" href="{{ asset('storage/' . $__branding['favicon']) }}">
    @endif
    <style>
        :root { --brand-primary: {{ $__p }}; --brand-secondary: {{ $__s }}; }
        @if($mode === 'guest')
        .bg-\[\#e8890c\], .hover\:bg-\[\#e8890c\]:hover { background-color: var(--brand-primary) !important; }
        .bg-\[\#c0441a\] { background-color: var(--brand-secondary) !important; }
        .text-\[\#e8890c\] { color: var(--brand-primary) !important; }
        [style*="#e8890c"] { color: var(--brand-primary) !important; }
        .w-4.bg-\[\#e8890c\] { background-color: var(--brand-primary) !important; }
        @else
        .bg-amber-500, .bg-amber-600, .hover\:bg-amber-600:hover { background-color: var(--brand-primary) !important; }
        .text-amber-500, .text-amber-600 { color: var(--brand-primary) !important; }
        .bg-emerald-600, .hover\:bg-emerald-700:hover { background-color: var(--brand-secondary) !important; }
        @endif
    </style>
@endif
@if($__dark && $mode === 'admin' && $user)
    <style>
        html.comms-dark { filter: invert(1) hue-rotate(180deg); background: #fff; }
        html.comms-dark img, html.comms-dark video, html.comms-dark canvas, html.comms-dark iframe { filter: invert(1) hue-rotate(180deg); }
        #comms-dark-toggle { position: fixed; right: 14px; bottom: 14px; z-index: 9999; width: 40px; height: 40px; border-radius: 9999px;
            border: 1px solid #cbd5e1; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,.2); cursor: pointer; font-size: 18px; line-height: 1; }
        @media print { #comms-dark-toggle { display: none; } html.comms-dark { filter: none; } }
    </style>
    <script>
        (function () {
            var on = {{ $user->dark_mode ? 'true' : 'false' }};
            if (on) document.documentElement.classList.add('comms-dark');
            document.addEventListener('DOMContentLoaded', function () {
                var b = document.createElement('button');
                b.id = 'comms-dark-toggle'; b.type = 'button'; b.title = 'Toggle dark mode'; b.textContent = '☾';
                b.addEventListener('click', function () {
                    on = !on;
                    document.documentElement.classList.toggle('comms-dark', on);
                    var t = document.querySelector('meta[name="csrf-token"]');
                    fetch(@json(route('admin.dark-mode.toggle')), {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': t ? t.content : ''},
                        body: JSON.stringify({dark_mode: on})
                    });
                });
                document.body.appendChild(b);
            });
        })();
    </script>
@endif
@if($__dark && $mode === 'guest')
    <script>
        (function () {
            // Remember the guest's light/dark choice of the menu's own theme toggle.
            var KEY = 'comms_guest_dark';
            function shell() { return document.querySelector('.phone-shell'); }
            document.addEventListener('alpine:initialized', function () {
                try {
                    var saved = localStorage.getItem(KEY);
                    if (saved !== null && shell() && window.Alpine) Alpine.$data(shell()).darkMode = (saved === '1');
                } catch (e) {}
            });
            document.addEventListener('click', function (e) {
                if (!e.target.closest || !e.target.closest('.theme-toggle-btn')) return;
                setTimeout(function () {
                    try { localStorage.setItem(KEY, Alpine.$data(shell()).darkMode ? '1' : '0'); } catch (e) {}
                }, 50);
            }, true);
        })();
    </script>
@endif
