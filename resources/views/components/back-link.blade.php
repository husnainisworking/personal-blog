@props([
    // Where to go if the user opened the page directly (no browser history) 
    'fallback' => route('home'),
    
    // Button text
    'label' => 'Back'
])

<button type="button"
        onclick="history.length > 1 ? history.back() : window.location.href='{{ $fallback }}'"
        class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800">
    <span aria-hidden="true">←</span>
    <span> {{ $label }}</span>
</button>