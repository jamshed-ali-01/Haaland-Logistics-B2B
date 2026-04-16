<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Haaland Logistics | Premium B2B Shipping</title>
        <link rel="icon" href="{{ asset('assets/favicon.png') }}">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-[#f8fafb] text-slate-900" x-data="{}">
        <x-toast />
        <!-- Navigation -->
        <nav class="fixed top-0 w-full z-50 bg-white border-b border-slate-100 px-6 py-4 shadow-sm">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-brand-700 rounded-lg flex items-center justify-center font-bold text-white text-xl shadow-lg shadow-brand-700/20">
                        HL
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-900 font-outfit uppercase">Haaland <span class="text-brand-700 font-normal">Logistics</span></span>
                </div>
                
                <div class="flex items-center gap-6">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-primary no-underline text-sm uppercase tracking-widest font-bold">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-slate-500 hover:text-brand-700 transition-colors font-bold text-xs uppercase tracking-widest no-underline">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-primary no-underline text-sm uppercase tracking-widest font-bold">Registration</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="pt-40 pb-20 px-6 overflow-hidden min-h-screen flex flex-col justify-start">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-20 items-center w-full">
                <div class="animate-fade-in-up">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-700/10 border border-brand-700/20 text-brand-700 text-[10px] font-bold uppercase tracking-widest mb-8">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-700 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-700"></span>
                        </span>
                        Enterprise Logistics Solutions
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-bold text-slate-900 font-outfit leading-[1.1] mb-8 tracking-tight">
                        Global Shipping <br/>
                        <span class="text-brand-700 italic">Simplified.</span>
                    </h1>
                    <p class="text-lg text-slate-500 mb-10 max-w-lg leading-relaxed font-outfit">
                        The ultimate B2B platform for instant quotes, vessel scheduling, and real-time shipment management. Engineered for the modern supply chain.
                    </p>
                    
                    <div class="flex items-center gap-6 p-1 pr-6 bg-white w-fit rounded-full shadow-lg border border-slate-100">
                        <div class="flex -space-x-3 pl-3">
                            <div class="w-10 h-10 rounded-full border-2 border-white bg-brand-700 flex items-center justify-center text-xs font-bold text-white">H</div>
                            <div class="w-10 h-10 rounded-full border-2 border-white bg-slate-900 flex items-center justify-center text-xs font-bold text-white">L</div>
                        </div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Premium Portal <span class="text-emerald-500 mx-2">●</span> Active Hubs: <span class="text-slate-900">LA, Miami</span></p>
                    </div>
                </div>

                <!-- Quote Widget -->
                <div class="animate-fade-in-up" style="animation-delay: 0.2s">
                    <div x-data="quoteWidget()" class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-2xl relative group overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-brand-700/5 blur-[60px] rounded-full"></div>
                        
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-2xl font-bold text-slate-900 font-outfit uppercase tracking-tight">Instant Quotation</h2>
                            <div class="p-2 bg-brand-700/10 rounded-xl">
                                <svg class="w-6 h-6 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg>
                            </div>
                        </div>

                        <form @submit.prevent="submitQuote()" class="space-y-6">
                            @csrf
                            <!-- Origin -->
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">Origin Warehouse</label>
                                <select x-model="origin" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3.5 text-slate-900 focus:border-brand-700 focus:ring-0 outline-none transition-all font-bold text-sm appearance-none cursor-pointer">
                                    <option value="">Select Origin Point</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}">{{ $w->name }} ({{ $w->code }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Destination -->
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">Destination Country</label>
                                <select x-model="country_id" required @change="updateRegions()" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3.5 text-slate-900 focus:border-brand-700 focus:ring-0 outline-none transition-all font-bold text-sm appearance-none cursor-pointer">
                                    <option value="">Select Destination</option>
                                    @foreach($countries as $c)
                                        <option value="{{ $c->id }}" data-regions="{{ $c->regions->toJson() }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Region (Dynamic) -->
                            <div x-show="regions.length > 0" x-transition class="space-y-2">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">Target Region</label>
                                <select x-model="region_id" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3.5 text-slate-900 focus:border-brand-700 focus:ring-0 outline-none transition-all font-bold text-sm appearance-none cursor-pointer">
                                    <option value="">Select Region</option>
                                    <template x-for="r in regions" :key="r.id">
                                        <option :value="r.id" x-text="r.name"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Volume -->
                            <div class="grid grid-cols-2 gap-6 pb-2">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">Volume (CBM)</label>
                                    <input type="number" step="0.01" x-model="cbm" @input="convertToCft()" placeholder="0.00" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3.5 text-slate-900 focus:border-brand-700 focus:ring-0 outline-none transition-all font-bold text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">Volume (CFT)</label>
                                    <input type="number" step="0.01" x-model="cft" @input="convertToCbm()" placeholder="0.00" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3.5 text-slate-900 focus:border-brand-700 focus:ring-0 outline-none transition-all font-bold text-sm">
                                </div>
                            </div>

                            <div class="p-6 bg-brand-700/5 rounded-2xl border border-brand-700/10 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Billable Volume</p>
                                    <p class="text-2xl font-bold text-slate-900 font-outfit"><span x-text="Math.max(cft || 0, 100)">100</span> <span class="text-sm font-normal text-slate-400">CFT</span></p>
                                </div>
                                <div class="text-left sm:text-right">
                                    <span x-show="cft < 100 && cft > 0" class="text-[9px] font-bold text-brand-700 bg-brand-700/10 px-2 py-1 rounded-full uppercase tracking-tighter">Min 100 Applied</span>
                                </div>
                            </div>

                            <!-- Lead Data Capture -->
                            <input type="hidden" name="origin_id" x-model="origin">
                            <input type="hidden" name="country_id" x-model="country_id">
                            <input type="hidden" name="region_id" x-model="region_id">
                            <input type="hidden" name="volume" x-model="cft">
                            <input type="hidden" name="volume_unit" value="CFT">

                            <div class="space-y-2 mb-6">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">Your Business Email</label>
                                <input type="email" x-model="email" required placeholder="name@company.com" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3.5 text-slate-900 focus:border-brand-700 focus:ring-0 outline-none transition-all font-bold text-sm">
                            </div>
                            
                             <button type="submit" 
                                     :disabled="loading"
                                     class="w-full bg-brand-700 hover:bg-brand-800 text-white font-bold py-5 rounded-2xl flex items-center justify-center gap-3 transition-all shadow-xl shadow-brand-700/20 no-underline uppercase tracking-widest text-sm outline-none border-none cursor-pointer mb-4 disabled:opacity-70 disabled:cursor-not-allowed">
                                <span x-show="!loading" class="flex items-center gap-3">
                                    Submit Quote Inquiry
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </span>
                                <span x-show="loading" class="flex items-center gap-3">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>

                            @auth
                                <a href="{{ route('quotes.create') }}" class="block text-center text-[10px] font-bold text-brand-700 uppercase tracking-widest hover:text-brand-800 transition-colors">
                                    Or Continue to portal for real-time rates →
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="block text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-brand-700 transition-colors">
                                    Registration for instant portal access →
                                </a>
                            @endauth
                        </form>
                                <p class="text-[10px] text-center text-slate-400 uppercase tracking-[0.2em] mt-6 font-bold">
                                    Accurate LCL rates require portal access
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Grid -->
        <section class="max-w-7xl mx-auto px-6 mb-32 grid grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm animate-fade-in-up">
                <p class="text-[10px] font-bold text-brand-700 uppercase tracking-widest mb-2">Operation Hubs</p>
                <h3 class="text-3xl font-bold text-slate-900 font-outfit">LA / MIA</h3>
            </div>
            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm animate-fade-in-up" style="animation-delay: 0.1s">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Destinations</p>
                <h3 class="text-3xl font-bold text-slate-900 font-outfit">100+ Points</h3>
            </div>
            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm animate-fade-in-up" style="animation-delay: 0.2s">
                <p class="text-[10px] font-bold text-brand-700 uppercase tracking-widest mb-2">Availability</p>
                <h3 class="text-3xl font-bold text-slate-900 font-outfit">24/7 Live</h3>
            </div>
            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm animate-fade-in-up" style="animation-delay: 0.3s">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Consolidation</p>
                <h3 class="text-3xl font-bold text-slate-900 font-outfit">Weekly</h3>
            </div>
        </section>

        <script>
            function quoteWidget() {
                return {
                    origin: '',
                    country_id: '',
                    region_id: '',
                    regions: [],
                    cbm: null,
                    cft: null,
                    email: '',
                    loading: false,
                    
                    updateRegions() {
                        const select = event.target;
                        const option = select.options[select.selectedIndex];
                        if (option.dataset.regions) {
                            this.regions = JSON.parse(option.dataset.regions);
                        } else {
                            this.regions = [];
                        }
                        this.region_id = '';
                    },
                    
                    convertToCft() {
                        if (this.cbm) {
                            this.cft = (this.cbm * 35.3147).toFixed(2);
                        } else {
                            this.cft = null;
                        }
                    },
                    
                    convertToCbm() {
                        if (this.cft) {
                            this.cbm = (this.cft / 35.3147).toFixed(2);
                        } else {
                            this.cbm = null;
                        }
                    },

                    async submitQuote() {
                        if (this.loading) return;
                        this.loading = true;

                        try {
                            const response = await axios.post('{{ route('leads.store') }}', {
                                email: this.email,
                                origin_id: this.origin,
                                country_id: this.country_id,
                                region_id: this.region_id,
                                volume: this.cft,
                                volume_unit: 'CFT'
                            }, {
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });

                            if (response.data.success) {
                                window.dispatchEvent(new CustomEvent('toast', {
                                    detail: { message: response.data.message, type: 'success' }
                                }));
                                
                                // Reset Form
                                this.email = '';
                                this.cbm = null;
                                this.cft = null;
                                this.origin = '';
                                this.country_id = '';
                                this.region_id = '';
                                this.regions = [];

                                if (response.data.redirect) {
                                    setTimeout(() => window.location.href = response.data.redirect, 1500);
                                }
                            }
                        } catch (error) {
                            console.error('Submission error:', error);
                            let message = 'Something went wrong. Please try again.';
                            
                            if (error.response && error.response.data && error.response.data.errors) {
                                message = Object.values(error.response.data.errors).flat()[0];
                            } else if (error.response && error.response.data && error.response.data.message) {
                                message = error.response.data.message;
                            }

                            window.dispatchEvent(new CustomEvent('toast', {
                                detail: { message: message, type: 'error' }
                            }));
                        } finally {
                            this.loading = false;
                        }
                    }
                }
            }
        </script>
    </body>
</html>
