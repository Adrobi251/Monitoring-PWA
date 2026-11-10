@php
    $jadwal = $pengajuan->jadwal;
    $catatan = $pengajuan->catatan;
@endphp

<x-app-layout>
    <x-page-header
        title="Detail Konsultasi"
        :subtitle="$pengajuan->topik"
    >
        <x-slot:breadcrumb>
            <x-breadcrumb :items="[['label' => 'Riwayat Konsultasi', 'url' => route('mahasiswa.riwayat')], ['label' => 'Detail Konsultasi']]" />
        </x-slot:breadcrumb>
        <x-slot:actions>
            <x-status-badge :status="$pengajuan->status" />
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-card>
                <h2 class="text-sm font-semibold text-foreground">Informasi Pengajuan</h2>

                <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Topik</dt>
                        <dd class="mt-1 text-sm font-medium text-foreground">{{ $pengajuan->topik }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Tanggal Diajukan</dt>
                        <dd class="mt-1 text-sm text-foreground">{{ $pengajuan->created_at->translatedFormat('l, d F Y · H:i') }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Deskripsi</dt>
                        <dd class="mt-1 whitespace-pre-line text-sm leading-relaxed text-foreground">{{ $pengajuan->deskripsi }}</dd>
                    </div>
                </dl>

                @if ($pengajuan->status === 'rejected' && $pengajuan->alasan_penolakan)
                    <div class="mt-5 rounded-lg border border-danger/25 bg-danger/10 px-4 py-3">
                        <p class="text-sm font-medium text-danger">Alasan Penolakan</p>
                        <p class="mt-1 text-sm text-danger">{{ $pengajuan->alasan_penolakan }}</p>
                    </div>
                @endif
            </x-card>

            @if ($catatan)
                <x-card>
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-sm font-semibold text-foreground">Catatan Konsultasi</h2>
                        <x-icon name="chat" class="h-5 w-5 text-muted-foreground" />
                    </div>

                    <dl class="mt-4 space-y-4">
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

                    <x-button :href="route('mahasiswa.pengajuan.catatan.show', $pengajuan)" variant="secondary" size="sm" class="mt-5">
                        Lihat Catatan Lengkap
                    </x-button>
                </x-card>
            @endif

            <x-card>
                <h2 class="text-sm font-semibold text-foreground">Riwayat Status</h2>

                <x-timeline class="mt-4">
                    <x-timeline-item
                        title="Pengajuan dikirim"
                        :time="$pengajuan->created_at->translatedFormat('d M Y · H:i')"
                        tone="primary"
                    >
                        Pengajuan konsultasi berhasil dikirim dan menunggu persetujuan dosen wali.
                    </x-timeline-item>

                    @if ($pengajuan->status === 'approved' || $pengajuan->status === 'completed')
                        <x-timeline-item title="Pengajuan disetujui" tone="success">
                            Dosen wali menyetujui pengajuan konsultasi Anda.
                        </x-timeline-item>
                    @elseif ($pengajuan->status === 'rejected')
                        <x-timeline-item title="Pengajuan ditolak" tone="danger">
                            {{ $pengajuan->alasan_penolakan ?? 'Pengajuan tidak dapat diproses.' }}
                        </x-timeline-item>
                    @else
                        <x-timeline-item title="Menunggu persetujuan" tone="warning">
                            Pengajuan Anda sedang ditinjau oleh dosen wali.
                        </x-timeline-item>
                    @endif

                    @if ($pengajuan->status === 'completed')
                        <x-timeline-item title="Konsultasi selesai" tone="success">
                            Catatan hasil konsultasi telah diisi oleh dosen wali.
                        </x-timeline-item>
                    @endif
                </x-timeline>
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card>
                <h2 class="text-sm font-semibold text-foreground">Jadwal</h2>

                @if ($jadwal)
                    <div class="mt-4 flex items-start gap-3">
                        <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <span class="text-base font-semibold leading-none">{{ $jadwal->tanggal->format('d') }}</span>
                            <span class="mt-0.5 text-[10px] font-medium uppercase">{{ $jadwal->tanggal->translatedFormat('M') }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-foreground">{{ $jadwal->tanggal->translatedFormat('l, d F Y') }}</p>
                            <p class="mt-0.5 text-sm text-muted-foreground">{{ $jadwal->jam_mulai }}–{{ $jadwal->jam_selesai }}</p>
                            <p class="mt-0.5 text-xs text-muted-foreground">{{ $jadwal->lokasi ?? 'Lokasi belum ditentukan' }}</p>
                        </div>
                    </div>
                @else
                    <p class="mt-3 text-sm text-muted-foreground">Jadwal tidak tersedia.</p>
                @endif
            </x-card>

            <x-card>
                <h2 class="text-sm font-semibold text-foreground">Dosen Wali</h2>

                @if ($pengajuan->dosen)
                    <div class="mt-4 flex items-center gap-3">
                        <x-avatar :name="$pengajuan->dosen->name" size="lg" />
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-foreground">{{ $pengajuan->dosen->name }}</p>
                            <p class="text-xs text-muted-foreground">NIDN {{ $pengajuan->dosen->nidn ?? '-' }}</p>
                        </div>
                    </div>
                    <p class="mt-3 truncate text-sm text-muted-foreground">{{ $pengajuan->dosen->email }}</p>
                @else
                    <p class="mt-3 text-sm text-muted-foreground">Data dosen tidak tersedia.</p>
                @endif
            </x-card>

            <x-button :href="route('mahasiswa.riwayat')" variant="secondary" class="w-full">
                <x-icon name="arrow-left" class="h-4 w-4" />
                Kembali ke Riwayat
            </x-button>
        </div>
    </div>
</x-app-layout>
