<x-app-layout>
    <x-slot name="header">
        {{ __('Quote Inquiries') }}
    </x-slot>

    <div x-data="{ 
            selectedLead: null,
            openDetails(lead) {
                this.selectedLead = lead;
                $dispatch('open-modal', 'lead-details');
            }
         }" class="space-y-6">
        
        <!-- Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in-up">
            <div class="premium-card">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Total Inquiries</p>
                <p class="text-3xl font-bold font-outfit text-slate-900">{{ $leads->total() }}</p>
            </div>
            <div class="premium-card">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">New Leads</p>
                <p class="text-3xl font-bold font-outfit text-brand-700">{{ $leads->where('status', 'new')->count() }}</p>
            </div>
            <div class="premium-card">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Conversion Rate</p>
                @php
                    $total = $leads->total();
                    $converted = $leads->where('status', 'converted')->count();
                    $rate = $total > 0 ? ($converted / $total) * 100 : 0;
                @endphp
                <p class="text-3xl font-bold font-outfit text-emerald-600">{{ number_format($rate, 1) }}%</p>
            </div>
        </div>

        <!-- Leads Table Card -->
        <div class="premium-card !p-0 overflow-hidden animate-fade-in-up" style="animation-delay: 0.2s">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-900 font-outfit uppercase tracking-widest">Guest Inquiries</h3>
            </div>

            <div class="p-6">
                <table class="datatable w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-brand-900 text-white text-[10px] uppercase tracking-widest font-bold">
                            <th class="px-6 py-4">Business Email</th>
                            <th class="px-6 py-4">Cargo Intent</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Submitted</th>
                            <th class="px-6 py-4 text-right">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 italic">
                        @foreach($leads as $lead)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-violet-100 border border-violet-200 flex items-center justify-center font-bold text-violet-700 shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">{{ $lead->email }}</p>
                                            <p class="text-[10px] text-slate-400 uppercase tracking-widest">Guest User</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <p class="text-sm font-bold text-slate-700">{{ number_format($lead->volume_cft, 1) }} CFT</p>
                                    <p class="text-[10px] text-slate-400 uppercase font-medium">
                                        {{ $lead->origin->name ?? 'N/A' }} → {{ $lead->country->name ?? 'N/A' }}
                                    </p>
                                </td>
                                <td class="px-6 py-5">
                                    @if($lead->status === 'new')
                                        <span class="px-2 py-1 rounded bg-violet-50 text-violet-700 text-[10px] font-bold uppercase border border-violet-100">Hot Lead</span>
                                    @elseif($lead->status === 'converted')
                                        <span class="px-2 py-1 rounded bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase border border-emerald-100 italic">Converted</span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-slate-50 text-slate-700 text-[10px] font-bold uppercase border border-slate-100">{{ $lead->status }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    <p class="text-xs text-slate-500 font-medium">{{ $lead->created_at->format('M d, Y') }}</p>
                                    <p class="text-[9px] text-slate-400 font-bold italic">{{ $lead->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <button @click="openDetails({{ $lead->toJson() }})" class="text-slate-400 hover:text-brand-700 transition-colors bg-transparent border-none cursor-pointer" title="View Full Details">
                                        <svg class="w-6 h-6 shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Details Modal -->
        <x-modal name="lead-details" maxWidth="2xl">
            <template x-if="selectedLead">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-100">
                        <div>
                            <h2 class="text-2xl font-bold text-slate-900 font-outfit uppercase tracking-tight">Inquiry Details</h2>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Lead ID: #<span x-text="selectedLead.id"></span></p>
                        </div>
                        <button x-on:click="$dispatch('close-modal', 'lead-details')" class="text-slate-400 hover:text-slate-900 bg-transparent border-none cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-8 mb-8">
                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Business Email</p>
                                <p class="text-slate-900 font-bold" x-text="selectedLead.email"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Submission Date</p>
                                <p class="text-slate-900" x-text="new Date(selectedLead.created_at).toLocaleString()"></p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Volume Intent</p>
                                <p class="text-slate-900 font-bold"><span x-text="selectedLead.volume_cft"></span> CFT</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Status</p>
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase border"
                                      :class="selectedLead.status === 'converted' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-violet-50 text-violet-700 border-violet-100'"
                                      x-text="selectedLead.status"></span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 space-y-6">
                        <div class="flex items-center gap-6">
                            <div class="flex-1">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Origin Warehouse</p>
                                <p class="text-sm font-bold text-slate-900" x-text="selectedLead.origin ? selectedLead.origin.name : 'Not Specified'"></p>
                            </div>
                            <div class="text-slate-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </div>
                            <div class="flex-1 text-right">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Destination Country</p>
                                <p class="text-sm font-bold text-slate-900" x-text="selectedLead.country ? selectedLead.country.name : 'Not Specified'"></p>
                            </div>
                        </div>
                        
                        <div x-show="selectedLead.region">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Target Region</p>
                            <p class="text-sm font-bold text-slate-900" x-text="selectedLead.region ? selectedLead.region.name : ''"></p>
                        </div>
                    </div>

                    <div class="mt-8 flex gap-4">
                        <a :href="'mailto:' + selectedLead.email" class="btn-primary flex-1 text-center py-4 no-underline inline-block rounded-xl font-bold uppercase tracking-widest text-[10px]">
                            Contact via Email
                        </a>
                        <button @click="$dispatch('close-modal', 'lead-details')" class="flex-1 py-4 text-[10px] font-bold text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest bg-white border border-slate-200 rounded-xl cursor-pointer">
                            Close
                        </button>
                    </div>
                </div>
            </template>
        </x-modal>
    </div>
</x-app-layout>
