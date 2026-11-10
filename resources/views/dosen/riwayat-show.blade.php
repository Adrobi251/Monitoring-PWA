@php
    $catatan = $pengajuan->catatan;
    $jadwal = $pengajuan->jadwal;
@endphp

<x-app-layout>
    <x-page-header
        title="Detail Pengajuan"
        :subtitle="$pengajuan->topik"
    >
        <x-slot:breadcrumb>
            <x-breadcrumb :items="[['label' => 'Pengajuan Konsultasi', 'url' => route('dosen.riwayat')], ['label' => 'Detail Pengajuan']]" />
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
                        <dt class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Mahasiswa</dt>
                        <dd class="mt-1 text-sm font-medium text-foreground">{{ $pengajuan->mahasiswa->name ?? '-' }}</dd>
                        <dd class="text-xs text-muted-foreground">NIM {{ $pengajuan->mahasiswa->nim ?? '-' }}</dd>
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

            <x-card>
                <h2 class="text-sm font-semibold text-foreground">Riwayat Status</h2>

                <x-timeline class="mt-4">
                    <x-timeline-item title="Pengajuan dikirim" :time="$pengajuan->created_at->translatedFormat('d M Y · H:i')" tone="primary">
                        Diajukan oleh {{ $pengajuan->mahasiswa->name ?? 'mahasiswa' }}.
                    </x-timeline-item>

                    @if ($pengajuan->status === 'approved' || $pengajuan->status === 'completed')
                        <x-timeline-item title="Pengajuan disetujui" tone="success">
                            Pengajuan disetujui dan siap dilaksanakan sesuai jadwal.
                        </x-timeline-item>
                    @elseif ($pengajuan->status === 'rejected')
                        <x-timeline-item title="Pengajuan ditolak" tone="danger">
                            {{ $pengajuan->alasan_penolakan ?? 'Pengajuan tidak dapat diproses.' }}
                        </x-timeline-item>
                    @else
                        <x-timeline-item title="Menunggu persetujuan" tone="warning">
                            Pengajuan menunggu keputusan dosen wali.
                        </x-timeline-item>
                    @endif

                    @if ($pengajuan->status === 'completed')
                        <x-timeline-item title="Konsultasi selesai" tone="success">
                            Catatan hasil konsultasi sudah diisi.
                        </x-timeline-item>
                    @endif
                </x-timeline>
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

                    <div class="mt-5 flex flex-wrap gap-2">
                        <x-button :href="route('dosen.pengajuan.catatan.edit', [$pengajuan, $catatan])" variant="secondary" size="sm">
                            <x-icon name="pencil" class="h-4 w-4" />
                            Edit Catatan
                        </x-button>
                        <x-button :href="route('mahasiswa.pengajuan.catatan.show', $pengajuan)" variant="ghost" size="sm">Lihat Halaman Catatan</x-button>
                    </div>
                </x-card>
            @endif
        </div>

        <div class="space-y-6">
            <x-card>
                <h2 class="text-sm font-semibold text-foreground">Jadwal Konsultasi</h2>

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
                <h2 class="text-sm font-semibold text-foreground">Tindakan</h2>

                <div class="mt-4 space-y-2">
                    @if ($pengajuan->status === 'pending')
                        <x-button type="button" variant="success" class="w-full justify-start" x-data x-on:click="$dispatch('open-modal', 'setujui-pengajuan')">
                            <x-icon name="check-circle" class="h-4 w-4" />
                            Setujui Pengajuan
                        </x-button>
                        <x-button type="button" variant="ghost" class="w-full justify-start text-danger hover:bg-danger/10" x-data x-on:click="$dispatch('open-modal', 'tolak-pengajuan')">
                            <x-icon name="x-circle" class="h-4 w-4" />
                            Tolak Pengajuan
                        </x-button>
                    @endif

                    @if ($pengajuan->status === 'approved' && ! $catatan)
                        <x-button :href="route('dosen.pengajuan.catatan.create', $pengajuan)" class="w-full justify-start">
                            <x-icon name="plus" class="h-4 w-4" />
                            Tambah Catatan Konsultasi
                        </x-button>
                        <p class="text-xs text-muted-foreground">Status akan otomatis menjadi selesai setelah catatan disimpan.</p>
                    @endif

                    @if ($catatan)
                        <x-button :href="route('dosen.pengajuan.catatan.edit', [$pengajuan, $catatan])" variant="secondary" class="w-full justify-start">
                            <x-icon name="pencil" class="h-4 w-4" />
                            Edit Catatan
                        </x-button>
                    @endif

                    <x-button :href="route('dosen.riwayat')" variant="ghost" class="w-full justify-start">
                        <x-icon name="arrow-left" class="h-4 w-4" />
                        Kembali ke Daftar
                    </x-button>
                </div>
            </x-card>

            @if ($pengajuan->mahasiswa)
                <x-card>
                    <h2 class="text-sm font-semibold text-foreground">Mahasiswa</h2>
                    <div class="mt-4 flex items-center gap-3">
                        <x-avatar :name="$pengajuan->mahasiswa->name" size="lg" />
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-foreground">{{ $pengajuan->mahasiswa->name }}</p>
                            <p class="text-xs text-muted-foreground">NIM {{ $pengajuan->mahasiswa->nim ?? '-' }}</p>
                        </div>
                    </div>
                    <x-button :href="route('dosen.mahasiswa.show', $pengajuan->mahasiswa)" variant="secondary" size="sm" class="mt-4 w-full">Lihat Profil Mahasiswa</x-button>
                </x-card>
            @endif
        </div>
    </div>

    @if ($pengajuan->status === 'pending')
        <x-modal name="setujui-pengajuan" maxWidth="md" focusable>
            <form method="POST" action="{{ route('dosen.pengajuan.status', $pengajuan) }}" class="p-6">
                @csrf
                @method('PUT')

                <div class="flex items-start gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-success/10 text-success">
                        <x-icon name="check-circle" class="h-5 w-5" />
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-lg font-semibold text-foreground">Setujui pengajuan?</h2>
                        <p class="mt-1 text-sm text-muted-foreground">Pengajuan ini akan disetujui dan mahasiswa dapat melaksanakan konsultasi sesuai jadwal.</p>
                    </div>
                </div>

                <input type="hidden" name="status" value="approved">

                <div class="mt-6 flex justify-end gap-2">
                    <x-button type="button" variant="secondary" x-on:click="$dispatch('close')">Batal</x-button>
                    <x-button variant="success">Setujui</x-button>
                </div>
            </form>
        </x-modal>

        <x-modal name="tolak-pengajuan" maxWidth="md" focusable>
            <form method="POST" action="{{ route('dosen.pengajuan.status', $pengajuan) }}" class="p-6">
                @csrf
                @method('PUT')

                <div class="flex items-start gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-danger/10 text-danger">
                        <x-icon name="x-circle" class="h-5 w-5" />
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-lg font-semibold text-foreground">Tolak pengajuan?</h2>
                        <p class="mt-1 text-sm text-muted-foreground">Berikan alasan agar mahasiswa memahami keputusan Anda.</p>
                    </div>
                </div>

                <input type="hidden" name="status" value="rejected">

                <div class="mt-4">
                    <x-form-field label="Alasan Penolakan" name="alasan_penolakan">
                        <x-textarea name="alasan_penolakan" rows="3" placeholder="Tuliskan alasan penolakan" required></x-textarea>
                    </x-form-field>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <x-button type="button" variant="secondary" x-on:click="$dispatch('close')">Batal</x-button>
                    <x-button variant="danger">Tolak Pengajuan</x-button>
                </div>
            </form>
        </x-modal>
    @endif
</x-app-layout>
