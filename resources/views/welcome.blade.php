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
        <meta name="description" content="Sistem Informasi Layanan Konsultasi Akademik Mahasiswa berbasis PWA.">

        <title>{{ config('app.name', 'Konsultasi Akademik') }}</title>

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
        <a
            href="#konten"
            class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-surface focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:text-foreground focus:ring-2 focus:ring-ring"
        >
            Lewati ke konten
        </a>

        <header class="glass sticky top-0 z-30 border-x-0 border-t-0 border-b border-border">
            <div class="page-container flex h-16 items-center justify-between gap-4">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 rounded-lg focus-ring">
                    <x-brand-mark class="h-9 w-9 text-primary" />
                    <span class="text-sm font-semibold text-foreground">Konsultasi Akademik</span>
                </a>

                <nav class="hidden items-center gap-6 text-sm text-muted-foreground sm:flex" aria-label="Navigasi halaman">
                    <a href="#fitur" class="rounded-sm transition-colors hover:text-foreground focus-ring">Fitur</a>
                    <a href="#alur" class="rounded-sm transition-colors hover:text-foreground focus-ring">Alur</a>
                    <a href="#tentang" class="rounded-sm transition-colors hover:text-foreground focus-ring">Tentang</a>
                </nav>

                <div class="flex items-center gap-1.5">
                    <x-theme-toggle />
                    @auth
                        <x-button :href="route('dashboard')" size="sm">Dashboard</x-button>
                    @else
                        <x-button :href="route('login')" variant="ghost" size="sm">Masuk</x-button>
                        <x-button :href="route('register')" size="sm">Daftar</x-button>
                    @endauth
                </div>
            </div>
        </header>

        <main id="konten">
            <section class="border-b border-border">
                <div class="page-container grid items-center gap-10 py-16 sm:py-20 lg:grid-cols-2">
                    <div>
                        <h1 class="text-3xl font-semibold leading-tight tracking-tight text-foreground sm:text-4xl lg:text-5xl">
                            Konsultasi akademik dengan dosen wali, lebih teratur dan terpantau.
                        </h1>

                        <p class="mt-4 max-w-xl text-base leading-relaxed text-muted-foreground">
                            Ajukan jadwal konsultasi, pantau status persetujuan, dan simpan catatan hasil bimbingan akademik dalam satu sistem yang mudah digunakan.
                        </p>

                        <div class="mt-7 flex flex-col gap-2 sm:flex-row">
                            @auth
                                <x-button :href="route('dashboard')" size="lg">
                                    Buka Dashboard
                                    <x-icon name="arrow-right" class="h-4 w-4" />
                                </x-button>
                            @else
                                <x-button :href="route('login')" size="lg">
                                    Masuk ke Sistem
                                    <x-icon name="arrow-right" class="h-4 w-4" />
                                </x-button>
                                <x-button :href="route('register')" variant="secondary" size="lg">Daftar Mahasiswa</x-button>
                            @endauth
                        </div>

                        <dl class="mt-10 grid grid-cols-1 gap-4 border-t border-border pt-6 sm:grid-cols-3">
                            <div>
                                <dt class="text-xs text-muted-foreground">Peran pengguna</dt>
                                <dd class="mt-1 text-lg font-semibold text-foreground">Mahasiswa &amp; Dosen</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-muted-foreground">Status konsultasi</dt>
                                <dd class="mt-1 text-lg font-semibold text-foreground">Real-time</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-muted-foreground">Akses</dt>
                                <dd class="mt-1 text-lg font-semibold text-foreground">PWA</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="relative">
                        <div class="glass rounded-2xl p-4 sm:p-5">
                            <div class="flex items-center justify-between border-b border-border pb-3">
                                <p class="text-sm font-semibold text-foreground">Ringkasan Konsultasi</p>
                                <span class="rounded-full bg-primary/10 px-2.5 py-1 text-xs font-medium text-primary">Akademik</span>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <div class="rounded-lg bg-muted p-3">
                                    <p class="text-xs text-muted-foreground">Pengajuan</p>
                                    <p class="mt-1 text-xl font-semibold text-foreground">Terpantau</p>
                                </div>
                                <div class="rounded-lg bg-muted p-3">
                                    <p class="text-xs text-muted-foreground">Jadwal</p>
                                    <p class="mt-1 text-xl font-semibold text-foreground">Terjadwal</p>
                                </div>
                            </div>

                            <div class="mt-3 space-y-2">
                                <div class="flex items-center gap-3 rounded-lg border border-border px-3 py-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"><x-icon name="calendar" class="h-4 w-4" /></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-medium text-foreground">Jadwal konsultasi</p>
                                        <p class="text-xs text-muted-foreground">Dosen wali membuka slot waktu</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 rounded-lg border border-border px-3 py-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-warning/10 text-warning"><x-icon name="clock" class="h-4 w-4" /></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-medium text-foreground">Menunggu persetujuan</p>
                                        <p class="text-xs text-muted-foreground">Pengajuan ditinjau dosen wali</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 rounded-lg border border-border px-3 py-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-success/10 text-success"><x-icon name="check-circle" class="h-4 w-4" /></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-medium text-foreground">Catatan konsultasi</p>
                                        <p class="text-xs text-muted-foreground">Hasil bimbingan tersimpan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="fitur" class="border-b border-border py-16">
                <div class="page-container">
                    <div class="max-w-2xl">
                        <h2 class="text-2xl font-semibold tracking-tight text-foreground sm:text-3xl">Dirancang untuk alur konsultasi akademik.</h2>
                        <p class="mt-2 text-sm leading-relaxed text-muted-foreground">Setiap fitur mengikuti kebutuhan mahasiswa dan dosen wali, dari pengajuan hingga catatan hasil konsultasi.</p>
                    </div>

                    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <x-card>
                            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary"><x-icon name="calendar" class="h-5 w-5" /></span>
                            <h3 class="mt-4 text-sm font-semibold text-foreground">Jadwal Terpadu</h3>
                            <p class="mt-1 text-sm leading-relaxed text-muted-foreground">Dosen wali mengelola slot konsultasi, mahasiswa memilih jadwal yang tersedia.</p>
                        </x-card>
                        <x-card>
                            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning/10 text-warning"><x-icon name="clock" class="h-5 w-5" /></span>
                            <h3 class="mt-4 text-sm font-semibold text-foreground">Monitoring Status</h3>
                            <p class="mt-1 text-sm leading-relaxed text-muted-foreground">Pantau status pengajuan: menunggu, disetujui, ditolak, hingga selesai.</p>
                        </x-card>
                        <x-card>
                            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-success/10 text-success"><x-icon name="chat" class="h-5 w-5" /></span>
                            <h3 class="mt-4 text-sm font-semibold text-foreground">Catatan &amp; Riwayat</h3>
                            <p class="mt-1 text-sm leading-relaxed text-muted-foreground">Hasil konsultasi terdokumentasi dan dapat diakses kembali kapan saja.</p>
                        </x-card>
                    </div>
                </div>
            </section>

            <section id="alur" class="border-b border-border py-16">
                <div class="page-container">
                    <div class="max-w-2xl">
                        <h2 class="text-2xl font-semibold tracking-tight text-foreground sm:text-3xl">Tiga langkah sederhana.</h2>
                    </div>

                    <ol class="mt-8 grid gap-4 sm:grid-cols-3">
                        <li class="glass-lite rounded-xl p-5">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-sm font-semibold text-primary-foreground">1</span>
                            <h3 class="mt-4 text-sm font-semibold text-foreground">Pilih jadwal</h3>
                            <p class="mt-1 text-sm text-muted-foreground">Lihat slot konsultasi yang dibuka dosen wali.</p>
                        </li>
                        <li class="glass-lite rounded-xl p-5">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-sm font-semibold text-primary-foreground">2</span>
                            <h3 class="mt-4 text-sm font-semibold text-foreground">Kirim pengajuan</h3>
                            <p class="mt-1 text-sm text-muted-foreground">Isi topik dan deskripsi konsultasi yang dibutuhkan.</p>
                        </li>
                        <li class="glass-lite rounded-xl p-5">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-sm font-semibold text-primary-foreground">3</span>
                            <h3 class="mt-4 text-sm font-semibold text-foreground">Pantau hasil</h3>
                            <p class="mt-1 text-sm text-muted-foreground">Ikuti status dan baca catatan hasil konsultasi.</p>
                        </li>
                    </ol>
                </div>
            </section>

            <section id="tentang" class="py-16">
                <div class="page-container">
                    <div class="rounded-2xl bg-brand px-6 py-10 text-brand-foreground sm:px-10">
                        <div class="flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
                            <div class="max-w-xl">
                                <h2 class="text-2xl font-semibold tracking-tight sm:text-3xl">Mulai konsultasi akademik Anda.</h2>
                                <p class="mt-2 text-sm leading-relaxed text-brand-foreground/80">Masuk menggunakan akun yang telah terdaftar untuk mengakses jadwal dan pengajuan konsultasi.</p>
                            </div>
                            <div class="flex flex-col gap-2 sm:flex-row">
                                @auth
                                    <x-button :href="route('dashboard')" variant="secondary" size="lg">Buka Dashboard</x-button>
                                @else
                                    <x-button :href="route('login')" variant="secondary" size="lg">Masuk</x-button>
                                    <x-button :href="route('register')" variant="secondary" size="lg">Daftar</x-button>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-border py-6">
            <div class="page-container flex flex-col items-center justify-between gap-2 text-xs text-muted-foreground sm:flex-row">
                <p>&copy; {{ date('Y') }} Sistem Informasi Layanan Konsultasi Akademik</p>
                <p>Universitas Cokroaminoto Palopo</p>
            </div>
        </footer>

        <x-offline-banner />
    </body>
</html>
