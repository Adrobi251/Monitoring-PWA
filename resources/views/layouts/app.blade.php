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
        <meta name="theme-color" content="#0F172A">
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
        <div class="flex min-h-screen flex-col">
            <x-topbar />

            <main class="flex-1 py-6 pb-28 sm:py-8 lg:pb-8">
                <div class="page-container">
                    @isset($header)
                        <div class="mb-6">{{ $header }}</div>
                    @endisset

                    {{ $slot }}
                </div>
            </main>

            <footer class="border-t border-border py-4 pb-28 lg:pb-4">
                <div class="page-container flex flex-col items-center justify-between gap-1 text-xs text-muted-foreground sm:flex-row">
                    <p>&copy; {{ date('Y') }} Sistem Informasi Layanan Konsultasi Akademik</p>
                    <p>Universitas Cokroaminoto Palopo</p>
                </div>
            </footer>
        </div>

        <x-bottom-nav />

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
