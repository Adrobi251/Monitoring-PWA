<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="manifest" href="/manifest.webmanifest">
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <meta name="apple-mobile-web-app-title" content="Konsultasi">
        <meta name="theme-color" content="#176B52">
        <meta name="mobile-web-app-capable" content="yes">

        <title>{{ isset($title) ? $title.' · ' : '' }}{{ config('app.name', 'Konsultasi Akademik') }}</title>

        <script>
            (function () {
                const stored = localStorage.getItem('theme');
                const dark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.classList.toggle('dark', dark);
            })();
        </script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-background font-sans text-foreground antialiased">
        <div x-data="{ sidebarOpen: false }" x-on:toggle-sidebar.window="sidebarOpen = true" x-on:keydown.escape.window="sidebarOpen = false" class="min-h-screen lg:pl-64">
            <x-sidebar />

            <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true" aria-label="Menu navigasi">
                <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-foreground/40 backdrop-blur-sm" x-on:click="sidebarOpen = false"></div>

                <div
                    x-show="sidebarOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="-translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="-translate-x-full"
                    x-on:click="if ($event.target.closest('a')) sidebarOpen = false"
                    class="fixed inset-y-0 left-0 flex w-72 max-w-[85vw] flex-col border-r border-border bg-surface shadow-pop"
                >
                    <div class="flex justify-end px-3 pt-3">
                        <button type="button" x-on:click="sidebarOpen = false" class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-muted-foreground hover:bg-muted hover:text-foreground focus-ring" aria-label="Tutup menu">
                            <x-icon name="x" class="h-5 w-5" />
                        </button>
                    </div>
                    <x-sidebar-nav />
                </div>
            </div>

            <div class="flex min-h-screen flex-col">
                <x-topbar />

                <main class="flex-1 py-6 sm:py-8">
                    <div class="page-container">
                        @isset($header)
                            <div class="mb-6">{{ $header }}</div>
                        @endisset

                        {{ $slot }}
                    </div>
                </main>

                <footer class="border-t border-border py-4">
                    <div class="page-container flex flex-col items-center justify-between gap-1 text-xs text-muted-foreground sm:flex-row">
                        <p>&copy; {{ date('Y') }} Sistem Informasi Layanan Konsultasi Akademik</p>
                        <p>Universitas Cokroaminoto Palopo</p>
                    </div>
                </footer>
            </div>
        </div>

        <div class="pointer-events-none fixed inset-x-4 top-20 z-50 flex flex-col items-end gap-2 sm:inset-x-auto sm:right-6 sm:max-w-sm">
            @if (session('success'))
                <div class="pointer-events-auto w-full"><x-toast type="success" :message="session('success')" /></div>
            @endif

            @if (session('error'))
                <div class="pointer-events-auto w-full"><x-toast type="error" :message="session('error')" /></div>
            @endif

            @if ($errors->any())
                <div class="pointer-events-auto w-full"><x-toast type="error" :message="$errors->first()" /></div>
            @endif
        </div>

        <x-offline-banner />
    </body>
</html>
