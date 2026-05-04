<x-app-layout>
    <x-slot name="header">
        {{ __('New Shipment Quote') }}
    </x-slot>

    <div class="max-w-4xl mx-auto py-8">
        <form action="{{ route('quotes.store') }}" method="POST" x-data="quoteCalculator(@js($prefill))">
            @csrf
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Form Inputs -->
                <div class="md:col-span-2 space-y-6">
                    <div class="premium-card">
                        <h3 class="text-xl font-bold text-slate-900 mb-6 font-outfit">Route Details</h3>
                        
                        <div class="space-y-4">
                            <!-- Origin -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Origin Warehouse</label>
                                <select name="origin_id" x-model="originId" class="input-premium w-full">
                                    <option value="">Select Origin</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}">{{ $w->name }} ({{ $w->code }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Destination -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Destination Country</label>
                                <select name="country_id" x-model="countryId" @change="updateRegions()" class="input-premium w-full">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $c)
                                        <option value="{{ $c->id }}" data-regions="{{ $c->regions->toJson() }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Region -->
                            <div x-show="regions.length > 0">
                                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Region</label>
                                <select name="region_id" x-model="regionId" class="input-premium w-full">
                                    <option value="">Select Region</option>
                                    <template x-for="r in regions" :key="r.id">
                                        <option :value="r.id" x-text="r.name"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="premium-card">
                        <h3 class="text-xl font-bold text-slate-900 mb-6 font-outfit">Cargo Specs</h3>
                        
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Volume</label>
                                <input type="number" step="0.01" name="volume" x-model="volume" @input="calculate()" class="input-premium w-full" placeholder="0.00">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Unit</label>
                                <select name="volume_unit" x-model="unit" @change="calculate()" class="input-premium w-full">
                                    <option value="CBM">CBM</option>
                                    <option value="CFT">CFT</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Service Logic</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="service_type" value="Warehouse to Door" @change="calculate()" class="peer sr-only" checked>
                                    <div class="p-4 border border-slate-200 rounded-2xl peer-checked:border-brand-500 peer-checked:bg-brand-50 transition-all text-center">
                                        <span class="block text-sm font-bold text-slate-900 uppercase tracking-widest">Standard Consolidation</span>
                                        <span class="text-[10px] text-slate-500">Warehouse to Door</span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer opacity-50">
                                    <input type="radio" name="service_type" value="Express" disabled class="peer sr-only">
                                    <div class="p-4 border border-slate-200 rounded-2xl transition-all text-center bg-slate-50">
                                        <span class="block text-sm font-bold text-slate-900 uppercase tracking-widest">Express Air</span>
                                        <span class="text-[10px] text-slate-500">Coming Soon</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Preview Sidebar -->
                <div class="space-y-6">
                    <div class="premium-card border-brand-500/20 shadow-xl shadow-brand-500/5">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-brand-700 mb-4 italic">Quote Summary</h4>
                        
                            <div class="flex justify-between border-b border-slate-100 pb-2">
                                <span class="text-xs text-slate-500">Actual Vol.</span>
                                <span class="text-sm font-bold text-slate-900"><span x-text="actualCft">0.00</span> CFT</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-100 pb-2">
                                <span class="text-xs text-slate-500">Billable Vol. (Min)</span>
                                <span class="text-sm font-bold text-brand-700"><span x-text="taxableCft">0.00</span> CFT</span>
                            </div>
                        </div>

                        <!-- Minimum Volume Warning -->
                        <div x-show="actualCft < minVol && volume > 0" x-cloak class="mb-6 p-3 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <div>
                                <p class="text-[10px] font-bold text-amber-800 uppercase tracking-tight">Minimum Applied</p>
                                <p class="text-[10px] text-amber-700 leading-tight mt-0.5">Your volume is below the <span x-text="minVol"></span> CFT minimum. Pricing is based on the minimum volume.</p>
                            </div>
                        </div>

                        <div class="p-4 bg-brand-50 border border-brand-100 rounded-2xl text-center mb-6">
                            <p class="text-[10px] uppercase tracking-widest text-brand-700 font-bold mb-1">Estimated Total</p>
                            <p class="text-3xl font-bold text-slate-900 font-outfit" x-text="estimatedTotal ? '$' + estimatedTotal : '$---'">$---</p>
                            <p class="text-[10px] text-slate-500 mt-2 italic" x-text="loading ? 'Updating rate...' : 'Calculated at current market rates'"></p>
                        </div>

                        <!-- AJAX Form Submit Handler -->
                        <button type="button" @click="submitQuote()" :disabled="!estimatedTotal || submitting" class="btn-primary w-full py-4 flex justify-center items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed btn-loading-wrapper">
                            <div class="btn-spinner" :class="{ 'loading': submitting }">
                                <div class="spinner-mini"></div>
                            </div>
                            <div class="flex items-center gap-2 btn-loading-text" :class="{ 'loading': submitting }">
                                <span x-text="submitting ? 'Generating...' : 'Generate Official Quote'"></span>
                                <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function quoteCalculator(prefill = {}) {
            return {
                originId: prefill.origin_id || '',
                countryId: prefill.country_id || '',
                regionId: prefill.region_id || '',
                regions: [],
                volume: prefill.volume || 0,
                unit: prefill.volume_unit || 'CBM',
                taxableCft: 0,
                estimatedTotal: null,
                loading: false,
                submitting: false,
                minVol: {{ $minVol }},
                actualCft: 0,
                
                init() {
                    if (this.countryId) {
                        const select = document.querySelector('select[name="country_id"]');
                        const option = [...select.options].find(o => o.value == this.countryId);
                        if (option && option.dataset.regions) {
                            this.regions = JSON.parse(option.dataset.regions);
                        }
                    }
                    this.calculate();
                },

                updateRegions() {
                    const select = event.target;
                    const option = select.options[select.selectedIndex];
                    if (option.dataset.regions) {
                        this.regions = JSON.parse(option.dataset.regions);
                    } else {
                        this.regions = [];
                    }
                    this.regionId = '';
                    this.calculate();
                },
                
                async calculate() {
                    let cft = this.unit === 'CBM' ? this.volume * 35.3147 : this.volume;
                    this.actualCft = parseFloat(cft).toFixed(2);
                    this.taxableCft = Math.max(cft, this.minVol).toFixed(2);

                    if (this.originId && this.countryId && this.volume > 0) {
                        this.loading = true;
                        try {
                            const serviceType = document.querySelector('input[name="service_type"]:checked');
                            const params = new URLSearchParams({
                                origin_id: this.originId,
                                country_id: this.countryId,
                                region_id: this.regionId,
                                volume: this.volume,
                                volume_unit: this.unit,
                                service_type: serviceType ? serviceType.value : 'Warehouse to Door'
                            });
                            
                            const response = await fetch(`{{ route('quotes.calculate') }}?${params.toString()}`);
                            const data = await response.json();
                            
                            if (data.success) {
                                this.estimatedTotal = data.total_price.toLocaleString('en-US', { minimumFractionDigits: 2 });
                            } else {
                                this.estimatedTotal = null;
                            }
                        } catch (e) {
                            console.error(e);
                        } finally {
                            this.loading = false;
                        }
                    } else {
                        this.estimatedTotal = null;
                    }
                },

                async submitQuote() {
                    if (!this.estimatedTotal) return;
                    this.submitting = true;
                    
                    const form = event.target.closest('form');
                    const formData = new FormData(form);
                    
                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            },
                            body: formData
                        });
                        
                        if (response.ok) {
                            // Quote successful, redirect via javascript to avoid flash reloading
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Quote generated successfully!', type: 'success' }}));
                            setTimeout(() => window.location.href = '{{ route("quotes.index") }}', 1000);
                        } else {
                            const errData = await response.json();
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: errData.message || 'Error generating quote.', type: 'error' }}));
                            this.submitting = false;
                        }
                    } catch (e) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'A network error occurred.', type: 'error' }}));
                        this.submitting = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
