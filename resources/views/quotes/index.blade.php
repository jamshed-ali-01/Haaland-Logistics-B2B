<x-app-layout>
    <x-slot name="header">
        {{ __('Quote History') }}
    </x-slot>

    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex justify-between items-center animate-fade-in-up">
            <h3 class="text-xl font-bold text-white font-outfit">Your Shipments</h3>
            <a href="{{ route('quotes.create') }}" class="btn-primary flex items-center gap-2 no-underline">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                New Quote
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="glass border border-emerald-500/20 p-4 rounded-2xl text-emerald-400 text-sm animate-fade-in-up">
                {{ session('success') }}
            </div>
        @endif

        <!-- Quotes Grid/Table -->
        <div class="premium-card !p-0 overflow-hidden animate-fade-in-up" style="animation-delay: 0.1s">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/2 text-slate-400 text-[10px] uppercase tracking-widest font-bold">
                            <th class="px-6 py-4">Quote ID</th>
                            <th class="px-6 py-4">Route</th>
                            <th class="px-6 py-4">Volume (Taxable)</th>
                            <th class="px-6 py-4">Est. Total</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($quotes as $quote)
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-slate-900 uppercase">{{ $quote->reference_number }}</span>
                                    <p class="text-[10px] text-slate-500 uppercase font-medium">{{ $quote->created_at->format('M d, Y') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-brand-700">{{ $quote->origin->code }}</span>
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        <span class="text-xs font-bold text-slate-900">{{ $quote->country->name }}</span>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-bold">{{ $quote->service_type }} Service</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-slate-700">{{ number_format($quote->billable_volume_cft, 2) }} CFT</span>
                                    @if($quote->volume_cft < 100)
                                        <p class="text-[9px] text-brand-600 font-bold uppercase tracking-tighter mt-1">(Min Volume Applied)</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-lg font-bold text-emerald-400 font-outfit">${{ number_format($quote->total_price, 2) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($quote->status === 'active')
                                        <span class="px-2 py-1 rounded bg-brand-500/10 text-brand-500 text-[10px] font-bold uppercase border border-brand-500/20">Valid</span>
                                    @elseif($quote->status === 'booked')
                                        <span class="px-2 py-1 rounded bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase border border-emerald-500/20">Booked</span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-slate-500/10 text-slate-500 text-[10px] font-bold uppercase">{{ $quote->status }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($quote->status === 'active')
                                        <a href="{{ route('bookings.create', $quote) }}" class="btn-primary no-underline inline-block text-[10px] font-bold uppercase px-3 py-2 rounded-lg transition-all shadow-lg shadow-brand-600/20">
                                            Book Now
                                        </a>
                                    @else
                                        <button class="bg-white/5 text-slate-500 text-[10px] font-bold uppercase px-3 py-2 rounded-lg cursor-not-allowed">
                                            {{ ucfirst($quote->status) }}
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="max-w-xs mx-auto">
                                        <svg class="w-12 h-12 text-slate-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <p class="text-slate-500 text-sm">No quotes found. Start by creating a new shipment request.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($quotes->hasPages())
                <div class="px-6 py-4 border-t border-white/5">
                    {{ $quotes->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
