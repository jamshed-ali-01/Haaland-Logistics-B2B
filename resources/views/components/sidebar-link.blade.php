@props(['active', 'icon', 'disabled' => false])

@php
$baseClasses = 'flex items-center gap-4 px-4 py-3.5 transition-all rounded-r-2xl no-underline font-medium relative ';
if ($disabled) {
    $classes = $baseClasses . 'opacity-50 cursor-not-allowed text-slate-500 hover:bg-transparent border-l-4 border-transparent';
} else {
    $classes = ($active ?? false)
                ? $baseClasses . 'bg-brand-800 text-white font-bold border-l-4 border-accent-500 shadow-lg'
                : $baseClasses . 'text-slate-300 hover:text-white hover:bg-white/5 border-l-4 border-transparent hover:border-white/10';
}
@endphp

<a @if($disabled) onclick="event.preventDefault(); window.dispatchEvent(new CustomEvent('toast', {detail: {message: 'Account verification required to access this feature', type: 'warning'}}))" @endif
   {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path>
        </svg>
    @endif
    <span class="text-sm tracking-tight flex-1">{{ $slot }}</span>
    
    @if($disabled)
        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2zm10-10V7a4 4 0 0 0-8 0v4h8z"/></svg>
    @endif
</a>
