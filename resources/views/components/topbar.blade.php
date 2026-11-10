<header class="sticky top-0 z-30 border-b border-border bg-surface/90 backdrop-blur">
    <div class="flex h-16 items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-2">
            <button
                type="button"
                x-on:click="$dispatch('toggle-sidebar')"
                class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground focus-ring lg:hidden"
                aria-label="Buka menu navigasi"
            >
                <x-icon name="menu" class="h-5 w-5" />
            </button>

            <a href="{{ auth()->user()->role === 'dosen' ? route('dosen.dashboard') : route('mahasiswa.dashboard') }}" class="flex min-w-0 items-center gap-2 lg:hidden">
                <x-brand-mark class="h-8 w-8 text-primary" />
                <span class="truncate text-sm font-semibold text-foreground">Konsultasi Akademik</span>
            </a>

            <p class="hidden text-sm text-muted-foreground lg:block">
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
        </div>

        <div class="flex items-center gap-1 sm:gap-1.5">
            <x-theme-toggle />
            <x-notification-menu />
            <span class="mx-1 hidden h-6 w-px bg-border sm:block"></span>
            <x-user-menu />
        </div>
    </div>
</header>
