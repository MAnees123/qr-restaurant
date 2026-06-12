<!-- Waiter Call Notification Icon -->
<div x-data="globalNotifications()" x-init="initPolling()" class="relative mr-4 z-50">
    <!-- Bell Trigger -->
    <button @click="dropdownOpen = !dropdownOpen"
        class="relative p-2 rounded-xl transition-all duration-300 flex items-center justify-center"
        :class="calls.some(c => c.status === 'pending') ?
            'bg-amber-100 text-amber-600 animate-vibrate' : 'bg-slate-100 text-slate-400'">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
            </path>
        </svg>

        <template x-if="calls.filter(c => c.status === 'pending').length > 0">
            <span
                class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-black text-white shadow-sm ring-2 ring-white"
                x-text="calls.filter(c => c.status === 'pending').length"></span>
        </template>
    </button>

    <!-- Notifications Dropdown -->
    <div x-show="dropdownOpen" @click.away="dropdownOpen = false" style="display:none;"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        class="absolute right-0 mt-3 w-72 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden text-left" style="transform-origin: top right;">

        <div class="p-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Waiter Requests</h3>
            <span class="text-[10px] font-black px-2 py-0.5 rounded bg-amber-100 text-amber-700 uppercase"
                x-text="calls.length + ' Active'"></span>
        </div>

        <div class="max-h-[300px] overflow-y-auto">
            <template x-if="calls.length === 0">
                <div class="p-8 text-center">
                    <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                    </div>
                    <p class="text-xs text-slate-400 font-bold italic">Not any request yet</p>
                </div>
            </template>

            <template x-for="call in calls" :key="call.id">
                <div class="p-4 border-b border-slate-50 last:border-0 hover:bg-slate-50 transition flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-xs"
                            :class="call.status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'"
                            x-text="call.table.table_number"></div>
                        <div>
                            <p class="text-xs font-black text-slate-800"
                                x-text="call.status === 'pending' ? 'Assistance Needed' : 'Waiter on way'">
                            </p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase"
                                x-text="formatTime(call.created_at)"></p>
                        </div>
                    </div>
                    <div class="flex gap-1">
                        <template x-if="call.status === 'pending'">
                            <button @click="acceptCall(call.id)" title="Accept Call"
                                class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        </template>
                        <button @click="completeCall(call.id)" title="Mark as Completed"
                            class="p-2 text-emerald-500 hover:bg-emerald-50 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
