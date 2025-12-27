import './bootstrap';
import './search';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

/**
 * Alpine "store" = global state you can access anywhere in Blade via:
 *     $store.theme.isDark
 *     $store.theme.toggle()
 */
Alpine.store('theme', {
    // This is the global state (true = dark mode on)
    isDark: false,

    /** 
     * Runs once on page load.
     * - checks localStorage first (user preference)
     * - if no saved preference, uses OS setting
     * - adds/remove the "dark" class on <html>
    */
    init() {
        const stored = localStorage.getItem('theme'); // 'dark' | 'light' | null
        const prefersDark =
            window.matchMedia &&
            window.matchMedia('(prefers-color-scheme: dark)').matches;

        this.isDark = stored ? stored === 'dark' : prefersDark;

        // Tailwind will apply dark styles when <html class="dark" exists
        document.documentElement.classList.toggle('dark', this.isDark);
    },
    /**
     * Set theme explicity + save it.
     * Call: $store.theme.set(true) or $store.theme.set(false)
     */
    set(isDark) {
        this.isDark = isDark;
        document.documentElement.classList.toggle('dark', isDark);
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    },

    /**
     * Flip theme.
     * Call: $store.theme.toggle()
     */
    toggle() {
        this.set(!this.isDark);
    },
});

Alpine.start();

// IMPORTANT: Initialize the store after Alpine starts
Alpine.store('theme').init();

