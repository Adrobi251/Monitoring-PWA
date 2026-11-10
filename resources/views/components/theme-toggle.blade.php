<button
    type="button"
    x-data
    x-on:click="
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    "
    {{ $attributes->merge(['class' => 'inline-flex h-10 w-10 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground focus-ring']) }}
    aria-label="Ganti mode terang atau gelap"
    title="Ganti tema"
>
    <x-icon name="sun" class="h-5 w-5 dark:hidden" />
    <x-icon name="moon" class="hidden h-5 w-5 dark:block" />
</button>
