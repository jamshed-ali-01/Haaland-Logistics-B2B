<x-app-layout>
    <x-slot name="header">
        {{ __('Rate Management') }}
    </x-slot>

    <div class="space-y-6" x-data="{ 
        editingRate: { id: '', origin_id: '', country_id: '', region_id: '', shipping_service_id: '', service_type_id: '', tier_prices: {} },
        newRate: { country_id: '' },
        countries: {{ $countries->toJson() }},
        getRegions(countryId) {
            const country = this.countries.find(c => c.id == countryId);
            return country ? country.regions : [];
        }
    }">
        <!-- Actions Row -->
        <div class="flex justify-between items-center animate-fade-in-up">
            <div class="bg-white px-6 py-4 rounded-2xl border border-slate-200 flex items-center gap-4">
                <div class="flex flex-col">
                    <span class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Standard Formula</span>
                    <span class="text-sm font-medium text-slate-600 italic">Rate * Volume + Origin Fee</span>
                </div>
            </div>
            
            <button x-on:click.prevent="$dispatch('open-modal', 'add-rate-modal')" class="btn-primary flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Add New Route
            </button>
        </div>

        <!-- Rates Table Card -->
        <div class="premium-card !p-0 overflow-hidden animate-fade-in-up" style="animation-delay: 0.1s">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-900 font-outfit">Active Shipping Rates</h3>
                <div class="flex gap-3">
                    <select class="input-premium py-2 text-xs w-40">
                        <option>All Origins</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 text-[10px] uppercase tracking-widest font-bold transition-colors">
                            <th class="px-6 py-4">Route Info</th>
                            <th class="px-6 py-4">Service</th>
                            <th class="px-6 py-4">Service Type</th>
                            <th class="px-4 py-4 text-center">Tier 110</th>
                            <th class="px-4 py-4 text-center">Tier 170</th>
                            <th class="px-4 py-4 text-center">Tier 200</th>
                            <th class="px-4 py-4 text-center">Tier 350+</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($rates as $rate)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-2">
                                            <span class="px-1.5 py-0.5 rounded bg-brand-700/10 text-brand-700 text-[10px] font-bold border border-brand-700/20">{{ $rate->origin->code }}</span>
                                            <span class="text-sm font-bold text-slate-900">→ {{ $rate->country->name }}</span>
                                        </div>
                                        @if($rate->region)
                                            <span class="text-[10px] text-slate-400 uppercase italic">{{ $rate->region->name }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold uppercase tracking-tighter">{{ $rate->shippingService->name ?? $rate->service }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-brand-700/5 text-brand-700 font-bold uppercase tracking-tighter border border-brand-700/10">{{ $rate->serviceType->name ?? $rate->service_type }}</span>
                                </td>
                                @foreach([110, 170, 200, 350] as $t)
                                    <td class="px-4 py-4 text-center">
                                        @php $tier = $rate->tiers->where('min_volume', $t)->first(); @endphp
                                        @if($tier)
                                            <span class="text-sm font-bold text-emerald-600 font-outfit">${{ number_format($tier->price_per_cft, 2) }}</span>
                                        @else
                                            <span class="text-[10px] text-slate-300 font-bold">—</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button 
                                            @click='
                                                (() => {
                                                    let data = {!! $rate->toJson() !!};
                                                    // Ensure tier_prices is initialized with all 4 keys
                                                    data.tier_prices = { "110": "", "170": "", "200": "", "350": "" };
                                                    if (data.tiers) {
                                                        data.tiers.forEach(t => { 
                                                            data.tier_prices[Math.round(t.min_volume).toString()] = t.price_per_cft; 
                                                        });
                                                    }
                                                    editingRate = data;
                                                })();
                                                $dispatch("open-modal", "edit-rate-modal")
                                            '
                                            class="bg-slate-50 text-slate-400 hover:bg-slate-100 p-2 rounded-lg transition-all border border-slate-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <form action="{{ route('admin.rates.destroy', $rate) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this route? This will remove all associated tiers.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-50 text-red-500 hover:bg-red-600 hover:text-white p-2 rounded-lg transition-all border border-red-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                    No rates defined yet. Click "Add New Route" to start.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($rates->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $rates->links() }}
                </div>
            @endif
        </div>


    <!-- Add Rate Modal -->
    <x-modal name="add-rate-modal" :show="$errors->any()" focusable>
        <form action="{{ route('admin.rates.store') }}" method="POST" class="p-8">
            @csrf
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-slate-900 font-outfit uppercase tracking-tight">Add New Shipping Route</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

                <div class="space-y-8">
                    <!-- Route & Service Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="origin_id" value="Origin Warehouse" />
                            <select name="origin_id" class="input-premium w-full mt-1" required>
                                <option value="">Select Origin</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="country_id" value="Destination Country" />
                            <select name="country_id" class="input-premium w-full mt-1" required x-model="newRate.country_id">
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="region_id" value="Region (Optional)" />
                            <select name="region_id" class="input-premium w-full mt-1">
                                <option value="">Entire Country / Main Hub</option>
                                <template x-for="region in getRegions(newRate.country_id)" :key="region.id">
                                    <option :value="region.id" x-text="region.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="shipping_service_id" value="Service (Freight Category)" />
                            <select name="shipping_service_id" class="input-premium w-full mt-1" required>
                                <option value="">Select Service</option>
                                @foreach($shippingServices as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="service_type_id" value="Service Type (Scope)" />
                            <select name="service_type_id" class="input-premium w-full mt-1" required>
                                <option value="">Select Scope</option>
                                @foreach($serviceTypes as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                <!-- Pricing Tiers -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200">
                    <h4 class="text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400 mb-6 flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                        Volume Pricing Tiers ($ / CFT)
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach([110, 170, 200, 350] as $index => $vol)
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-700">Tier {{ $vol }}+</label>
                                <input type="hidden" name="tiers[{{ $index }}][min_volume]" value="{{ $vol }}">
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm italic">$</span>
                                    <input type="number" step="0.01" name="tiers[{{ $index }}][price_per_cft]" 
                                           class="w-full pl-7 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-1 focus:ring-brand-700 focus:border-brand-700 outline-none transition-all" 
                                           required placeholder="0.00">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                    <x-primary-button class="shadow-xl shadow-brand-700/20">Create Shipping Route</x-primary-button>
                </div>
            </div>
        </form>
    </x-modal>

    <!-- Edit Rate Modal -->
    <x-modal name="edit-rate-modal" focusable>
        <form :action="editingRate ? '/admin/rates/' + editingRate.id : '#'" method="POST" class="p-8">
            @csrf
            @method('PATCH')
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-slate-900 font-outfit uppercase tracking-tight">Edit Shipping Route</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="edit_origin_id" value="Origin Warehouse" />
                        <select name="origin_id" id="edit_origin_id" class="input-premium w-full mt-1" required x-model="editingRate.origin_id">
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="edit_country_id" value="Destination Country" />
                        <select name="country_id" id="edit_country_id" class="input-premium w-full mt-1" required x-model="editingRate.country_id">
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="edit_region_id" value="Region (Optional)" />
                        <select name="region_id" id="edit_region_id" class="input-premium w-full mt-1" x-model="editingRate.region_id">
                            <option value="">Entire Country / Main Hub</option>
                            <template x-for="region in getRegions(editingRate.country_id)" :key="region.id">
                                <option :value="region.id" x-text="region.name" :selected="region.id == editingRate.region_id"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="edit_shipping_service_id" value="Service (Freight Category)" />
                        <select name="shipping_service_id" id="edit_shipping_service_id" class="input-premium w-full mt-1" required x-model="editingRate.shipping_service_id">
                            @foreach($shippingServices as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="edit_service_type_id" value="Service Type (Scope)" />
                        <select name="service_type_id" id="edit_service_type_id" class="input-premium w-full mt-1" required x-model="editingRate.service_type_id">
                            @foreach($serviceTypes as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200">
                    <h4 class="text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400 mb-6 flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                        Update Pricing Tiers ($ / CFT)
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach([110, 170, 200, 350] as $index => $vol)
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-700">Tier {{ $vol }}+</label>
                                <input type="hidden" name="tiers[{{ $index }}][min_volume]" value="{{ $vol }}">
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm italic">$</span>
                                    <input type="number" step="0.01" name="tiers[{{ $index }}][price_per_cft]" 
                                           x-model="editingRate.tier_prices['{{ $vol }}']"
                                           class="w-full pl-7 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-1 focus:ring-brand-700 focus:border-brand-700 outline-none transition-all" 
                                           required placeholder="0.00">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                    <x-primary-button class="shadow-xl shadow-brand-700/20">Save Changes</x-primary-button>
                </div>
            </div>
        </form>
    </x-modal>
    </div>
</x-app-layout>
