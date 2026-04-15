<x-app-layout>
    <x-slot name="header">
        {{ __('Schedule Your Drop-off') }}
    </x-slot>

    <div class="max-w-xl mx-auto py-12">
        <div class="premium-card">
            <h3 class="text-xl font-bold text-white mb-2 font-outfit">Booking Confirmation</h3>
            <p class="text-xs text-slate-500 mb-6 uppercase tracking-widest">Quote Reference: Q-{{ str_pad($quote->id, 5, '0', STR_PAD_LEFT) }}</p>

            <form action="{{ route('bookings.store', $quote) }}" method="POST" class="space-y-6" x-data="bookingScheduler()">
                @csrf
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Drop-off Date</label>
                        <input type="date" name="drop_off_date" required 
                               x-model="date"
                               @change="validate()"
                               class="input-premium w-full" 
                               min="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Time</label>
                        <input type="time" name="drop_off_time" required 
                               x-model="time"
                               @change="validate()"
                               class="input-premium w-full">
                    </div>
                </div>

                <div class="p-4 bg-brand-500/5 rounded-xl border border-white/5 space-y-2">
                    <div class="flex items-center gap-2 text-brand-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-xs font-bold uppercase tracking-tight">Warehouse Hours</span>
                    </div>
                    <p class="text-xs text-slate-400">Mon – Fri: 9:00 AM – 6:00 PM</p>
                    <p class="text-[10px] text-slate-500 italic">Deliveries outside these hours require a "Special Request" flag.</p>
                </div>

                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" name="special_request" x-model="specialRequest" class="w-5 h-5 rounded border-white/10 bg-white/5 text-brand-600 focus:ring-brand-500">
                    <span class="text-xs text-slate-300 group-hover:text-white transition-colors">
                        This is a special request / off-hours delivery
                        <span x-show="autoFlagged" class="text-brand-400 font-bold ml-2">(Auto-flagged)</span>
                    </span>
                </label>

                <div class="pt-4">
                    <button type="submit" class="btn-primary w-full py-4 flex justify-center items-center gap-2">
                        Confirm Booking
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                    <a href="{{ route('quotes.index') }}" class="block text-center mt-4 text-xs text-slate-500 hover:text-white transition-colors">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <script>
        function bookingScheduler() {
            return {
                date: '',
                time: '09:00',
                specialRequest: false,
                autoFlagged: false,

                validate() {
                    if (!this.date || !this.time) return;

                    const selectedDate = new Date(this.date);
                    const day = selectedDate.getDay(); // 0 is Sunday, 6 is Saturday
                    const hour = parseInt(this.time.split(':')[0]);

                    const isWeekend = (day === 0 || day === 6);
                    const isOutsideHours = (hour < 9 || hour >= 18);

                    if (isWeekend || isOutsideHours) {
                        this.specialRequest = true;
                        this.autoFlagged = true;
                    } else {
                        this.autoFlagged = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
