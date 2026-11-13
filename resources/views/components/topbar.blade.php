<header class="sticky top-0 z-40 px-3 pt-3 sm:px-4 lg:px-6 lg:pt-4">
    <div class="glass mx-auto flex h-14 max-w-5xl items-center gap-2 rounded-full pl-2.5 pr-1.5 shadow-pop lg:gap-3 lg:pl-3">
        <a href="{{ route(auth()->user()->role === 'dosen' ? 'dosen.dashboard' : 'mahasiswa.dashboard') }}" class="flex shrink-0 items-center gap-2 rounded-full focus-ring">
            <x-brand-mark class="h-7 w-7 text-primary" />
            <span class="hidden whitespace-nowrap text-sm font-semibold text-foreground sm:block lg:hidden xl:block">Konsultasi Akademik</span>
        </a>

        <nav class="hidden flex-1 justify-center lg:flex" aria-label="Navigasi utama">
            <x-primary-nav />
        </nav>

        <div class="ml-auto flex items-center gap-0.5 sm:gap-1 lg:ml-0">
            <x-theme-toggle />
            <x-notification-menu />
            <x-user-menu />
        </div>
    </div>
</header>
