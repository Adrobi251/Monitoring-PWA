@php
    $hour = now()->hour;
    $greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 19 ? 'Selamat sore' : 'Selamat malam'));
    $firstName = explode(' ', trim($user->name))[0];
@endphp

<x-app-layout>
    <x-page-header
        :title="$greeting.', '.$firstName"
        subtitle="Ringkasan bimbingan akademik dan pengajuan konsultasi mahasiswa Anda."
    >
        <x-slot:actions>
            <x-button :href="route('dosen.jadwal.create')">
                <x-icon name="plus" class="h-4 w-4" />
                Tambah Jadwal
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
        <x-stat-card label="Mahasiswa Bimbingan" :value="$stats['total_mahasiswa']" tone="primary">
            <x-slot:icon><x-icon name="users" class="h-5 w-5" /></x-slot:icon>
        </x-stat-card>
        <x-stat-card label="Total Pengajuan" :value="$stats['total_pengajuan']" tone="neutral">
            <x-slot:icon><x-icon name="document" class="h-5 w-5" /></x-slot:icon>
        </x-stat-card>
        <x-stat-card label="Menunggu" :value="$stats['pending']" tone="warning">
            <x-slot:icon><x-icon name="clock" class="h-5 w-5" /></x-slot:icon>
        </x-stat-card>
        <x-stat-card label="Selesai" :value="$stats['completed']" tone="success">
            <x-slot:icon><x-icon name="check-circle" class="h-5 w-5" /></x-slot:icon>
        </x-stat-card>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-card :padding="false">
                <x-slot:header>
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-sm font-semibold text-foreground">Jadwal Konsultasi Mendatang</h2>
                            <p class="mt-0.5 text-xs text-muted-foreground">Jadwal aktif yang akan datang</p>
                        </div>
                        <a href="{{ route('dosen.jadwal') }}" class="text-xs font-medium text-primary hover:text-primary-dark">Kelola</a>
                    </div>
                </x-slot:header>

                @if ($jadwalMendatang->isEmpty())
                    <x-empty-state
                        icon="calendar"
                        title="Belum ada jadwal mendatang"
                        description="Tambahkan jadwal agar mahasiswa bimbingan dapat mengajukan konsultasi."
                    >
                        <x-slot:action>
                            <x-button :href="route('dosen.jadwal.create')" size="sm">Tambah Jadwal</x-button>
                        </x-slot:action>
                    </x-empty-state>
                @else
                    <ul class="divide-y divide-border">
                        @foreach ($jadwalMendatang as $item)
                            <li class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-lg bg-primary/10 text-primary">
                                        <span class="text-base font-semibold leading-none">{{ $item->tanggal->format('d') }}</span>
                                        <span class="mt-0.5 text-[10px] font-medium uppercase">{{ $item->tanggal->translatedFormat('M') }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-foreground">{{ $item->tanggal->translatedFormat('l, d F Y') }}</p>
                                        <p class="mt-0.5 text-xs text-muted-foreground">
                                            {{ $item->jam_mulai }}–{{ $item->jam_selesai }}
                                            @if ($item->lokasi)
                                                &middot; {{ $item->lokasi }}
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 sm:shrink-0">
                                    @if ($item->terisi_count > 0)
                                        <span class="rounded-full bg-warning/10 px-2.5 py-1 text-xs font-medium text-warning ring-1 ring-inset ring-warning/25">Terisi</span>
                                    @else
                                        <span class="rounded-full bg-success/10 px-2.5 py-1 text-xs font-medium text-success ring-1 ring-inset ring-success/25">Tersedia</span>
                                    @endif
                                    <x-button :href="route('dosen.jadwal.edit', $item)" variant="ghost" size="sm">Edit</x-button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-card>

            <x-card :padding="false">
                <x-slot:header>
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-sm font-semibold text-foreground">Pengajuan Terbaru</h2>
                        <a href="{{ route('dosen.riwayat') }}" class="text-xs font-medium text-primary hover:text-primary-dark">Lihat semua</a>
                    </div>
                </x-slot:header>

                @if ($riwayatTerbaru->isEmpty())
                    <x-empty-state
                        icon="clipboard"
                        title="Belum ada pengajuan"
                        description="Pengajuan konsultasi dari mahasiswa bimbingan akan muncul di sini."
                    />
                @else
                    <ul class="divide-y divide-border">
                        @foreach ($riwayatTerbaru as $item)
                            <li>
                                <a href="{{ route('dosen.riwayat.show', $item) }}" class="flex items-center gap-3 px-5 py-4 transition-colors hover:bg-muted sm:px-6">
                                    <x-avatar :name="$item->mahasiswa->name ?? '?'" size="sm" />
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-medium text-foreground">{{ $item->topik }}</span>
                                        <span class="mt-0.5 block text-xs text-muted-foreground">{{ $item->mahasiswa->name ?? '-' }} &middot; {{ $item->created_at->diffForHumans() }}</span>
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
                <h2 class="text-sm font-semibold text-foreground">Aksi Cepat</h2>
                <div class="mt-4 space-y-2">
                    <x-button :href="route('dosen.jadwal.create')" class="w-full justify-start">
                        <x-icon name="plus" class="h-4 w-4" />
                        Tambah Jadwal
                    </x-button>
                    <x-button :href="route('dosen.riwayat')" variant="secondary" class="w-full justify-start">
                        <x-icon name="clipboard" class="h-4 w-4" />
                        Tinjau Pengajuan
                        @if ($stats['pending'] > 0)
                            <span class="ml-auto rounded-full bg-warning/15 px-2 py-0.5 text-xs font-semibold text-warning">{{ $stats['pending'] }}</span>
                        @endif
                    </x-button>
                    <x-button :href="route('dosen.mahasiswa.index')" variant="ghost" class="w-full justify-start">
                        <x-icon name="users" class="h-4 w-4" />
                        Mahasiswa Bimbingan
                    </x-button>
                </div>
            </x-card>

            <x-card>
                <h2 class="text-sm font-semibold text-foreground">Ringkasan Bimbingan</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-muted-foreground">Mahasiswa bimbingan</dt>
                        <dd class="font-semibold text-foreground">{{ $stats['total_mahasiswa'] }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-muted-foreground">Pengajuan disetujui</dt>
                        <dd class="font-semibold text-foreground">{{ $stats['approved'] }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-muted-foreground">Konsultasi selesai</dt>
                        <dd class="font-semibold text-foreground">{{ $stats['completed'] }}</dd>
                    </div>
                </dl>
            </x-card>
        </div>
    </div>
</x-app-layout>
