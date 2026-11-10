@php
    $isDosen = auth()->user()->role === 'dosen';
    $pengajuan = $catatan->pengajuan;
@endphp

<x-app-layout>
    <x-page-header
        title="Catatan Konsultasi"
        :subtitle="$pengajuan->topik"
    >
        <x-slot:breadcrumb>
            <x-breadcrumb :items="[['label' => 'Catatan Konsultasi']]" />
        </x-slot:breadcrumb>
        <x-slot:actions>
            @if ($isDosen)
                <x-button :href="route('dosen.pengajuan.catatan.edit', [$pengajuan, $catatan])" variant="secondary">
                    <x-icon name="pencil" class="h-4 w-4" />
                    Edit Catatan
                </x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-card>
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-sm font-semibold text-foreground">Hasil Konsultasi</h2>
                    <x-icon name="chat" class="h-5 w-5 text-muted-foreground" />
                </div>

                <dl class="mt-4 space-y-5">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Ringkasan</dt>
                        <dd class="mt-1 whitespace-pre-line text-sm leading-relaxed text-foreground">{{ $catatan->ringkasan }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Arahan</dt>
                        <dd class="mt-1 whitespace-pre-line text-sm leading-relaxed text-foreground">{{ $catatan->arahan }}</dd>
                    </div>
                    @if ($catatan->tindak_lanjut)
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Tindak Lanjut</dt>
                            <dd class="mt-1 whitespace-pre-line text-sm leading-relaxed text-foreground">{{ $catatan->tindak_lanjut }}</dd>
                        </div>
                    @endif
                </dl>
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card>
                <h2 class="text-sm font-semibold text-foreground">Informasi Konsultasi</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-muted-foreground">Mahasiswa</dt>
                        <dd class="font-medium text-foreground">{{ $pengajuan->mahasiswa->name ?? '-' }}</dd>
                        <dd class="text-xs text-muted-foreground">NIM {{ $pengajuan->mahasiswa->nim ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted-foreground">Dosen Wali</dt>
                        <dd class="font-medium text-foreground">{{ $pengajuan->dosen->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted-foreground">Jadwal</dt>
                        <dd class="font-medium text-foreground">
                            {{ $pengajuan->jadwal?->tanggal?->translatedFormat('d M Y') ?? '-' }}
                            @if ($pengajuan->jadwal)
                                &middot; {{ $pengajuan->jadwal->jam_mulai }}–{{ $pengajuan->jadwal->jam_selesai }}
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted-foreground">Status</dt>
                        <dd class="mt-1"><x-status-badge :status="$pengajuan->status" /></dd>
                    </div>
                </dl>

                <x-button :href="$isDosen ? route('dosen.riwayat.show', $pengajuan) : route('mahasiswa.pengajuan.show', $pengajuan)" variant="secondary" size="sm" class="mt-5 w-full">
                    <x-icon name="arrow-left" class="h-4 w-4" />
                    Kembali ke Detail Pengajuan
                </x-button>
            </x-card>
        </div>
    </div>
</x-app-layout>
