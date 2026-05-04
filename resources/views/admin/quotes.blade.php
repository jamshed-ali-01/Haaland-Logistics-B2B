<x-app-layout>
    <x-slot name="header">
        {{ __('Quote Inquiries') }}
    </x-slot>

    <div class="space-y-6" x-data="{ showAcceptModal: false, acceptUrl: '', quoteRef: '' }" x-on:open-modal.window="if($event.detail == 'confirm-booking') showAcceptModal = true" x-on:close-modal.window="if($event.detail == 'confirm-booking') showAcceptModal = false">
        <!-- Quotes Table Card -->
        <div class="premium-card !p-0 overflow-hidden animate-fade-in-up">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-900 font-outfit uppercase tracking-widest">Client Quote Requests</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-brand-900 text-white text-[10px] uppercase tracking-widest font-bold">
                            <th class="px-6 py-4">Quote Details</th>
                            <th class="px-6 py-4">Client</th>
                            <th class="px-6 py-4">Route</th>
                            <th class="px-6 py-4 text-center">Volume</th>
                            <th class="px-6 py-4 text-center">Price</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 italic">
                        @foreach($quotes as $quote)
                            <tr class="hover:bg-slate-50 transition-colors group cursor-pointer" onclick="window.location='{{ route('admin.quotes.show', $quote) }}'">
                                <td class="px-6 py-5">
                                    <p class="text-sm font-bold text-slate-900">{{ $quote->reference_number }}</p>
                                    <p class="text-[10px] text-slate-400 uppercase font-medium">{{ $quote->created_at->format('M d, Y H:i') }}</p>
                                </td>
                                <td class="px-6 py-5">
                                    <p class="text-sm font-bold text-slate-700">{{ $quote->user->name }}</p>
                                    <p class="text-[10px] text-slate-400 uppercase font-medium">{{ $quote->user->company_name ?? 'Individual' }}</p>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-brand-700">{{ $quote->origin->code }}</span>
                                        <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        <span class="text-xs font-bold text-slate-900">{{ $quote->country->name }}</span>
                                    </div>
                                    <div class="mt-1 flex items-center gap-1">
                                        <span class="text-[9px] text-slate-400 uppercase font-bold tracking-widest">POE:</span>
                                        <span class="text-[9px] text-brand-600 font-bold uppercase">{{ $quote->destination->name ?? 'Not Assigned' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <p class="text-sm font-bold text-slate-700">{{ number_format($quote->volume_cft, 2) }} CFT</p>
                                    <p class="text-[10px] text-slate-400 uppercase font-medium">{{ number_format($quote->volume_cbm, 3) }} CBM</p>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <p class="text-lg font-bold text-emerald-600 font-outfit">${{ number_format($quote->total_price, 2) }}</p>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    @if($quote->status === 'requested')
                                        <span class="px-2 py-1 rounded bg-amber-50 text-amber-700 text-[10px] font-bold uppercase border border-amber-100">Booking Requested</span>
                                    @elseif($quote->status === 'active')
                                        <span class="px-2 py-1 rounded bg-blue-50 text-blue-700 text-[10px] font-bold uppercase border border-blue-100">Pending</span>
                                    @elseif($quote->status === 'booked')
                                        <span class="px-2 py-1 rounded bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase border border-emerald-100">Booked</span>
                                    @elseif($quote->status === 'rejected')
                                        <span class="px-2 py-1 rounded bg-red-50 text-red-700 text-[10px] font-bold uppercase border border-red-100">Rejected</span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-slate-50 text-slate-700 text-[10px] font-bold uppercase border border-slate-100">{{ $quote->status }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-right" onclick="event.stopPropagation()">
                                    <div class="flex justify-end gap-2">
                                        @if($quote->status === 'active')
                                            <button 
                                                x-on:click.prevent="acceptUrl = '{{ route('admin.quotes.accept', $quote) }}'; quoteRef = '{{ $quote->reference_number }}'; showAcceptModal = true"
                                                class="bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20">
                                                Accept / Book
                                            </button>
                                            <form action="{{ route('admin.quotes.reject', $quote) }}" method="POST">
                                                @csrf
                                                <button class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase hover:bg-red-600 hover:text-white transition-all border border-red-100">
                                                    Reject
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.quotes.show', $quote) }}" class="bg-slate-100 text-slate-600 px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase hover:bg-slate-200 transition-all border border-slate-200 no-underline">
                                            View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($quotes->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                    {{ $quotes->links() }}
                </div>
            @endif
        </div>

        <!-- Reusable Vessel Modal -->
        <div x-show="showAcceptModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
            <div @click.away="showAcceptModal = false" class="bg-white rounded-[2rem] shadow-2xl max-w-md w-full p-8 animate-fade-in">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight">Finalize Booking</h2>
                    <button type="button" @click="showAcceptModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-6">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase mb-1">Quote Reference</p>
                        <p class="text-lg font-bold text-brand-700" x-text="quoteRef"></p>
                    </div>

                    <form :action="acceptUrl" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Select Vessel / Container</label>
                            <select name="departure_id" required class="w-full rounded-2xl border-slate-200 py-3 text-sm focus:ring-brand-500 shadow-sm font-semibold text-slate-600">
                                <option value="">-- Choose Container --</option>
                                @foreach($departures as $departure)
                                    <option value="{{ $departure->id }}">
                                        {{ $departure->vessel_name }} (Voyage: #{{ $departure->voyage_number }} | Dep: {{ $departure->departure_date->format('M d') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-end gap-3 pt-6 border-t border-slate-50">
                            <button type="button" @click="showAcceptModal = false" class="px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest text-slate-500 border border-slate-200 hover:bg-slate-50 transition-all">
                                Cancel
                            </button>
                            <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest text-white bg-slate-900 hover:bg-slate-800 shadow-xl shadow-slate-900/20 transition-all">
                                Confirm Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
