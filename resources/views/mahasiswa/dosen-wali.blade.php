<x-app-layout>
    <x-page-header
        title="Informasi Dosen Wali"
        subtitle="Kontak dan jadwal konsultasi dosen wali Anda."
    >
        <x-slot:breadcrumb>
            <x-breadcrumb :items="[['label' => 'Dosen Wali']]" />
        </x-slot:breadcrumb>
    </x-page-header>

    @if (! $dosenWali)
        <x-card>
            <x-empty-state
                icon="academic-cap"
                title="Dosen wali belum ditetapkan"
                description="Hubungi administrator akademik untuk penetapan dosen wali."
            />
        </x-card>
    @else
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <x-card>
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                        <x-avatar :name="$dosenWali->name" size="xl" />
                        <div class="min-w-0">
                            <h2 class="text-lg font-semibold text-foreground">{{ $dosenWali->name }}</h2>
                            <p class="mt-0.5 text-sm text-muted-foreground">Dosen Wali &middot; NIDN {{ $dosenWali->nidn ?? '-' }}</p>
                            <a href="mailto:{{ $dosenWali->email }}" class="mt-2 inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-primary-dark">
                                <x-icon name="mail" class="h-4 w-4" />
                                {{ $dosenWali->email }}
                            </a>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-lg bg-muted px-4 py-3">
                            <p class="text-xs text-muted-foreground">Total Konsultasi</p>
                            <p class="mt-1 text-xl font-semibold text-foreground">{{ $totalKonsultasi }}</p>
                        </div>
                        <div class="rounded-lg bg-muted px-4 py-3">
                            <p class="text-xs text-muted-foreground">Jadwal Tersedia</p>
                            <p class="mt-1 text-xl font-semibold text-foreground">{{ $jadwal->count() }}</p>
                        </div>
                    </div>

                    <p class="mt-5 text-sm leading-relaxed text-muted-foreground">
                        Dosen wali membantu Anda dalam perencanaan studi, konsultasi akademik, dan bimbingan selama masa perkuliahan.
                        Gunakan jadwal konsultasi yang tersedia untuk mengajukan pertemuan.
                    </p>

                    <div class="mt-5 flex flex-col gap-2 sm:flex-row">
                        <x-button :href="route('mahasiswa.pengajuan.create')">
                            <x-icon name="plus" class="h-4 w-4" />
                            Ajukan Konsultasi
                        </x-button>
                        <x-button :href="route('mahasiswa.jadwal')" variant="secondary">Lihat Semua Jadwal</x-button>
                    </div>
                </x-card>
            </div>

            <div>
                <x-card :padding="false">
                    <x-slot:header>
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="text-sm font-semibold text-foreground">Jadwal Mendatang</h2>
                            <x-icon name="calendar" class="h-5 w-5 text-muted-foreground" />
                        </div>
                    </x-slot:header>

                    @if ($jadwal->isEmpty())
                        <x-empty-state
                            icon="calendar"
                            title="Belum ada jadwal"
                            description="Dosen wali belum membuka jadwal konsultasi."
                        />
                    @else
                        <ul class="divide-y divide-border">
                            @foreach ($jadwal->take(5) as $item)
                                <li class="flex items-center gap-3 px-5 py-3.5">
                                    <div class="flex h-10 w-10 shrink-0 flex-col items-center justify-center rounded-lg bg-primary/10 text-primary">
                                        <span class="text-sm font-semibold leading-none">{{ $item->tanggal->format('d') }}</span>
                                        <span class="mt-0.5 text-[9px] font-medium uppercase">{{ $item->tanggal->translatedFormat('M') }}</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-foreground">{{ $item->jam_mulai }}–{{ $item->jam_selesai }}</p>
                                        <p class="truncate text-xs text-muted-foreground">{{ $item->lokasi ?? 'Lokasi belum ditentukan' }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-card>
            </div>
        </div>
    @endif
</x-app-layout>
