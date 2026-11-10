@php
    $dosenWali = auth()->user()->dosenWali;
@endphp

<x-app-layout>
    <x-page-header
        title="Ajukan Konsultasi"
        subtitle="Pilih jadwal dosen wali dan jelaskan topik yang ingin Anda konsultasikan."
    >
        <x-slot:breadcrumb>
            <x-breadcrumb :items="[['label' => 'Riwayat Konsultasi', 'url' => route('mahasiswa.riwayat')], ['label' => 'Ajukan Konsultasi']]" />
        </x-slot:breadcrumb>
    </x-page-header>

    @if ($jadwal->isEmpty())
        <x-card>
            <x-empty-state
                icon="calendar"
                title="Belum ada slot jadwal tersedia"
                description="Semua jadwal dosen wali sedang terisi atau belum dibuka. Silakan periksa kembali nanti."
            >
                <x-slot:action>
                    <x-button :href="route('mahasiswa.jadwal')" size="sm">Lihat Jadwal</x-button>
                </x-slot:action>
            </x-empty-state>
        </x-card>
    @else
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <x-card>
                    <form method="POST" action="{{ route('mahasiswa.pengajuan.store') }}" class="space-y-5">
                        @csrf

                        <x-form-field
                            label="Jadwal Konsultasi"
                            name="jadwal_id"
                            hint="Hanya jadwal yang masih tersedia yang ditampilkan."
                        >
                            <x-select name="jadwal_id" required>
                                <option value="">Pilih jadwal konsultasi</option>
                                @foreach ($jadwal as $item)
                                    <option value="{{ $item->id }}" @selected((int) old('jadwal_id', $selectedJadwal) === $item->id)>
                                        {{ $item->tanggal->translatedFormat('l, d F Y') }} · {{ $item->jam_mulai }}–{{ $item->jam_selesai }}{{ $item->lokasi ? ' · '.$item->lokasi : '' }}
                                    </option>
                                @endforeach
                            </x-select>
                        </x-form-field>

                        <x-form-field label="Topik Konsultasi" name="topik" hint="Contoh: Konsultasi rencana studi semester depan.">
                            <x-text-input type="text" name="topik" :value="old('topik')" placeholder="Tuliskan topik konsultasi" required maxlength="255" />
                        </x-form-field>

                        <x-form-field label="Deskripsi" name="deskripsi" hint="Jelaskan secara singkat hal yang ingin didiskusikan.">
                            <x-textarea name="deskripsi" rows="6" placeholder="Tuliskan deskripsi konsultasi" required maxlength="2000">{{ old('deskripsi') }}</x-textarea>
                        </x-form-field>

                        <div class="flex flex-col-reverse gap-2 border-t border-border pt-5 sm:flex-row sm:justify-end">
                            <x-button :href="route('mahasiswa.jadwal')" variant="secondary">Batal</x-button>
                            <x-button type="submit">
                                <x-icon name="check" class="h-4 w-4" />
                                Kirim Pengajuan
                            </x-button>
                        </div>
                    </form>
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card>
                    <h2 class="text-sm font-semibold text-foreground">Dosen Wali</h2>

                    @if ($dosenWali)
                        <div class="mt-4 flex items-center gap-3">
                            <x-avatar :name="$dosenWali->name" size="lg" />
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-foreground">{{ $dosenWali->name }}</p>
                                <p class="text-xs text-muted-foreground">NIDN {{ $dosenWali->nidn ?? '-' }}</p>
                            </div>
                        </div>
                        <p class="mt-3 text-sm text-muted-foreground">{{ $dosenWali->email }}</p>
                    @else
                        <p class="mt-3 text-sm text-muted-foreground">Dosen wali belum ditetapkan.</p>
                    @endif
                </x-card>

                <x-card>
                    <h2 class="text-sm font-semibold text-foreground">Ketentuan Pengajuan</h2>
                    <ul class="mt-3 space-y-2 text-sm text-muted-foreground">
                        <li class="flex gap-2"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-primary"></span>Satu jadwal hanya untuk satu pengajuan konsultasi.</li>
                        <li class="flex gap-2"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-primary"></span>Pengajuan akan berstatus menunggu hingga disetujui dosen wali.</li>
                        <li class="flex gap-2"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-primary"></span>Anda dapat memantau status pada halaman riwayat konsultasi.</li>
                    </ul>
                </x-card>
            </div>
        </div>
    @endif
</x-app-layout>
