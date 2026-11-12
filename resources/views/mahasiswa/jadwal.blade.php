<x-app-layout>
    <x-page-header
        title="Jadwal Konsultasi"
        subtitle="Pilih jadwal dosen wali yang tersedia, lalu ajukan konsultasi."
    >
        <x-slot:breadcrumb>
            <x-breadcrumb :items="[['label' => 'Jadwal Konsultasi']]" />
        </x-slot:breadcrumb>
    </x-page-header>

    @if (auth()->user()->dosen_wali_id === null)
        <x-card>
            <x-empty-state
                icon="academic-cap"
                title="Dosen wali belum ditetapkan"
                description="Jadwal konsultasi akan muncul setelah dosen wali Anda ditetapkan oleh administrator."
            />
        </x-card>
    @elseif ($jadwal->isEmpty())
        <x-card>
            <x-empty-state
                icon="calendar"
                title="Belum ada jadwal tersedia"
                description="Dosen wali Anda belum membuka jadwal konsultasi. Silakan periksa kembali nanti."
            />
        </x-card>
    @else
        <div class="space-y-3">
            @foreach ($jadwal as $item)
                <div class="glass-lite rounded-xl p-4 sm:p-5">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-start gap-4">
                            <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <span class="text-lg font-semibold leading-none">{{ $item->tanggal->format('d') }}</span>
                                <span class="mt-0.5 text-[10px] font-medium uppercase tracking-wide">{{ $item->tanggal->translatedFormat('M') }}</span>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-foreground">{{ $item->tanggal->translatedFormat('l, d F Y') }}</p>
                                <p class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-muted-foreground">
                                    <span class="inline-flex items-center gap-1.5">
                                        <x-icon name="clock" class="h-4 w-4" />
                                        {{ $item->jam_mulai }}–{{ $item->jam_selesai }}
                                    </span>
                                    @if ($item->lokasi)
                                        <span class="text-border">•</span>
                                        <span>{{ $item->lokasi }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-2 sm:justify-end">
                            @if ($item->terisi_count > 0)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-muted px-2.5 py-1 text-xs font-medium text-muted-foreground ring-1 ring-inset ring-border">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    Slot sudah terisi
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-success/10 px-2.5 py-1 text-xs font-medium text-success ring-1 ring-inset ring-success/25">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    Tersedia
                                </span>
                                <x-button :href="route('mahasiswa.pengajuan.create', ['jadwal' => $item->id])" size="sm">
                                    Ajukan
                                    <x-icon name="arrow-right" class="h-4 w-4" />
                                </x-button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
