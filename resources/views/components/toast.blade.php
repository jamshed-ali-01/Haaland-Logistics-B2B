<div x-data="{ 
        messages: [],
        remove(id) {
            this.messages = this.messages.filter(m => m.id !== id)
        },
        add(message, type = 'success') {
            const id = Date.now()
            this.messages.push({ id, message, type })
            setTimeout(() => this.remove(id), 5000)
        }
    }"
    @toast.window="add($event.detail.message, $event.detail.type)"
    class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none w-full max-w-sm">
    
    <template x-for="m in messages" :key="m.id">
        <div x-show="true"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-y-[-20px] opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-200 opacity-0"
             :class="{
                'bg-emerald-600 shadow-emerald-500/20': m.type === 'success',
                'bg-red-600 shadow-red-500/20': m.type === 'error',
                'bg-amber-600 shadow-amber-500/20': m.type === 'warning',
                'bg-brand-700 shadow-brand-700/20': m.type === 'info'
             }"
             class="pointer-events-auto flex items-center gap-3 px-5 py-4 rounded-2xl text-white shadow-2xl font-bold font-outfit uppercase tracking-widest text-[10px] min-w-[280px]">
            
            <template x-if="m.type === 'success'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </template>
            <template x-if="m.type === 'error'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </template>
            <template x-if="m.type === 'warning'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </template>

            <span x-text="m.message"></span>

            <button @click="remove(m.id)" class="ml-auto opacity-70 hover:opacity-100 transition-opacity">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>

    {{-- Initial Flash Messages Hook --}}
    <div x-init="
        @if(session('success')) 
            $nextTick(() => add('{{ session('success') }}', 'success'))
        @endif
        @if(session('error')) 
            $nextTick(() => add('{{ session('error') }}', 'error'))
        @endif
        @if(session('status')) 
            $nextTick(() => add('{{ session('status') }}', 'info'))
        @endif
    "></div>
</div>
