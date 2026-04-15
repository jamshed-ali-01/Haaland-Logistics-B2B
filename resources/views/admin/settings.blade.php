<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight">
            {{ __('Global Operations Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Toast Notifications -->
            <x-toast />
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-6 md:p-8">
                    <div class="mb-8 border-b border-slate-100 pb-5">
                        <h3 class="text-lg font-bold text-slate-900 font-outfit">Core Pricing Variables</h3>
                        <p class="text-sm text-slate-500 mt-1">Configure the global baselines that impact all system-generated quotes.</p>
                    </div>

                    <form action="{{ route('admin.settings.update') }}" method="POST" class="max-w-xl">
                        @csrf
                        <div class="space-y-6">
                            
                            <!-- Origin Service Fee -->
                            <div class="p-5 bg-brand-50/50 border border-brand-100 rounded-xl relative overflow-hidden">
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-brand-500"></div>
                                <label for="origin_service_fee" class="block text-sm font-bold text-brand-900 mb-2 font-outfit flex items-center gap-2">
                                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Origin Service Fee
                                </label>
                                <p class="text-xs text-slate-500 mb-3 ml-6 leading-relaxed">This static fee is multiplied by the total billable CFT and automatically added as "Origin Handling" to every new shipping quote. (Default: $3.00)</p>
                                <div class="flex items-center gap-3 ml-6">
                                    <span class="text-slate-400 font-bold">$</span>
                                    <input type="number" step="0.01" name="origin_service_fee" id="origin_service_fee" 
                                        value="{{ old('origin_service_fee', $settings['origin_service_fee']->value ?? '3.00') }}" 
                                        class="input-premium w-32 font-mono text-lg text-slate-900" required>
                                    <span class="text-sm text-slate-500 font-bold">per CFT</span>
                                </div>
                                @error('origin_service_fee') <span class="text-xs text-accent-500 mt-1 block ml-6 font-bold">{{ $message }}</span> @enderror
                            </div>

                            <!-- Minimum Volume -->
                            <div class="p-5 bg-slate-50 border border-slate-200 rounded-xl">
                                <label for="minimum_volume" class="block text-sm font-semibold text-slate-700 mb-2 font-outfit">Global Minimum Required Volume</label>
                                <p class="text-xs text-slate-500 mb-3 leading-relaxed">If a client ships cargo smaller than this volume, the system automatically bills them for this exact baseline size instead. (Default: 100 CFT)</p>
                                <div class="flex items-center gap-3">
                                    <input type="number" step="0.01" name="minimum_volume" id="minimum_volume" 
                                        value="{{ old('minimum_volume', $settings['minimum_volume']->value ?? '100.00') }}" 
                                        class="input-premium w-32 font-mono text-slate-900" required>
                                    <span class="text-sm text-slate-500 font-bold">CFT</span>
                                </div>
                                @error('minimum_volume') <span class="text-xs text-accent-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="pt-4 flex justify-end">
                                <button type="submit" class="btn-primary shadow-lg shadow-brand-500/30 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Save System Settings
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
