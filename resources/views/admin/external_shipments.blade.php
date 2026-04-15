<x-app-layout>
    <x-slot name="header">
        {{ __('External Shipments (Manual Entry)') }}
    </x-slot>

    <div class="grid lg:grid-cols-3 gap-8 animate-fade-in-up">
        <!-- Manual Entry Form -->
        <div class="lg:col-span-1">
            <div class="premium-card bg-gradient-to-br from-slate-900 to-brand-950 border-none shadow-2xl">
                <h3 class="text-xl font-bold text-white mb-6 font-outfit uppercase tracking-tight italic">Log External Shipment</h3>
                
                <form action="{{ route('admin.external-shipments.store') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Select Client</label>
                        <select name="user_id" required class="input-premium w-full text-sm">
                            <option value="">Choose User...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Ref / Tracking #</label>
                        <input type="text" name="external_reference" required placeholder="EXT123456" class="input-premium w-full">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Volume (CFT)</label>
                            <input type="number" step="0.01" name="external_volume_cft" required placeholder="0.00" class="input-premium w-full">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Received Date</label>
                            <input type="date" name="drop_off_date" required class="input-premium w-full text-xs">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Assign Departure (Optional)</label>
                        <select name="departure_id" class="input-premium w-full text-sm">
                            <option value="">Unassigned</option>
                            @foreach($departures as $dep)
                                <option value="{{ $dep->id }}">{{ $dep->vessel_name }} (#{{ $dep->voyage_number }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="btn-primary w-full py-4 flex justify-center items-center gap-2">
                            Add to Operations
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- External Shipments List -->
        <div class="lg:col-span-2">
            <div class="premium-card !p-0 overflow-hidden">
                <div class="p-6 border-b border-white/5">
                    <h3 class="text-lg font-bold text-white font-outfit">Logged Shipments</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white/2 text-slate-400 text-[10px] uppercase tracking-widest font-bold">
                                <th class="px-6 py-4">Ref / Tracking</th>
                                <th class="px-6 py-4">Client</th>
                                <th class="px-6 py-4">Volume</th>
                                <th class="px-6 py-4">Departure</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($bookings as $booking)
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-brand-400 uppercase tracking-tight">{{ $booking->external_reference }}</span>
                                            <span class="text-[10px] text-slate-500 uppercase italic">ID: {{ $booking->booking_number }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-medium text-slate-300">{{ $booking->user->name }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-white">{{ number_format($booking->external_volume_cft, 2) }} CFT</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($booking->departure)
                                            <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-tight">{{ $booking->departure->vessel_name }}</span>
                                        @else
                                            <span class="text-[10px] text-slate-600 uppercase italic">Awaiting Plan</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-md bg-white/5 text-slate-400 text-[10px] uppercase font-bold border border-white/5">
                                            {{ $booking->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                        No external shipments recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($bookings->hasPages())
                    <div class="px-6 py-4 border-t border-white/5">
                        {{ $bookings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
