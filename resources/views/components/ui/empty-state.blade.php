@props(['title' => 'Nothing here yet', 'message' => null])
<div {{ $attributes->merge(['class' => 'text-center py-14 px-6']) }}>
    <div class="mx-auto w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mb-4">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4m16 0h-5l-1 2h-4l-1-2H4"/></svg>
    </div>
    <p class="font-semibold text-slate-700">{{ $title }}</p>
    @if($message)<p class="text-sm text-slate-500 mt-1">{{ $message }}</p>@endif
    <div class="mt-4">{{ $slot }}</div>
</div>
