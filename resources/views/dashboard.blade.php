<x-app-layout>
    <x-slot name="header">
        {{ Auth::user()->role === 'admin' ? 'Strategic Command Center' : 'My Logistics Dashboard' }}
    </x-slot>

    <div class="space-y-8 pb-12">
        @if(Auth::user()->role === 'admin')
            <!-- 1. HIGH LEVEL BUSINESS PULSE -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 animate-fade-in-up">
                <div class="premium-card !p-6 bg-gradient-to-br from-brand-800 to-brand-950 text-white border-none shadow-2xl shadow-brand-900/30">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-md">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                        </div>
                        <span class="px-3 py-1 bg-white/20 rounded-full text-[10px] font-bold tracking-widest uppercase">Estimated Revenue</span>
                    </div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/50 mb-1">Total Bookings Value</p>
                    <p class="text-4xl font-bold font-outfit mb-4 text-white">${{ number_format($stats['estimated_revenue'], 2) }}</p>
                    <div class="flex items-center gap-2 text-brand-200 text-xs">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        <span class="font-bold">Active Growth Phase</span>
                    </div>
                </div>

                <div class="premium-card !p-6 bg-white border-slate-200 shadow-sm">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-brand-700 shadow-inner">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <span class="px-3 py-1 bg-brand-700/10 text-brand-700 rounded-full text-[10px] font-bold tracking-widest uppercase">Cargo Volume</span>
                    </div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-1">Total Shipped Volume</p>
                    <p class="text-4xl font-bold text-slate-900 font-outfit mb-4">{{ number_format($stats['total_volume_cft'], 0) }} <span class="text-2xl text-slate-400">CFT</span></p>
                    <div class="flex items-center gap-4">
                        <div class="flex -space-x-2">
                            @foreach($stats['recent_quotes']->take(3) as $rq)
                                <div class="w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center text-[8px] font-bold">{{ substr($rq->user->name ?? 'G',0,1) }}</div>
                            @endforeach
                        </div>
                        <span class="text-xs text-slate-400 italic">Growing Client Base</span>
                    </div>
                </div>
            </div>

            <!-- 2. SYSTEM-WIDE MATRIX (The "Everything" Grid) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-6 animate-fade-in-up" style="animation-delay: 0.1s">
                <!-- User Core -->
                <div class="premium-card !p-5 hover:border-brand-500 transition-all">
                    <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-700"></span> Clients
                    </p>
                    <div class="flex items-end justify-between">
                        <p class="text-3xl font-bold text-slate-800 font-outfit">{{ $stats['total_clients'] }}</p>
                        <span class="text-[10px] text-amber-600 font-bold mb-1">{{ $stats['pending_approvals'] }} pending</span>
                    </div>
                </div>

                <!-- Logistics Core -->
                <div class="premium-card !p-5">
                    <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Warehouses
                    </p>
                    <p class="text-3xl font-bold text-slate-800 font-outfit">{{ $stats['total_warehouses'] }}</p>
                </div>

                <div class="premium-card !p-5">
                    <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span> Reach
                    </p>
                    <div class="flex items-end justify-between">
                        <p class="text-3xl font-bold text-slate-800 font-outfit">{{ $stats['total_countries'] }}</p>
                        <span class="text-[10px] text-slate-400 mb-1">Countries</span>
                    </div>
                </div>

                <!-- Global Logic -->
                <div class="premium-card !p-5">
                    <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-violet-500"></span> Rates
                    </p>
                    <p class="text-3xl font-bold text-slate-800 font-outfit">{{ $stats['total_rates'] }}</p>
                </div>

                <div class="premium-card !p-5">
                    <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Services
                    </p>
                    <div class="flex items-end justify-between">
                        <p class="text-3xl font-bold text-slate-800 font-outfit">{{ $stats['total_services'] }}</p>
                        <span class="text-[10px] text-slate-400 mb-1">{{ $stats['total_service_types'] }} Types</span>
                    </div>
                </div>

                <!-- Operational -->
                <div class="premium-card !p-5">
                    <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span> Sea Ops
                    </p>
                    <div class="flex items-end justify-between">
                        <p class="text-3xl font-bold text-slate-800 font-outfit">{{ $stats['active_departures'] }}</p>
                        <span class="text-[10px] text-slate-400 mb-1">Active</span>
                    </div>
                </div>

                <!-- Special Requests -->
                <div class="premium-card !p-5 bg-amber-500/5 border-amber-500/20">
                    <p class="text-[9px] font-bold uppercase tracking-widest text-amber-600 mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Special
                    </p>
                    <div class="flex items-end justify-between">
                        <p class="text-3xl font-bold text-amber-700 font-outfit">{{ $stats['special_requests_count'] }}</p>
                        <span class="text-[10px] text-amber-600 mb-1 font-bold">Needs Review</span>
                    </div>
                </div>
            </div>

            <!-- 3. DETAILED OPERATIONAL VIEW -->
            <div class="grid lg:grid-cols-3 gap-8 animate-fade-in-up" style="animation-delay: 0.2s">
                <!-- Activity Ticker -->
                <div class="lg:col-span-2 premium-card !p-0 overflow-hidden bg-white border-slate-200">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 font-outfit uppercase tracking-tight">Recent Activity Feed</h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Unified Stream</p>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex flex-col text-right">
                                <span class="text-[10px] font-bold text-brand-700">{{ $stats['total_quotes'] }}</span>
                                <span class="text-[8px] text-slate-400 uppercase font-bold">Quotes</span>
                            </div>
                            <div class="flex flex-col text-right border-l border-slate-200 pl-4">
                                <span class="text-[10px] font-bold text-emerald-600">{{ $stats['total_bookings'] }}</span>
                                <span class="text-[8px] text-slate-400 uppercase font-bold">Bookings</span>
                            </div>
                        </div>
                    </div>
                    <div class="divide-y divide-slate-100 max-h-[500px] overflow-y-auto custom-scrollbar">
                        <!-- Leads -->
                        @foreach($stats['recent_leads'] as $lead)
                            <div class="p-6 flex items-center justify-between bg-brand-50/30 hover:bg-slate-50 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-violet-500/10 flex items-center justify-center text-violet-600 border border-violet-500/20 shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 uppercase">New Guest Inquiry: {{ $lead->email }}</p>
                                        <p class="text-[10px] text-slate-500 uppercase tracking-tight">Requested Volume: {{ number_format($lead->volume_cft ?? 0, 1) }} CFT</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-0.5 bg-violet-100 rounded text-[9px] font-bold text-violet-600 uppercase">Hot Lead</span>
                                    <p class="text-[10px] text-slate-400 tracking-tighter mt-1">{{ $lead->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                        <!-- Bookings -->
                        @foreach($stats['recent_bookings'] as $booking)
                            <div class="p-6 flex items-center justify-between hover:bg-slate-50 transition-colors {{ $booking->is_special_request ? 'bg-amber-50/50' : '' }}">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl {{ $booking->is_special_request ? 'bg-amber-500/10 text-amber-600 border-amber-500/20' : 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20' }} flex items-center justify-center shadow-sm">
                                        @if($booking->is_special_request)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900">New Booking: #{{ $booking->booking_number }}</p>
                                        <p class="text-[10px] text-slate-500 uppercase tracking-tight">Confirmed by {{ $booking->user->name ?? 'User' }} • {{ number_format($booking->quote->volume_cft ?? 0, 0) }} CFT</p>
                                    </div>
                                </div>
                                <div class="text-right flex flex-col items-end gap-2">
                                    <div class="flex items-center gap-2">
                                        <p class="text-xs font-bold text-emerald-600">${{ number_format($booking->quote->total_price ?? 0, 2) }}</p>
                                        @if($booking->status === 'pending')
                                            <form action="{{ route('admin.bookings.toggle-status', $booking) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white p-1 rounded transition-colors" title="Move to Scheduled">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    <p class="text-[10px] text-slate-400 tracking-tighter">{{ $booking->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                        <!-- Quotes -->
                        @foreach($stats['recent_quotes'] as $quote)
                            <div class="p-6 flex items-center justify-between hover:bg-slate-50 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-brand-700/5 flex items-center justify-center text-brand-700 border border-brand-700/10">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900">Quote Query: {{ $quote->country->name }}</p>
                                        <p class="text-[10px] text-slate-500 uppercase tracking-tight">Requested by {{ $quote->user->name ?? 'Guest' }} • ${{ number_format($quote->total_price, 2) }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-0.5 bg-slate-100 rounded text-[9px] font-bold text-slate-500 uppercase">Estimated</span>
                                    <p class="text-[10px] text-slate-400 tracking-tighter mt-1">{{ $quote->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Next Vessels -->
                <div class="premium-card flex flex-col bg-slate-900 text-white border-none shadow-2xl">
                    <h3 class="text-xl font-bold mb-8 font-outfit uppercase tracking-tight italic">Operations Strip</h3>
                    <div class="space-y-8 flex-1">
                        @forelse($stats['upcoming_departures'] as $dep)
                            <div class="space-y-4">
                                <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-[0.2em] px-1">
                                    <span class="text-slate-200">{{ $dep->origin_code }} → {{ $dep->destination_name }}</span>
                                    <span class="text-emerald-400">Next Up</span>
                                </div>
                                <div class="bg-white/10 p-4 rounded-2xl border border-white/20">
                                    <p class="text-sm font-bold text-white mb-1">{{ $dep->vessel_name ?: 'Vessel TBA' }}</p>
                                    <div class="flex justify-between text-[10px] text-white/60">
                                        <span>Cutoff: {{ $dep->cutoff_date->format('M d') }}</span>
                                        <span class="text-white font-bold">Voyage #{{ $dep->voyage_number }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-500 italic text-xs">No active voyages found.</p>
                        @endforelse
                    </div>
                    <div class="mt-8 pt-6 border-t border-white/5">
                        <a href="{{ route('admin.departures') }}" class="btn-primary w-full text-center py-2.5 bg-white text-brand-900 hover:bg-slate-200 no-underline inline-block rounded-xl font-bold uppercase tracking-widest text-[10px]">Full Schedule</a>
                    </div>
                </div>
            </div>

        @else
            <!-- CLIENT DASHBOARD (Tailored Stats) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-fade-in-up">
                <div class="premium-card">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">My Quotes</p>
                    <p class="text-4xl font-bold text-slate-900 font-outfit">{{ $stats['my_quotes_count'] }}</p>
                </div>
                <div class="premium-card">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">My Bookings</p>
                    <p class="text-4xl font-bold text-brand-700 font-outfit">{{ $stats['my_bookings_count'] }}</p>
                </div>
                <div class="premium-card">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Freight Volume</p>
                    <p class="text-4xl font-bold text-slate-900 font-outfit">{{ number_format($stats['my_volume_cft'], 0) }} <span class="text-sm">CFT</span></p>
                </div>
                <div class="premium-card">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Total Logistics Spend</p>
                    <p class="text-4xl font-bold text-slate-900 font-outfit">${{ number_format($stats['my_total_spending'], 2) }}</p>
                </div>
            </div>

            <!-- Recent Items for Client -->
            <div class="grid lg:grid-cols-2 gap-8 animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="premium-card bg-white">
                    <h3 class="text-lg font-bold mb-6 font-outfit border-b pb-4">Recent Bookings</h3>
                    <div class="space-y-4">
                        @forelse($stats['recent_my_bookings'] as $b)
                            <div class="flex justify-between items-center p-4 bg-slate-50 rounded-xl">
                                <div>
                                    <p class="text-sm font-bold">#{{ $b->booking_number }}</p>
                                    <p class="text-[10px] uppercase text-slate-400">{{ $b->created_at->format('M d, Y') }}</p>
                                </div>
                                <p class="text-brand-700 font-bold">${{ number_format($b->quote->total_price ?? 0, 2) }}</p>
                            </div>
                        @empty
                            <p class="text-slate-400 italic text-sm text-center py-6">No bookings yet.</p>
                        @endforelse
                    </div>
                </div>
                <div class="premium-card bg-brand-700 text-white flex flex-col justify-center items-center text-center p-12">
                    <h3 class="text-2xl font-bold font-outfit mb-4 italic uppercase">Need a new rate?</h3>
                    <p class="text-brand-200 text-sm mb-8">Get instant pricing and book your space in seconds.</p>
                    <a href="{{ route('quotes.create') }}" class="px-8 py-4 bg-white text-brand-900 font-bold rounded-xl shadow-xl hover:bg-slate-100 transition-all uppercase tracking-widest text-xs">Start New Quote</a>
                </div>
            </div>

            <!-- Client: Next Vessels Visibility -->
            <div class="premium-card flex flex-col bg-slate-900 text-white border-none shadow-2xl animate-fade-in-up md:col-span-2 lg:col-span-1 mt-8" style="animation-delay: 0.2s">
                <h3 class="text-xl font-bold mb-8 font-outfit uppercase tracking-tight italic">Operations Strip</h3>
                <div class="space-y-8 flex-1">
                    @forelse($stats['upcoming_departures'] as $dep)
                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-[0.2em] px-1">
                                <span class="text-slate-200">Terminal Cutoff</span>
                                <span class="text-emerald-400">Next Up</span>
                            </div>
                            <div class="bg-white/10 p-4 rounded-2xl border border-white/20">
                                <p class="text-sm font-bold text-white mb-1">{{ $dep->vessel_name ?: 'Vessel TBA' }}</p>
                                <div class="flex justify-between items-center text-[10px] text-white/60">
                                    <span class="font-bold text-white">{{ $dep->cutoff_date->format('M d, Y') }}</span>
                                    <span>Voyage #{{ $dep->voyage_number }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500 italic text-xs text-center py-6">No active voyages found.</p>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
