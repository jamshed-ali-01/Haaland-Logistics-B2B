<x-app-layout>
    <x-slot name="header">
        {{ __('Quote Detail') }}: {{ $quote->reference_number }}
    </x-slot>

    <div class="space-y-6 animate-fade-in-up">
        <!-- Back Button -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.quotes.index') }}" class="flex items-center gap-2 text-slate-500 hover:text-brand-700 transition-colors no-underline font-bold text-sm uppercase tracking-widest">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l-7-7m7-7H3"/></svg>
                Back to List
            </a>
            <div class="flex items-center gap-3">
                @if($quote->status === 'active')
                    <form action="{{ route('admin.quotes.accept', $quote) }}" method="POST">
                        @csrf
                        <x-primary-button class="bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-500/20">
                            {{ __('Accept & Create Booking') }}
                        </x-primary-button>
                    </form>
                    <form action="{{ route('admin.quotes.reject', $quote) }}" method="POST">
                        @csrf
                        <button class="px-6 py-2.5 rounded-xl font-semibold text-red-600 border border-red-200 hover:bg-red-50 transition-all">
                            {{ __('Reject Inquiry') }}
                        </button>
                    </form>
                @elseif($quote->status === 'booked')
                    <div class="bg-emerald-50 text-emerald-700 px-4 py-2 rounded-xl border border-emerald-100 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="font-bold uppercase text-xs tracking-widest">Converted to Booking: {{ $quote->booking->booking_number ?? 'N/A' }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Inquiry Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Main Info Card -->
                <div class="premium-card">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-1">Reference Number</p>
                            <h2 class="text-3xl font-black text-slate-900 font-outfit">{{ $quote->reference_number }}</h2>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-1">Current Status</p>
                            @if($quote->status === 'active')
                                <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase border border-blue-100">Pending Review</span>
                            @elseif($quote->status === 'booked')
                                <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold uppercase border border-emerald-100">Booked</span>
                            @elseif($quote->status === 'rejected')
                                <span class="px-3 py-1 rounded-full bg-red-50 text-red-700 text-xs font-bold uppercase border border-red-100">Rejected</span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-slate-50 text-slate-700 text-xs font-bold uppercase border border-slate-100">{{ $quote->status }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 p-6 bg-slate-50/50 rounded-2xl border border-slate-100">
                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Route & Logistics</p>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-brand-700 text-white flex items-center justify-center font-bold text-xs">{{ $quote->origin->code }}</div>
                                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    <div class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-xs">{{ $quote->destination->code ?? '?' }}</div>
                                </div>
                                <p class="text-sm font-bold text-slate-900 mt-2">{{ $quote->country->name }} ({{ $quote->region->name }})</p>
                                <p class="text-[10px] text-slate-500 font-medium uppercase mt-1">POE: {{ $quote->destination->name ?? 'Not Assigned' }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Cargo Volume</p>
                                <p class="text-2xl font-black text-slate-900 font-outfit">{{ number_format($quote->volume_cft, 2) }} <span class="text-sm font-bold text-slate-400">CFT</span></p>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-tight mt-1">{{ number_format($quote->volume_cbm, 3) }} CBM Equivalent</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Quote Pricing</p>
                                <p class="text-2xl font-black text-emerald-600 font-outfit">${{ number_format($quote->total_price, 2) }}</p>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-tight mt-1">All-inclusive estimated</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-2 gap-6">
                        <div class="p-4 border border-slate-100 rounded-xl">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Origin Details</p>
                            <p class="text-sm font-bold text-slate-900">{{ $quote->origin->name }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $quote->origin->address }}</p>
                        </div>
                        <div class="p-4 border border-slate-100 rounded-xl">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Service Selection</p>
                            <p class="text-sm font-bold text-slate-900">{{ $quote->service_type ?? 'Standard GRP' }}</p>
                            <p class="text-xs text-slate-500 mt-1">Carrier Service: {{ $quote->service ?? 'Direct' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Admin Notes Card -->
                <div class="premium-card">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 font-outfit uppercase tracking-widest">Internal Notes & History</h3>
                    </div>

                    <form action="{{ route('admin.quotes.update-notes', $quote) }}" method="POST" class="space-y-4">
                        @csrf
                        <textarea name="admin_notes" rows="4" class="w-full rounded-2xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 text-sm italic" placeholder="Add internal notes about this inquiry (visible only to admins)...">{{ $quote->admin_notes }}</textarea>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-slate-900 text-white px-6 py-2 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-brand-900 transition-all">Save Notes</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Client & Metadata -->
            <div class="space-y-6">
                <!-- Client Info Card -->
                <div class="premium-card">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-6">Client Profile</p>
                    
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-16 h-16 rounded-2xl bg-brand-700 text-white flex items-center justify-center text-2xl font-black shadow-xl shadow-brand-700/20">
                            {{ substr($quote->user->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-slate-900">{{ $quote->user->name }}</h4>
                            <p class="text-sm font-bold text-brand-700">{{ $quote->user->company_name ?? 'Individual' }}</p>
                        </div>
                    </div>

                    <div class="space-y-4 border-t border-slate-100 pt-6">
                        <div class="flex items-center gap-3 text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span class="text-xs font-medium">{{ $quote->user->email }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span class="text-xs font-medium">{{ $quote->user->phone ?? 'Not provided' }}</span>
                        </div>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('admin.users') }}?search={{ $quote->user->email }}" class="block text-center py-3 rounded-xl bg-slate-50 text-slate-600 text-[10px] font-bold uppercase tracking-widest hover:bg-slate-100 transition-all border border-slate-100 no-underline">
                            View Full Client Profile
                        </a>
                    </div>
                </div>

                <!-- Timeline / Log Card -->
                <div class="premium-card">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-6">Activity Timeline</p>
                    <div class="space-y-6 relative before:absolute before:left-2 before:top-2 before:bottom-2 before:w-px before:bg-slate-100">
                        <div class="relative pl-8">
                            <div class="absolute left-0 top-1 w-4 h-4 rounded-full bg-brand-700 border-4 border-white shadow-sm"></div>
                            <p class="text-[11px] font-bold text-slate-900">Inquiry Received</p>
                            <p class="text-[10px] text-slate-400">{{ $quote->created_at->format('M d, Y - H:i') }}</p>
                        </div>
                        @if($quote->status === 'booked')
                            <div class="relative pl-8">
                                <div class="absolute left-0 top-1 w-4 h-4 rounded-full bg-emerald-500 border-4 border-white shadow-sm"></div>
                                <p class="text-[11px] font-bold text-slate-900">Converted to Booking</p>
                                <p class="text-[10px] text-slate-400">{{ $quote->booking->created_at->format('M d, Y - H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
