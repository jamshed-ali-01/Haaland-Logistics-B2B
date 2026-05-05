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
            <!-- GRP PLATFORM DASHBOARD (Tailored for Haaland Logistics) -->
            <div class="space-y-6 animate-fade-in-up" x-data="grpCalculator(@js($warehouses), @js($countries))">
                
                <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col md:flex-row justify-between items-center gap-4 shadow-sm border-t-4 border-t-[#1A5161]">
                    <div class="flex items-center gap-4">
                        <div class="bg-white p-2 rounded-xl border border-slate-100 shadow-sm">
                            <img src="{{ asset('assets/logo-new.png') }}" alt="Haaland Logistics" class="h-10 w-auto object-contain">
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-[#1A5161] font-outfit uppercase tracking-tight">GRP Platform for Household Goods</h2>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Global Consolidation & Quoting Engine</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Welcome, <span class="text-slate-900">{{ Auth::user()->name }}</span></p>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-[#1A5161] text-white text-[10px] font-bold uppercase tracking-widest rounded-lg hover:bg-slate-800 transition-all shadow-md">Log Out</button>
                        </form>
                    </div>
                </div>

                <!-- MAIN WORKSPACE -->
                <div class="grid lg:grid-cols-3 gap-6">
                    
                    <!-- LEFT: SHIPMENT DETAILS FORM -->
                    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-8 shadow-sm">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-8 border-b border-slate-50 pb-4">Shipment Details</h3>
                        
                        <div class="grid md:grid-cols-2 gap-8">
                            <!-- Volume & Unit -->
                            <div class="space-y-6">
                                <div>
                                    <div class="flex justify-between items-center mb-3">
                                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Volume</label>
                                        <div class="flex items-center gap-4">
                                            <label class="flex items-center gap-2 cursor-pointer group">
                                                <input type="radio" x-model="unit" value="CFT" class="w-4 h-4 text-[#1A5161] focus:ring-[#1A5161]">
                                                <span class="text-[10px] font-bold text-slate-400 group-hover:text-slate-600 transition-colors uppercase">CFT</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer group">
                                                <input type="radio" x-model="unit" value="CBM" class="w-4 h-4 text-[#1A5161] focus:ring-[#1A5161]">
                                                <span class="text-[10px] font-bold text-slate-400 group-hover:text-slate-600 transition-colors uppercase">CBM</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="relative">
                                        <input type="number" x-model="volume" class="input-premium w-full !bg-slate-50/50 !border-slate-200 !text-xl font-bold font-mono !py-4" placeholder="0.00">
                                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                            <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3">Drop-off Warehouse</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <template x-for="w in warehouses" :key="w.id">
                                            <label class="relative cursor-pointer">
                                                <input type="radio" x-model="originId" :value="w.id" class="sr-only peer">
                                                <div class="p-3 border border-slate-200 rounded-xl text-center peer-checked:border-[#1A5161] peer-checked:bg-[#1A5161]/5 transition-all">
                                                    <span class="block text-[10px] font-bold text-slate-400 peer-checked:text-[#1A5161] uppercase tracking-wider" x-text="w.name"></span>
                                                </div>
                                            </label>
                                        </template>
                                        <!-- Dallas Placeholder -->
                                        <div class="p-3 border border-slate-100 bg-slate-50 rounded-xl text-center opacity-60">
                                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider italic">Dallas, TX</span>
                                            <span class="block text-[8px] text-[#ED1C24] font-bold uppercase tracking-widest mt-1">Coming Soon</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Destination & Service -->
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3">Destination Region / Option</label>
                                    <div class="space-y-3">
                                        <select x-model="countryId" @change="updateRegions()" class="input-premium w-full !bg-slate-50/50">
                                            <option value="">Select Country</option>
                                            <template x-for="c in countries" :key="c.id">
                                                <option :value="c.id" x-text="c.name"></option>
                                            </template>
                                        </select>
                                        <div x-show="regions.length > 0">
                                            <select x-model="regionId" class="input-premium w-full !bg-slate-50/50">
                                                <option value="">Select Region / Logic</option>
                                                <template x-for="r in regions" :key="r.id">
                                                    <option :value="r.id" x-text="r.name"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3">Service Type</label>
                                    <div class="space-y-3">
                                        <label class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200 cursor-pointer hover:border-[#1A5161]/30 transition-all group">
                                            <input type="radio" x-model="serviceType" value="Warehouse to Door" class="w-4 h-4 text-[#1A5161] focus:ring-[#1A5161]" checked>
                                            <div>
                                                <p class="text-[10px] font-bold text-slate-900 uppercase tracking-wide">Warehouse to Door</p>
                                                <p class="text-[8px] text-slate-400 uppercase tracking-widest">Full consolidation & delivery</p>
                                            </div>
                                        </label>
                                        <label class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200 cursor-pointer hover:border-[#1A5161]/30 transition-all group">
                                            <input type="radio" x-model="serviceType" value="Warehouse to Port" class="w-4 h-4 text-[#1A5161] focus:ring-[#1A5161]">
                                            <div>
                                                <p class="text-[10px] font-bold text-slate-900 uppercase tracking-wide">Warehouse to Port</p>
                                                <p class="text-[8px] text-slate-400 uppercase tracking-widest">Client pickup at destination</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 pt-8 border-t border-slate-50">
                            <textarea x-model="notes" class="input-premium w-full !bg-slate-50/20 text-xs" rows="3" placeholder="Additional Notes: Vehicle, Pallet, Special Requirements..."></textarea>
                            <div class="flex flex-wrap gap-4 mt-6">
                                <button type="button" @click="calculate()" class="px-8 py-4 bg-[#1A5161] text-white font-bold rounded-xl shadow-lg hover:bg-slate-800 transition-all uppercase tracking-widest text-[10px]">Calculate Quote</button>
                                <button type="button" @click="resetForm()" class="px-8 py-4 text-slate-400 font-bold hover:text-slate-600 transition-all uppercase tracking-widest text-[10px]">Reset</button>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: PRICE BREAKDOWN -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm flex flex-col">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-8 border-b border-slate-50 pb-4">Estimated Price <span class="text-[10px] ml-1">(USD)</span></h3>
                        
                        <div class="flex-1 space-y-5">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-500">Origin Handling</span>
                                <span class="font-bold text-slate-900" x-text="calculation ? '$' + calculation.origin_handling_usd : '$0.00'">$0.00</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <div class="flex flex-col">
                                    <span class="text-slate-500">Ocean Freight</span>
                                    <span class="text-[9px] text-slate-400 font-mono" x-show="calculation" x-text="`(${calculation.billable_cft} CFT × $${(calculation.ocean_freight_usd / calculation.billable_cft).toFixed(2)})`"></span>
                                </div>
                                <span class="font-bold text-slate-900" x-text="calculation ? '$' + calculation.ocean_freight_usd : '$0.00'">$0.00</span>
                            </div>
                            <div class="flex justify-between items-center text-xs opacity-40">
                                <span class="text-slate-500">Destination Charges</span>
                                <span class="font-bold text-slate-900">$0.00</span>
                            </div>
                            <div class="flex justify-between items-center text-xs opacity-40">
                                <span class="text-slate-500">Documentation</span>
                                <span class="font-bold text-slate-900">$0.00</span>
                            </div>

                            <div class="mt-8 pt-6 border-t-2 border-slate-50 flex justify-between items-end">
                                <p class="text-xs font-bold text-slate-900 uppercase tracking-widest">Total</p>
                                <p class="text-3xl font-bold text-[#1A5161] font-outfit" x-text="calculation ? '$' + calculation.total_price.toLocaleString() : '$0.00'">$0.00</p>
                            </div>
                        </div>

                        <div class="mt-10 p-5 bg-slate-50 rounded-2xl border border-slate-100">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Next Departure:</p>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-bold text-slate-900" x-text="nextDeparture ? nextDeparture.departure_date : 'No Departure scheduled'">---</span>
                                <span class="px-2 py-0.5 bg-white border border-slate-200 rounded text-[8px] font-bold text-slate-400" x-text="nextDeparture ? nextDeparture.cutoff_date : '---'">---</span>
                            </div>
                            <p class="text-[9px] text-slate-400 uppercase tracking-widest" x-show="nextDeparture">Cutoff: <span class="text-[#ED1C24] font-bold" x-text="nextDeparture.cutoff_date">---</span></p>
                        </div>

                        <!-- Progress Section (Transparency Details) -->
                        <div class="mt-6 space-y-3 pt-6 border-t border-slate-50">
                            <div class="flex items-center justify-between text-[9px] font-bold uppercase tracking-widest text-slate-400">
                                <span>Billable CBM</span>
                                <span class="text-slate-900" x-text="calculation ? calculation.billable_cbm + ' CBM' : '--'"></span>
                            </div>
                            <div class="flex items-center justify-between text-[9px] font-bold uppercase tracking-widest text-slate-400">
                                <span>Exchange Rate (EUR/USD)</span>
                                <span class="text-slate-900" x-text="calculation ? calculation.fx_rate : '--'"></span>
                            </div>
                            <div class="flex items-center justify-between text-[9px] font-bold uppercase tracking-widest text-slate-400" x-show="calculation && calculation.applied_min">
                                <span class="text-[#ED1C24]">Min. Volume Applied</span>
                                <span class="text-[#ED1C24]">Yes</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BOTTOM: UPCOMING CONTAINERS -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mt-8">
                    <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-[0.2em]">Upcoming Containers</h3>
                        <a href="{{ route('quotes.index') }}" class="text-[10px] font-bold text-slate-400 uppercase hover:text-[#1A5161] transition-all no-underline">View All &rsaquo;</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Lane</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Departure</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Cutoff</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($stats['upcoming_departures'] as $dep)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <span class="w-2 h-2 rounded-full bg-[#1A5161]"></span>
                                                <span class="text-xs font-bold text-slate-900 uppercase tracking-wide">Miami &rsaquo; {{ $dep->vessel_name ?: 'Global Port' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-xs text-slate-600 font-medium">{{ $dep->departure_date->format('M d, Y') }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 bg-red-50 text-[#ED1C24] text-[10px] font-bold rounded uppercase tracking-tighter">{{ $dep->cutoff_date->format('M d, Y') }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button class="text-[#1A5161] hover:text-slate-900 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- FOOTER STRIP -->
                <div class="flex flex-wrap justify-center gap-8 py-8 opacity-60">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#1A5161]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="text-[10px] font-bold uppercase tracking-widest">Weekly Consolidations</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#1A5161]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="text-[10px] font-bold uppercase tracking-widest">Transparent Pricing</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#1A5161]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="text-[10px] font-bold uppercase tracking-widest">Live Departure Tracking</span>
                    </div>
                </div>
            </div>

            <!-- Dashboard Logic Script -->
            <script>
                function grpCalculator(warehouses, countries) {
                    return {
                        warehouses: warehouses,
                        countries: countries,
                        originId: '',
                        countryId: '',
                        regionId: '',
                        regions: [],
                        volume: 0,
                        unit: 'CFT',
                        serviceType: 'Warehouse to Door',
                        notes: '',
                        calculation: null,
                        nextDeparture: null,
                        loading: false,

                        init() {
                            if (this.warehouses.length > 0) this.originId = this.warehouses[0].id;
                            this.fetchNextDeparture();
                        },

                        updateRegions() {
                            const country = this.countries.find(c => c.id == this.countryId);
                            this.regions = country ? country.regions : [];
                            this.regionId = '';
                        },

                        async calculate() {
                            if (!this.originId || !this.countryId || this.volume <= 0) {
                                this.calculation = null;
                                return;
                            }

                            this.loading = true;
                            const params = new URLSearchParams({
                                origin_id: this.originId,
                                country_id: this.countryId,
                                region_id: this.regionId,
                                volume: this.volume,
                                volume_unit: this.unit,
                                service_type: this.serviceType
                            });

                            try {
                                const response = await fetch(`{{ route('quotes.calculate') }}?${params.toString()}`);
                                const data = await response.json();
                                if (data.success) {
                                    this.calculation = data;
                                } else {
                                    this.calculation = null;
                                }
                            } catch (e) {
                                console.error('Calculation Error:', e);
                            } finally {
                                this.loading = false;
                            }
                        },

                        fetchNextDeparture() {
                            // Simplified for demo - usually a separate API or passed via blade
                            @if(count($stats['upcoming_departures']) > 0)
                                const dep = @js($stats['upcoming_departures'][0]);
                                this.nextDeparture = {
                                    departure_date: new Date(dep.departure_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
                                    cutoff_date: new Date(dep.cutoff_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
                                };
                            @endif
                        },

                        async bookSpace() {
                            if (!this.calculation) return;
                            
                            // Redirect to official quote store
                            const formData = new FormData();
                            formData.append('_token', '{{ csrf_token() }}');
                            formData.append('origin_id', this.originId);
                            formData.append('country_id', this.countryId);
                            formData.append('region_id', this.regionId);
                            formData.append('volume', this.volume);
                            formData.append('volume_unit', this.unit);
                            formData.append('service_type', this.serviceType);
                            formData.append('notes', this.notes);

                            try {
                                const response = await fetch('{{ route("quotes.store") }}', {
                                    method: 'POST',
                                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                                    body: formData
                                });
                                const data = await response.json();
                                if (data.success) {
                                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Booking request submitted!', type: 'success' }}));
                                    setTimeout(() => window.location.href = data.redirect, 1500);
                                }
                            } catch (e) {
                                console.error('Booking Error:', e);
                            }
                        },

                        resetForm() {
                            this.volume = 0;
                            this.countryId = '';
                            this.regionId = '';
                            this.notes = '';
                            this.calculation = null;
                        }
                    }
                }
            </script>
        @endif
    </div>
</x-app-layout>
