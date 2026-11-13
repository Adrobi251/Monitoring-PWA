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

        <title>{{ isset($title) ? $title.' &middot; ' : '' }}{{ config('app.name', 'Konsultasi Akademik') }}</title>

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
        <div class="grid min-h-screen lg:grid-cols-2">
            <aside class="relative hidden flex-col justify-between overflow-hidden border-r border-border bg-secondary p-10 text-foreground lg:flex xl:p-14 dark:border-border/50 dark:bg-brand dark:text-brand-foreground">
                <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-foreground/5"></div>
                <div class="pointer-events-none absolute -bottom-32 -left-20 h-96 w-96 rounded-full bg-foreground/5"></div>

                <a href="{{ url('/') }}" class="relative flex items-center gap-3 rounded-lg focus-ring">
                    <x-brand-mark class="h-11 w-11 text-primary dark:text-brand-foreground" />
                    <span>
                        <span class="block text-sm font-semibold leading-tight">Konsultasi Akademik</span>
                        <span class="block text-xs text-muted-foreground dark:text-brand-foreground/70">Universitas Cokroaminoto Palopo</span>
                    </span>
                </a>

                <div class="relative max-w-md">
                    <h1 class="text-3xl font-semibold leading-tight xl:text-4xl">Konsultasi dengan dosen wali, lebih teratur dan terpantau.</h1>
                    <p class="mt-4 text-sm leading-relaxed text-muted-foreground dark:text-brand-foreground/80">Ajukan jadwal konsultasi, pantau status persetujuan, dan simpan catatan hasil bimbingan dalam satu tempat.</p>

                    <ul class="mt-8 space-y-3 text-sm text-muted-foreground dark:text-brand-foreground/90">
                        <li class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-foreground/10 dark:bg-white/15"><x-icon name="check" class="h-3.5 w-3.5" /></span>
                            Jadwal konsultasi dosen wali
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-foreground/10 dark:bg-white/15"><x-icon name="check" class="h-3.5 w-3.5" /></span>
                            Monitoring status pengajuan
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-foreground/10 dark:bg-white/15"><x-icon name="check" class="h-3.5 w-3.5" /></span>
                            Riwayat dan catatan konsultasi
                        </li>
                    </ul>
                </div>

                <p class="relative text-xs text-muted-foreground dark:text-brand-foreground/60">Sistem Informasi Layanan Konsultasi Akademik &middot; PWA</p>
            </aside>

            <main class="flex min-h-screen flex-col">
                <div class="mx-auto flex w-full max-w-md items-center justify-between px-4 py-4 sm:px-8 lg:px-0 lg:pt-8">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 lg:invisible">
                        <x-brand-mark class="h-9 w-9 text-primary" />
                        <span class="text-sm font-semibold text-foreground">Konsultasi Akademik</span>
                    </a>

                    <x-theme-toggle />
                </div>

                <div class="flex flex-1 items-center justify-center px-4 pb-8 sm:px-8 lg:px-0">
                    <div class="glass w-full max-w-md rounded-2xl p-6 sm:p-8">
                        {{ $slot }}
                    </div>
                </div>

                <p class="mx-auto w-full max-w-md px-4 pb-6 text-center text-xs text-muted-foreground sm:px-8 lg:px-0">
                    &copy; {{ date('Y') }} Sistem Informasi Layanan Konsultasi Akademik
                </p>
            </main>
        </div>

        <x-offline-banner />
    </body>
</html>
