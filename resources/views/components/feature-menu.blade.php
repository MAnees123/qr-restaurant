@foreach($items as $item)
    @php $active = request()->routeIs($item['pattern'] ?? $item['route']); @endphp
    @switch($theme)
        @case('classic')
            <a href="{{ route($item['route']) }}" class="classic-sb-item {{ $active ? 'active' : '' }}">{{ $item['label'] }}</a>
            @break
        @case('falcon')
            <a href="{{ route($item['route']) }}" class="theme-sb-item {{ $active ? 'active' : '' }}">{{ $item['label'] }}</a>
            @break
        @case('dark')
            <a href="{{ route($item['route']) }}" class="{{ $active ? 'dark-sb-active' : 'dark-sb-item' }}">{{ $item['label'] }}</a>
            @break
        @case('modern')
            <a href="{{ route($item['route']) }}" class="modern-sb-item {{ $active ? 'active' : '' }}">{{ $item['label'] }}</a>
            @break
        @default
            <a href="{{ route($item['route']) }}"
               class="block px-6 py-3 hover:bg-slate-700 {{ $active ? 'bg-slate-700 border-l-4 border-amber-500' : '' }}">{{ $item['label'] }}</a>
    @endswitch
@endforeach
