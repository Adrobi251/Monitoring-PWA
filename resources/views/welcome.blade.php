<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-background via-background to-primary/5">
        <header class="border-b border-border/60 bg-card/70 backdrop-blur">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-application-logo class="h-9 w-9 text-primary" />
                    <span class="font-serif font-semibold text-xl text-foreground">Konsultasi Layanan</span>
                </div>
                <nav class="flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-muted-foreground hover:text-foreground hover:bg-muted transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition">
                            Daftar
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-semibold uppercase tracking-wide">
                            Konsultasi Akademik
                        </span>
                        <h1 class="mt-5 font-serif text-4xl sm:text-5xl lg:text-6xl font-semibold leading-tight text-foreground">
                            Konsultasi dengan Dosen Wali, lebih mudah.
                        </h1>
                        <p class="mt-6 text-lg text-muted-foreground leading-relaxed">
                            Ajukan jadwal, pantau status, dan simpan catatan konsultasi akademik dalam satu platform yang aman untuk mahasiswa dan dosen.
                        </p>
                        <div class="mt-8 flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('register') }}" class="inline-flex justify-center items-center px-6 py-3 rounded-xl bg-primary text-white font-medium hover:bg-primary/90 transition shadow-lg shadow-primary/20">
                                Mulai Sebagai Mahasiswa
                            </a>
                            <a href="{{ route('login') }}" class="inline-flex justify-center items-center px-6 py-3 rounded-xl bg-card border border-border text-foreground font-medium hover:bg-muted transition">
                                Masuk Akun
                            </a>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute -inset-4 bg-gradient-to-tr from-primary/20 via-secondary/10 to-accent/20 rounded-3xl blur-2xl"></div>
                        <div class="relative bg-card border border-border rounded-3xl shadow-2xl p-6">
                            <div class="flex items-center justify-between mb-5">
                                <div>
                                    <p class="text-sm text-muted-foreground">Jadwal berikutnya</p>
                                    <h2 class="font-serif text-xl font-semibold text-foreground">Konsultasi Akademik</h2>
                                </div>
                                <span class="px-3 py-1 rounded-full bg-accent/10 text-accent text-sm font-medium">Disetujui</span>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center gap-4 p-4 rounded-2xl bg-muted border border-border">
                                    <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-semibold">R</div>
                                    <div>
                                        <p class="font-medium text-foreground">Review KRS &amp; IPK</p>
                                        <p class="text-sm text-muted-foreground">Senin, 09.00 - 10.30 · Ruang 204</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 p-4 rounded-2xl bg-card border border-border">
                                    <div class="w-12 h-12 rounded-xl bg-accent/10 text-accent flex items-center justify-center font-semibold">S</div>
                                    <div>
                                        <p class="font-medium text-foreground">Skripsi Chapter 2</p>
                                        <p class="text-sm text-muted-foreground">Rabu, 13.00 - 14.30 · Online</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-card border-y border-border">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="p-6 rounded-2xl bg-muted border border-border">
                            <div class="w-11 h-11 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <h3 class="font-serif text-lg font-semibold text-foreground">Jadwal Terstruktur</h3>
                            <p class="mt-2 text-sm text-muted-foreground">Buat dan kelola slot konsultasi akademik tanpa bentrok.</p>
                        </div>
                        <div class="p-6 rounded-2xl bg-muted border border-border">
                            <div class="w-11 h-11 rounded-xl bg-accent/10 text-accent flex items-center justify-center mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="font-serif text-lg font-semibold text-foreground">Status Jelas</h3>
                            <p class="mt-2 text-sm text-muted-foreground">Pantau pengajuan dari menunggu, disetujui, ditolak, hingga selesai.</p>
                        </div>
                        <div class="p-6 rounded-2xl bg-muted border border-border">
                            <div class="w-11 h-11 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <h3 class="font-serif text-lg font-semibold text-foreground">Catatan Tersimpan</h3>
                            <p class="mt-2 text-sm text-muted-foreground">Simpan ringkasan, arahan, dan tindak lanjut setiap konsultasi.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="rounded-3xl bg-primary text-white p-8 sm:p-12 text-center shadow-xl">
                    <h2 class="font-serif text-3xl sm:text-4xl font-semibold">Siap memulai konsultasi?</h2>
                    <p class="mt-3 text-white/80">Daftar sebagai mahasiswa atau masuk untuk melihat dashboard Anda.</p>
                    <div class="mt-6 flex flex-col sm:flex-row justify-center gap-3">
                        <a href="{{ route('register') }}" class="inline-flex justify-center items-center px-6 py-3 rounded-xl bg-white text-primary font-medium hover:bg-background transition">
                            Daftar Sekarang
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex justify-center items-center px-6 py-3 rounded-xl border border-white/40 text-white font-medium hover:bg-white/10 transition">
                            Masuk
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-border bg-card/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-muted-foreground flex flex-col sm:flex-row items-center justify-between gap-2">
                <span>{{ config('app.name', 'Laravel') }}</span>
                <span>Sistem Konsultasi Akademik Mahasiswa &amp; Dosen Wali</span>
            </div>
        </footer>
    </div>
</x-guest-layout>
