<x-app-layout>
    <x-page-header
        title="Edit Catatan Konsultasi"
        subtitle="Perbarui catatan hasil konsultasi mahasiswa."
    >
        <x-slot:breadcrumb>
            <x-breadcrumb :items="[['label' => 'Pengajuan Konsultasi', 'url' => route('dosen.riwayat')], ['label' => 'Detail Pengajuan', 'url' => route('dosen.riwayat.show', $catatan->pengajuan)], ['label' => 'Edit Catatan']]" />
        </x-slot:breadcrumb>
    </x-page-header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-card>
                <form method="POST" action="{{ route('dosen.pengajuan.catatan.update', [$catatan->pengajuan, $catatan]) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <x-form-field label="Ringkasan" name="ringkasan" hint="Ringkasan singkat hasil konsultasi.">
                        <x-textarea name="ringkasan" rows="3" placeholder="Tuliskan ringkasan konsultasi" required maxlength="255">{{ old('ringkasan', $catatan->ringkasan) }}</x-textarea>
                    </x-form-field>

                    <x-form-field label="Arahan" name="arahan" hint="Arahan atau saran yang diberikan kepada mahasiswa.">
                        <x-textarea name="arahan" rows="3" placeholder="Tuliskan arahan konsultasi" required maxlength="255">{{ old('arahan', $catatan->arahan) }}</x-textarea>
                    </x-form-field>

                    <x-form-field label="Tindak Lanjut" name="tindak_lanjut" hint="Opsional. Langkah berikutnya yang perlu dilakukan.">
                        <x-textarea name="tindak_lanjut" rows="3" placeholder="Tuliskan tindak lanjut" maxlength="255">{{ old('tindak_lanjut', $catatan->tindak_lanjut) }}</x-textarea>
                    </x-form-field>

                    <div class="flex flex-col-reverse gap-2 border-t border-border pt-5 sm:flex-row sm:justify-end">
                        <x-button :href="route('dosen.riwayat.show', $catatan->pengajuan)" variant="secondary">Batal</x-button>
                        <x-button type="submit">
                            <x-icon name="check" class="h-4 w-4" />
                            Simpan Perubahan
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>

        <div>
            <x-card>
                <h2 class="text-sm font-semibold text-foreground">Ringkasan Pengajuan</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-muted-foreground">Mahasiswa</dt>
                        <dd class="font-medium text-foreground">{{ $catatan->pengajuan->mahasiswa->name ?? '-' }}</dd>
                        <dd class="text-xs text-muted-foreground">NIM {{ $catatan->pengajuan->mahasiswa->nim ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted-foreground">Topik</dt>
                        <dd class="font-medium text-foreground">{{ $catatan->pengajuan->topik }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted-foreground">Jadwal</dt>
                        <dd class="font-medium text-foreground">
                            {{ $catatan->pengajuan->jadwal?->tanggal?->translatedFormat('d M Y') ?? '-' }}
                            @if ($catatan->pengajuan->jadwal)
                                &middot; {{ $catatan->pengajuan->jadwal->jam_mulai }}–{{ $catatan->pengajuan->jadwal->jam_selesai }}
                            @endif
                        </dd>
                    </div>
                </dl>
            </x-card>
        </div>
    </div>
</x-app-layout>
