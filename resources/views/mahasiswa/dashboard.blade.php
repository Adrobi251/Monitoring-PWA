@php
    $hour = now()->hour;
    $greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 19 ? 'Selamat sore' : 'Selamat malam'));
    $firstName = explode(' ', trim($user->name))[0];
    $terbaru = $riwayatTerbaru->first();
@endphp

<x-app-layout>
    <x-page-header
        :title="$greeting.', '.$firstName"
        subtitle="Berikut ringkasan aktivitas konsultasi akademik Anda hari ini."
    >
        <x-slot:actions>
            <x-button :href="route('mahasiswa.pengajuan.create')">
                <x-icon name="plus" class="h-4 w-4" />
                Ajukan Konsultasi
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
        <x-stat-card label="Total Pengajuan" :value="$stats['total_pengajuan']" tone="primary">
            <x-slot:icon><x-icon name="document" class="h-5 w-5" /></x-slot:icon>
        </x-stat-card>
        <x-stat-card label="Menunggu" :value="$stats['pending']" tone="warning">
            <x-slot:icon><x-icon name="clock" class="h-5 w-5" /></x-slot:icon>
        </x-stat-card>
        <x-stat-card label="Disetujui" :value="$stats['approved']" tone="success">
            <x-slot:icon><x-icon name="check-circle" class="h-5 w-5" /></x-slot:icon>
        </x-stat-card>
        <x-stat-card label="Selesai" :value="$stats['completed']" tone="neutral">
            <x-slot:icon><x-icon name="academic-cap" class="h-5 w-5" /></x-slot:icon>
        </x-stat-card>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-card :padding="false">
                <x-slot:header>
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-sm font-semibold text-foreground">Jadwal Konsultasi Terdekat</h2>
                            <p class="mt-0.5 text-xs text-muted-foreground">Jadwal yang sudah disetujui dosen wali</p>
                        </div>
                        <x-icon name="calendar" class="h-5 w-5 text-muted-foreground" />
                    </div>
                </x-slot:header>

                @if ($jadwalTerdekat && $jadwalTerdekat->jadwal)
                    <div class="p-5 sm:p-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-start gap-4">
                                <div class="flex h-16 w-16 shrink-0 flex-col items-center justify-center rounded-xl bg-primary/10 text-primary">
                                    <span class="text-xl font-semibold leading-none">{{ $jadwalTerdekat->jadwal->tanggal->format('d') }}</span>
                                    <span class="mt-1 text-[10px] font-semibold uppercase tracking-wide">{{ $jadwalTerdekat->jadwal->tanggal->translatedFormat('M') }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-foreground">{{ $jadwalTerdekat->jadwal->tanggal->translatedFormat('l, d F Y') }}</p>
                                    <p class="mt-1 flex items-center gap-1.5 text-sm text-muted-foreground">
                                        <x-icon name="clock" class="h-4 w-4" />
                                        {{ $jadwalTerdekat->jadwal->jam_mulai }}–{{ $jadwalTerdekat->jadwal->jam_selesai }}
                                    </p>
                                    <p class="mt-1 text-sm text-muted-foreground">
                                        {{ $jadwalTerdekat->jadwal->lokasi ?? 'Lokasi belum ditentukan' }}
                                    </p>
                                </div>
                            </div>

                            <x-button :href="route('mahasiswa.pengajuan.show', $jadwalTerdekat)" variant="secondary" size="sm" class="shrink-0">
                                Detail
                                <x-icon name="arrow-right" class="h-4 w-4" />
                            </x-button>
                        </div>
                    </div>
                @else
                    <x-empty-state
                        icon="calendar"
                        title="Belum ada jadwal terdekat"
                        description="Ajukan konsultasi pada jadwal dosen wali yang tersedia."
                    >
                        <x-slot:action>
                            <x-button :href="route('mahasiswa.jadwal')" size="sm">Lihat Jadwal</x-button>
                        </x-slot:action>
                    </x-empty-state>
                @endif
            </x-card>

            <x-card :padding="false">
                <x-slot:header>
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-sm font-semibold text-foreground">Aktivitas Terbaru</h2>
                        <a href="{{ route('mahasiswa.riwayat') }}" class="text-xs font-medium text-primary hover:text-primary-dark">Lihat semua</a>
                    </div>
                </x-slot:header>

                @if ($riwayatTerbaru->isEmpty())
                    <x-empty-state
                        icon="clipboard"
                        title="Belum ada aktivitas"
                        description="Pengajuan konsultasi Anda akan muncul di sini."
                    />
                @else
                    <ul class="divide-y divide-border">
                        @foreach ($riwayatTerbaru as $item)
                            <li>
                                <a href="{{ route('mahasiswa.pengajuan.show', $item) }}" class="flex items-center gap-3 px-5 py-4 transition-colors hover:bg-muted sm:px-6">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-muted text-muted-foreground">
                                        <x-icon name="document" class="h-4 w-4" />
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-medium text-foreground">{{ $item->topik }}</span>
                                        <span class="mt-0.5 block text-xs text-muted-foreground">
                                            {{ $item->jadwal?->tanggal?->translatedFormat('d M Y') ?? $item->created_at->translatedFormat('d M Y') }}
                                        </span>
                                    </span>
                                    <x-status-badge :status="$item->status" />
                                    <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-muted-foreground" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card>
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-foreground">Dosen Wali</h2>
                    <x-icon name="academic-cap" class="h-5 w-5 text-muted-foreground" />
                </div>

                @if ($dosenWali)
                    <div class="mt-4 flex items-center gap-3">
                        <x-avatar :name="$dosenWali->name" size="lg" />
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-foreground">{{ $dosenWali->name }}</p>
                            <p class="text-xs text-muted-foreground">NIDN {{ $dosenWali->nidn ?? '-' }}</p>
                        </div>
                    </div>

                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-muted-foreground">Email</dt>
                            <dd class="truncate font-medium text-foreground">{{ $dosenWali->email }}</dd>
                        </div>
                    </dl>

                    <x-button :href="route('mahasiswa.dosen-wali')" variant="secondary" size="sm" class="mt-4 w-full">
                        Informasi Dosen Wali
                    </x-button>
                @else
                    <p class="mt-4 text-sm text-muted-foreground">Anda belum memiliki dosen wali. Hubungi administrator akademik.</p>
                @endif
            </x-card>

            <x-card>
                <h2 class="text-sm font-semibold text-foreground">Status Pengajuan Terbaru</h2>

                @if ($terbaru)
                    <div class="mt-4">
                        <div class="flex items-start justify-between gap-3">
                            <p class="min-w-0 text-sm font-medium text-foreground">{{ $terbaru->topik }}</p>
                            <x-status-badge :status="$terbaru->status" class="shrink-0" />
                        </div>
                        <p class="mt-1 text-xs text-muted-foreground">Diajukan {{ $terbaru->created_at->diffForHumans() }}</p>

                        @if ($terbaru->status === 'rejected' && $terbaru->alasan_penolakan)
                            <p class="mt-3 rounded-lg bg-danger/10 px-3 py-2 text-xs text-danger">{{ $terbaru->alasan_penolakan }}</p>
                        @endif

                        <x-button :href="route('mahasiswa.pengajuan.show', $terbaru)" variant="ghost" size="sm" class="mt-3 w-full justify-between">
                            Lihat detail
                            <x-icon name="arrow-right" class="h-4 w-4" />
                        </x-button>
                    </div>
                @else
                    <p class="mt-4 text-sm text-muted-foreground">Belum ada pengajuan konsultasi.</p>
                @endif
            </x-card>

            <x-card>
                <h2 class="text-sm font-semibold text-foreground">Aksi Cepat</h2>
                <div class="mt-4 space-y-2">
                    <x-button :href="route('mahasiswa.pengajuan.create')" class="w-full justify-start">
                        <x-icon name="plus" class="h-4 w-4" />
                        Ajukan Konsultasi
                    </x-button>
                    <x-button :href="route('mahasiswa.jadwal')" variant="secondary" class="w-full justify-start">
                        <x-icon name="calendar" class="h-4 w-4" />
                        Lihat Jadwal
                    </x-button>
                    <x-button :href="route('mahasiswa.riwayat')" variant="ghost" class="w-full justify-start">
                        <x-icon name="clipboard" class="h-4 w-4" />
                        Riwayat Konsultasi
                    </x-button>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
