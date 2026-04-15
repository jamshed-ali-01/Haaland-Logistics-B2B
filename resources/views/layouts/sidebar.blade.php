<div class="h-full flex flex-col lg:flex-row transition-colors duration-300">
    
    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed inset-y-0 left-0 z-50 w-72 h-full transition-transform transform lg:translate-x-0 lg:static lg:inset-0 bg-brand-900 flex flex-col flex-shrink-0 shadow-2xl lg:shadow-none">
        
        <!-- Sidebar Branding -->
        <div class="h-24 flex items-center px-8 border-b border-white/5 bg-slate-950">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 no-underline uppercase tracking-tight">
                <img src="{{ asset('assets/logo.png') }}" alt="Haaland Logistics" class="h-10 w-auto object-contain">
            </a>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-8 space-y-2 overflow-y-auto custom-scrollbar">
            @if(Auth::user()->role === 'client' && Auth::user()->status === 'pending')
                <div class="mb-8 p-4 bg-amber-500/10 border border-amber-500/20 rounded-2xl">
                    <div class="flex items-center gap-3 text-amber-500 mb-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <p class="text-[10px] font-bold uppercase tracking-widest leading-none">Wait for Approval</p>
                    </div>
                    <p class="text-[9px] text-slate-400 font-medium leading-relaxed font-outfit">Your account is being verified. Formal quotes and bookings are restricted until approval.</p>
                </div>
            @endif

            <!-- Global Dashboard -->
            <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="m3 12 2-2m0 0 7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11 2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6">
                {{ __('Overview') }}
            </x-sidebar-link>

            @if(Auth::user()->role === 'admin')
                <div class="pt-6 pb-2 px-4">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Management</p>
                </div>
                <x-sidebar-link :href="route('admin.users')" :active="request()->routeIs('admin.users')" icon="M12 4.354a4 4 0 1 1 0 5.292M15 21H3v-1a6 6 0 0 1 12 0v1zm0 0h6v-1a6 6 0 0 0-9-5.197M13 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0z">
                    {{ __('Clients') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.warehouses')" :active="request()->routeIs('admin.warehouses')" icon="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4">
                    {{ __('Warehouses') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.rates')" :active="request()->routeIs('admin.rates')" icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z">
                    {{ __('Rates') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.services')" :active="request()->routeIs('admin.services')" icon="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4">
                    {{ __('Services') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.service-types')" :active="request()->routeIs('admin.service-types')" icon="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                    {{ __('Types') }}
                </x-sidebar-link>
                
                <div class="pt-6 pb-2 px-4">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Operations</p>
                </div>
                <x-sidebar-link :href="route('admin.departures')" :active="request()->routeIs('admin.departures')" icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z">
                    {{ __('Departures') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.external-shipments')" :active="request()->routeIs('admin.external-shipments')" icon="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                    {{ __('Ops Tool') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.settings')" :active="request()->routeIs('admin.settings')" icon="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z">
                    {{ __('Global Settings') }}
                </x-sidebar-link>
            @else
                <div class="pt-6 pb-2 px-4">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Shipments</p>
                </div>
                <x-sidebar-link :href="Auth::user()->status === 'approved' ? route('quotes.index') : '#'" 
                                :active="request()->routeIs('quotes.index')" 
                                icon="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"
                                :disabled="Auth::user()->status !== 'approved'">
                    {{ __('Quotes') }}
                </x-sidebar-link>
                <x-sidebar-link :href="Auth::user()->status === 'approved' ? route('bookings.index') : '#'" 
                                :active="request()->routeIs('bookings.index')" 
                                icon="M20 13V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v7m16 0a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2m16 0l-8 4-8-4"
                                :disabled="Auth::user()->status !== 'approved'">
                    {{ __('Bookings') }}
                </x-sidebar-link>
            @endif
        </nav>

        <!-- User Logout -->
        <div class="px-6 py-6 border-t border-white/5">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 text-slate-400 hover:text-red-400 font-bold transition-all text-sm uppercase tracking-wide">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Content Area -->
    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
        <!-- Top Header (Fixed in shell) -->
        <header class="h-20 flex items-center justify-between px-8 bg-white border-b border-slate-100 shadow-sm z-40">
            <div class="flex items-center gap-4 lg:hidden">
                <button @click="sidebarOpen = !sidebarOpen" class="text-slate-600 hover:text-brand-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>

            <div class="hidden lg:flex items-center gap-3 text-slate-400 text-xs font-bold uppercase tracking-widest">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                System Status: <span class="text-emerald-500">Live</span>
            </div>

            <div class="flex items-center gap-6">
                <!-- Notifications -->
                <div class="relative cursor-pointer text-slate-400 hover:text-brand-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-accent-500 rounded-full border-2 border-white"></span>
                </div>

                <!-- Profile Link -->
                <a href="{{ route('profile.edit') }}" class="group flex items-center gap-3 py-2 px-3 rounded-xl hover:bg-slate-50 transition-all no-underline">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-bold text-slate-900 line-height-none">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-slate-400 uppercase tracking-widest">{{ Auth::user()->role }}</p>
                    </div>
                    <div class="w-10 h-10 bg-brand-700 rounded-full flex items-center justify-center text-white font-bold shadow-lg shadow-brand-700/20 group-hover:scale-105 transition-transform">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </a>
            </div>
        </header>

        <!-- Main Content Area (Scrollable) -->
        <main class="flex-1 p-8 lg:p-12 overflow-y-auto custom-scrollbar bg-[#f8fafb]">
            @isset($header)
                <div class="mb-10 animate-fade-in-up">
                    <h1 class="text-3xl font-bold font-outfit tracking-tight text-slate-900 mb-2">{{ $header }}</h1>
                    <div class="h-1 w-12 bg-brand-700 rounded-full"></div>
                </div>
            @endisset

            {{ $slot }}
        </main>
    </div>
</div>
