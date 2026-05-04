<nav x-data="{ open: false }" class="glass border-b border-white/5 sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 no-underline group">
                        <div class="w-10 h-10 bg-brand-900 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg border-b-4 border-brand-600 group-hover:scale-105 transition-transform">
                            H
                        </div>
                        <div class="flex flex-col">
                            <span class="text-white font-black text-xl tracking-tighter leading-none group-hover:text-brand-400 transition-colors">HAALAND</span>
                            <span class="text-brand-600 font-bold text-[10px] tracking-[0.3em] leading-none mt-1">LOGISTICS</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-slate-300 hover:text-white transition-colors">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(Auth::user()->role === 'admin')
                        <!-- Admin Specific Links -->
                        <x-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users')" class="text-slate-300 hover:text-white transition-colors">
                            {{ __('Clients') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.quotes.index')" :active="request()->routeIs('admin.quotes.index')" class="text-slate-300 hover:text-white transition-colors">
                            {{ __('Quotes') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.rates')" :active="request()->routeIs('admin.rates')" class="text-slate-300 hover:text-white transition-colors">
                            {{ __('Rates') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.departures')" :active="request()->routeIs('admin.departures')" class="text-slate-300 hover:text-white transition-colors">
                            {{ __('Departures') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.bookings.index')" :active="request()->routeIs('admin.bookings.index')" class="text-slate-300 hover:text-white transition-colors">
                            {{ __('Bookings') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.services')" :active="request()->routeIs('admin.services')" class="text-slate-300 hover:text-white transition-colors">
                            {{ __('Services') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.service-types')" :active="request()->routeIs('admin.service-types')" class="text-slate-300 hover:text-white transition-colors">
                            {{ __('Types') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.external-shipments')" :active="request()->routeIs('admin.external-shipments')" class="text-slate-300 hover:text-white transition-colors">
                            {{ __('Ops Tool') }}
                        </x-nav-link>
                    @else
                        <!-- Client Specific Links -->
                        <x-nav-link :href="route('quotes.index')" :active="request()->routeIs('quotes.index')" class="text-slate-300 hover:text-white transition-colors">
                            {{ __('My Quotes') }}
                        </x-nav-link>
                        <x-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.index')" class="text-slate-300 hover:text-white transition-colors">
                            {{ __('My Bookings') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Notifications -->
            <div class="hidden sm:flex sm:items-center sm:ms-4" x-data="{ 
                notifications: [], 
                unreadCount: 0, 
                showNotifications: false,
                init() {
                    this.fetchNotifications();
                    // Polling as fallback for real-time notifications
                    setInterval(() => this.fetchNotifications(), 10000); // Check every 10 seconds

                    if (window.Echo) {
                        window.Echo.private(`App.Models.User.{{ Auth::id() }}`)
                            .notification((notification) => {
                                this.fetchNotifications(); // Refresh full state on push
                            });
                    }
                },
                playNotificationSound() {
                    const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
                    audio.play().catch(e => console.log('Audio play failed:', e));
                },
                fetchNotifications() {
                    fetch('{{ route('notifications.fetch') }}')
                        .then(res => res.json())
                        .then(data => {
                            const oldUnread = this.unreadCount;
                            this.notifications = data.notifications;
                            this.unreadCount = data.unread_count;
                            
                            // Show toast and play sound if new notification arrived
                            if (this.unreadCount > oldUnread && oldUnread !== 0) {
                                this.playNotificationSound();
                                window.dispatchEvent(new CustomEvent('toast', { 
                                    detail: { message: 'New notification received!', type: 'info' }
                                }));
                            }
                        });
                },
                markAsRead(id) {
                    fetch(`/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(() => {
                        this.notifications = this.notifications.map(n => n.id === id ? { ...n, read_at: new Date() } : n);
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    });
                }
            }">
                <div class="relative">
                    <button @click.stop="console.log('Bell clicked'); showNotifications = !showNotifications; if(showNotifications) fetchNotifications()" 
                            class="p-2 text-slate-600 hover:text-brand-700 bg-white border border-slate-200 hover:border-brand-500/50 rounded-xl transition-all duration-200 focus:outline-none shadow-sm relative">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <template x-if="unreadCount > 0">
                            <span class="absolute top-1 right-1 flex h-4 w-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-500 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-4 w-4 bg-brand-600 text-[10px] items-center justify-center font-bold text-white shadow-lg" x-text="unreadCount"></span>
                            </span>
                        </template>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="showNotifications" 
                         x-cloak
                         @click.away="showNotifications = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-50">
                        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Notifications</h3>
                            <button @click="fetchNotifications()" class="text-xs text-brand-700 hover:text-brand-600 font-bold uppercase">Refresh</button>
                        </div>
                        <div class="max-h-96 overflow-y-auto custom-scrollbar">
                            <template x-for="notification in notifications" :key="notification.id">
                                <div class="p-4 border-b border-white/5 hover:bg-white/5 transition-colors cursor-pointer relative"
                                     :class="notification.read_at ? 'opacity-60' : 'bg-brand-600/5'"
                                     @click="if(!notification.read_at) markAsRead(notification.id); window.location.href = notification.data.link">
                                    <div class="flex gap-3">
                                        <div class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center"
                                             :class="{
                                                 'bg-brand-600/20 text-brand-400': notification.data.type === 'info',
                                                 'bg-green-500/20 text-green-400': notification.data.type === 'success',
                                                 'bg-brand-100 text-brand-600': notification.data.type === 'info',
                                                 'bg-green-100 text-green-600': notification.data.type === 'success',
                                                 'bg-red-100 text-red-600': notification.data.type === 'error'
                                             }">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs font-bold text-slate-900" x-text="notification.data.title"></p>
                                            <p class="text-[11px] text-slate-500 mt-0.5" x-text="notification.data.message"></p>
                                            <p class="text-[10px] text-slate-400 mt-2 font-medium" x-text="notification.created_at"></p>
                                        </div>
                                        <template x-if="!notification.read_at">
                                            <div class="w-2 h-2 rounded-full bg-brand-500 mt-1"></div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                            <template x-if="notifications.length === 0">
                                <div class="p-8 text-center">
                                    <p class="text-sm text-slate-500 italic">No notifications yet</p>
                                </div>
                            </template>
                        </div>
                        <div class="p-3 bg-white/5 border-t border-white/5 text-center">
                            <a href="#" class="text-[10px] font-bold text-slate-400 hover:text-white uppercase tracking-widest transition-colors">View All History</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-white/10 text-sm leading-4 font-medium rounded-xl text-slate-300 bg-white/5 hover:text-white hover:bg-white/10 focus:outline-none transition ease-in-out duration-150 backdrop-blur-md">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <span class="px-1.5 py-0.5 rounded-md bg-brand-600/20 text-[10px] text-brand-400 font-bold uppercase ml-2 border border-brand-500/20">
                                    {{ Auth::user()->role }}
                                </span>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="hover:bg-brand-600/10">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                                    class="hover:bg-red-500/10 text-red-400">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (Modified for glass theme) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 focus:outline-none transition duration-150">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
