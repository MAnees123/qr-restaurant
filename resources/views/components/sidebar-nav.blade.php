@php $icon = fn ($name) => $icons[$name] ?? $icons['home']; @endphp
<nav class="sb-nav" data-sidebar-nav aria-label="Main navigation">
    @foreach($top as $item)
        <a href="{{ route($item['route']) }}" class="sb-link {{ request()->routeIs($item['pattern'] ?? $item['route']) ? 'active' : '' }}">
            <svg class="sb-icon" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $icon($item['icon'] ?? 'home') }}"/></svg>
            <span>{{ $item['label'] }}</span>
        </a>
    @endforeach

    @foreach($groups as $key => $group)
        <div class="sb-group {{ $group['active'] ? 'open has-active' : '' }}" data-group="{{ $key }}">
            <button type="button" class="sb-group-btn" aria-expanded="{{ $group['active'] ? 'true' : 'false' }}">
                <svg class="sb-icon" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $icon($group['icon']) }}"/></svg>
                <span class="sb-label">{{ $group['label'] }}</span>
                <span class="sb-count">{{ count($group['items']) }}</span>
                <svg class="sb-chevron" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="sb-children">
                <div class="sb-children-inner">
                    <div class="sb-sub">
                        @foreach($group['items'] as $item)
                            <a href="{{ route($item['route']) }}" class="sb-link {{ $item['active'] ? 'active' : '' }}">{{ $item['label'] }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</nav>

@once
<script>
(function () {
    var nav = document.querySelector('[data-sidebar-nav]');
    if (!nav) return;
    var scroller = nav.closest('aside, [data-sidebar-scroll]') || nav.parentElement;
    var KEY = 'sb-scroll';

    // Accordion: opening a category closes the others.
    nav.addEventListener('click', function (e) {
        var btn = e.target.closest('.sb-group-btn');
        if (!btn) return;
        var group = btn.parentElement, willOpen = !group.classList.contains('open');
        nav.querySelectorAll('.sb-group.open').forEach(function (g) {
            g.classList.remove('open');
            g.firstElementChild.setAttribute('aria-expanded', 'false');
        });
        if (willOpen) { group.classList.add('open'); btn.setAttribute('aria-expanded', 'true'); }
    });

    // Keep scroll position across page loads, then make sure the active link is visible.
    try { var saved = sessionStorage.getItem(KEY); if (saved !== null) scroller.scrollTop = +saved; } catch (e) {}
    var save = function () { try { sessionStorage.setItem(KEY, scroller.scrollTop); } catch (e) {} };
    nav.addEventListener('click', save);
    window.addEventListener('pagehide', save);

    var active = nav.querySelector('.sb-sub .sb-link.active, .sb-link.active');
    if (active) {
        // Wait for the open-animation so the measured position is final.
        setTimeout(function () {
            var a = active.getBoundingClientRect(), s = scroller.getBoundingClientRect();
            if (a.top < s.top + 8 || a.bottom > s.bottom - 8) {
                scroller.scrollTo({ top: scroller.scrollTop + (a.top - s.top) - s.height / 3, behavior: 'smooth' });
            }
        }, 280);
    }
})();
</script>
@endonce
