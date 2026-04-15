<x-app-layout>
    <x-slot name="header">
        {{ __('Departure Schedule') }}
    </x-slot>

    <div class="space-y-6" x-data="{ editingVessel: { id: '', vessel_name: '', voyage_number: '', cutoff_date: '', departure_date: '', arrival_date: '', capacity_cft: 0 } }">
        <!-- Actions Row -->
        <div class="flex justify-between items-center animate-fade-in-up">
            <div class="bg-white px-6 py-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-6">
                <div class="flex flex-col border-r border-slate-100 pr-6">
                    <span class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Planned Vessels</span>
                    <span class="text-xl font-bold text-slate-900 font-outfit">{{ $departures->total() }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Consolidation Rate</span>
                    <span class="text-xl font-bold text-brand-700 font-outfit">85% Avg</span>
                </div>
            </div>
            
            <button x-on:click.prevent="$dispatch('open-modal', 'add-vessel-modal')" class="btn-primary flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Publish New Vessel
            </button>
        </div>

        <!-- Departures Table -->
        <div class="premium-card !p-0 overflow-hidden animate-fade-in-up" style="animation-delay: 0.1s">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-900 font-outfit uppercase tracking-widest">Active Vessel Schedules</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-brand-900 text-white text-[10px] uppercase tracking-widest font-bold">
                            <th class="px-6 py-4">Vessel / Voyage</th>
                            <th class="px-6 py-4">Cut-off Date</th>
                            <th class="px-6 py-4">ETD / ETA</th>
                            <th class="px-6 py-4">Load Status</th>
                            <th class="px-8 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 italic">
                        @forelse($departures as $dep)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-brand-700/10 rounded-xl flex items-center justify-center text-brand-700 border border-brand-700/20 shadow-sm">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 4-8-4"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 uppercase">{{ $dep->vessel_name }}</p>
                                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Voyage: #{{ $dep->voyage_number }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="text-xs font-bold text-brand-700 uppercase tracking-tight">{{ $dep->cutoff_date->format('M d, Y') }}</span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-xs text-slate-800 font-bold">ETD: {{ $dep->departure_date->format('M d, Y') }}</span>
                                        <span class="text-xs text-slate-400">ETA: {{ $dep->arrival_date->format('M d, Y') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-col gap-1.5">
                                        <div class="flex justify-between text-[9px] uppercase font-bold">
                                            <span class="text-slate-500">Utilization: {{ $dep->utilization_percentage }}%</span>
                                            <span class="text-brand-700">Capacity: {{ number_format($dep->capacity_cft) }} CFT</span>
                                        </div>
                                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden border border-slate-200">
                                            <div class="bg-brand-700 h-full transition-all duration-500" :style="`width: {{ $dep->utilization_percentage }}%`" :class="{'bg-red-500': {{ $dep->utilization_percentage }} > 90, 'bg-amber-500': {{ $dep->utilization_percentage }} > 75 && {{ $dep->utilization_percentage }} <= 90}"></div>
                                        </div>
                                        @if($dep->utilization_percentage > 90)
                                            <span class="text-[8px] text-red-500 font-bold uppercase tracking-tighter">Critical: Near Capacity</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button 
                                            @click='
                                                (() => {
                                                    let data = {!! $dep->toJson() !!};
                                                    // Normalize dates for HTML date input (YYYY-MM-DD)
                                                    if (data.cutoff_date) data.cutoff_date = data.cutoff_date.split("T")[0];
                                                    if (data.departure_date) data.departure_date = data.departure_date.split("T")[0];
                                                    if (data.arrival_date) data.arrival_date = data.arrival_date.split("T")[0];
                                                    editingVessel = data;
                                                })();
                                                $dispatch("open-modal", "edit-vessel-modal")
                                            '
                                            class="bg-brand-700 text-white p-2 rounded text-[10px] font-bold transition-all uppercase tracking-widest shadow-lg shadow-brand-700/20">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <form action="{{ route('admin.departures.destroy', $dep) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this vessel departure?');">
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
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    No vessel departures scheduled.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($departures->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                    {{ $departures->links() }}
                </div>
            @endif
        </div>


    <!-- Publish Vessel Modal -->
    <x-modal name="add-vessel-modal" :show="$errors->any()" focusable>
        <form action="{{ route('admin.departures.store') }}" method="POST" class="p-8">
            @csrf
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-slate-900 font-outfit uppercase tracking-tight">Publish New Vessel Voyage</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="vessel_name" value="Vessel Name" />
                        <x-text-input id="vessel_name" name="vessel_name" type="text" class="mt-1 block w-full uppercase" required placeholder="e.g. EVER GIVEN" />
                    </div>
                    <div>
                        <x-input-label for="voyage_number" value="Voyage Number" />
                        <x-text-input id="voyage_number" name="voyage_number" type="text" class="mt-1 block w-full uppercase" required placeholder="e.g. V-204" />
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-6">
                    <div>
                        <x-input-label for="cutoff_date" value="Cut-off Date" />
                        <x-text-input id="cutoff_date" name="cutoff_date" type="date" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <x-input-label for="departure_date" value="ETD (Departure)" />
                        <x-text-input id="departure_date" name="departure_date" type="date" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <x-input-label for="arrival_date" value="ETA (Arrival)" />
                        <x-text-input id="arrival_date" name="arrival_date" type="date" class="mt-1 block w-full" required />
                    </div>
                </div>

                <div>
                    <x-input-label for="capacity_cft" value="Vessel Capacity (Total CFT)" />
                    <x-text-input id="capacity_cft" name="capacity_cft" type="number" class="mt-1 block w-full" required placeholder="e.g. 10000" />
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                    <x-primary-button class="shadow-xl shadow-brand-700/20">Publish Vessel Schedule</x-primary-button>
                </div>
            </div>
        </form>
    </x-modal>

    <!-- Edit Vessel Modal -->
    <x-modal name="edit-vessel-modal" focusable>
        <form :action="editingVessel ? '/admin/departures/' + editingVessel.id : '#'" method="POST" class="p-8">
            @csrf
            @method('PATCH')
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-slate-900 font-outfit uppercase tracking-tight">Edit Vessel Schedule</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="edit_vessel_name" value="Vessel Name" />
                        <x-text-input id="edit_vessel_name" name="vessel_name" type="text" class="mt-1 block w-full uppercase" required x-model="editingVessel.vessel_name" />
                    </div>
                    <div>
                        <x-input-label for="edit_voyage_number" value="Voyage Number" />
                        <x-text-input id="edit_voyage_number" name="voyage_number" type="text" class="mt-1 block w-full uppercase" required x-model="editingVessel.voyage_number" />
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-6">
                    <div>
                        <x-input-label for="edit_cutoff_date" value="Cut-off Date" />
                        <x-text-input id="edit_cutoff_date" name="cutoff_date" type="date" class="mt-1 block w-full" required x-model="editingVessel.cutoff_date" />
                    </div>
                    <div>
                        <x-input-label for="edit_departure_date" value="ETD (Departure)" />
                        <x-text-input id="edit_departure_date" name="departure_date" type="date" class="mt-1 block w-full" required x-model="editingVessel.departure_date" />
                    </div>
                    <div>
                        <x-input-label for="edit_arrival_date" value="ETA (Arrival)" />
                        <x-text-input id="edit_arrival_date" name="arrival_date" type="date" class="mt-1 block w-full" required x-model="editingVessel.arrival_date" />
                    </div>
                </div>

                <div>
                    <x-input-label for="edit_capacity_cft" value="Vessel Capacity (Total CFT)" />
                    <x-text-input id="edit_capacity_cft" name="capacity_cft" type="number" class="mt-1 block w-full" required x-model="editingVessel.capacity_cft" />
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                    <x-primary-button class="shadow-xl shadow-brand-700/20">Update Vessel Schedule</x-primary-button>
                </div>
            </div>
        </form>
    </x-modal>
    </div>
</x-app-layout>
