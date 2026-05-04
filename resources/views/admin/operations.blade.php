<x-app-layout>
    <x-slot name="header">
        {{ __('Operations Tool') }}
    </x-slot>

    <div class="space-y-10 animate-fade-in-up">
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div>
                <h2 class="text-2xl font-black text-slate-900 font-outfit uppercase tracking-tight">Vessel Consolidation</h2>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Live grouping of shipments by departure</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.external-shipments') }}" class="btn-primary flex items-center gap-2 !bg-slate-900 !shadow-none hover:!bg-slate-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Manual Entry
                </a>
                <div class="text-right pl-4 border-l border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Active Vessel Count</p>
                    <p class="text-xl font-bold text-slate-900">{{ $departures->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-brand-700 text-white flex items-center justify-center shadow-lg shadow-brand-700/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                </div>
            </div>
        </div>

        @forelse($departures as $vessel)
            <div class="space-y-4">
                <!-- Vessel Header -->
                <div class="flex items-center gap-4 bg-slate-900 text-white p-6 rounded-t-2xl shadow-xl">
                    <div class="w-14 h-14 rounded-2xl bg-brand-700 flex items-center justify-center text-white font-black text-xl shadow-lg border-b-4 border-brand-800">
                        {{ substr($vessel->vessel_name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <h3 class="text-xl font-black font-outfit uppercase tracking-tight">{{ $vessel->vessel_name }}</h3>
                            <span class="px-2 py-0.5 rounded bg-brand-700 text-[10px] font-bold uppercase tracking-widest">VOY: #{{ $vessel->voyage_number }}</span>
                        </div>
                        <div class="flex items-center gap-6 mt-1">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ETD: <span class="text-white">{{ $vessel->departure_date->format('M d, Y') }}</span></p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ETA: <span class="text-white">{{ $vessel->arrival_date->format('M d, Y') }}</span></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Utilization</p>
                        <div class="flex items-center gap-3">
                            <div class="w-32 bg-white/10 h-2 rounded-full overflow-hidden border border-white/5">
                                <div class="bg-brand-500 h-full" style="width: {{ $vessel->utilization_percentage }}%"></div>
                            </div>
                            <span class="text-sm font-black font-outfit">{{ $vessel->utilization_percentage }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Bookings Table -->
                <div class="premium-card !p-0 !rounded-t-none border-t-0 shadow-lg">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-400 text-[9px] uppercase tracking-widest font-bold border-b border-slate-100">
                                    <th class="px-6 py-3">Booking Ref</th>
                                    <th class="px-6 py-3">Client / Company</th>
                                    <th class="px-6 py-3">Volume</th>
                                    <th class="px-6 py-3">Inquiry Reference</th>
                                    <th class="px-6 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($vessel->bookings as $booking)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="text-xs font-bold text-slate-900">{{ $booking->booking_number }}</span>
                                            @if($booking->external_reference)
                                                <p class="text-[9px] text-brand-600 font-bold uppercase tracking-tight">EXT: {{ $booking->external_reference }}</p>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-xs font-bold text-slate-700">{{ $booking->user->name }}</p>
                                            <p class="text-[9px] text-slate-400 uppercase font-medium">{{ $booking->user->company_name ?? 'Individual' }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-xs font-bold text-slate-900">{{ number_format($booking->external_volume_cft ?? ($booking->quote->volume_cft ?? 0), 2) }} CFT</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($booking->quote)
                                                <a href="{{ route('admin.quotes.show', $booking->quote) }}" class="text-[10px] font-bold text-brand-700 uppercase hover:underline">
                                                    {{ $booking->quote->reference_number }}
                                                </a>
                                            @else
                                                <span class="text-[10px] text-slate-400 uppercase italic">Manual Entry</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[9px] font-bold uppercase border border-slate-200">
                                                {{ $booking->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-slate-400 text-xs italic">
                                            No cargo consolidated for this vessel yet.
                                        </td>
                                    </tr>
                                @forelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="premium-card p-20 text-center">
                <svg class="w-16 h-16 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4"/></svg>
                <h3 class="text-xl font-bold text-slate-400">No active vessels in operations</h3>
                <p class="text-sm text-slate-400 mt-2">Publish a vessel from the Departure Schedule to see it here.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
