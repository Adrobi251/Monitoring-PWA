<x-app-layout>
    <x-page-header
        title="Edit Jadwal Konsultasi"
        subtitle="Perbarui detail slot konsultasi yang sudah ada."
    >
        <x-slot:breadcrumb>
            <x-breadcrumb :items="[['label' => 'Kelola Jadwal', 'url' => route('dosen.jadwal')], ['label' => 'Edit Jadwal']]" />
        </x-slot:breadcrumb>
    </x-page-header>

    <div class="max-w-2xl">
        <x-card>
            @if ($sudahDipakai)
                <div class="mb-5 rounded-lg border border-warning/25 bg-warning/10 px-4 py-3 text-sm text-warning">
                    Jadwal ini sudah digunakan pada konsultasi yang disetujui. Perubahan waktu dapat memengaruhi jadwal mahasiswa.
                </div>
            @endif

            <form method="POST" action="{{ route('dosen.jadwal.update', $jadwal) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <x-form-field label="Tanggal" name="tanggal">
                    <x-text-input type="date" name="tanggal" :value="old('tanggal', $jadwal->tanggal->format('Y-m-d'))" min="{{ now()->toDateString() }}" required />
                </x-form-field>

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-form-field label="Jam Mulai" name="jam_mulai">
                        <x-text-input type="time" name="jam_mulai" :value="old('jam_mulai', $jadwal->jam_mulai)" required />
                    </x-form-field>

                    <x-form-field label="Jam Selesai" name="jam_selesai">
                        <x-text-input type="time" name="jam_selesai" :value="old('jam_selesai', $jadwal->jam_selesai)" required />
                    </x-form-field>
                </div>

                <x-form-field label="Lokasi" name="lokasi" hint="Opsional. Contoh: Ruang Dosen Lt. 2 atau Google Meet.">
                    <x-text-input type="text" name="lokasi" :value="old('lokasi', $jadwal->lokasi)" placeholder="Lokasi konsultasi" maxlength="255" />
                </x-form-field>

                <x-form-field label="Status" name="status">
                    <x-select name="status" required>
                        <option value="active" @selected(old('status', $jadwal->status) === 'active')>Aktif</option>
                        <option value="inactive" @selected(old('status', $jadwal->status) === 'inactive')>Tidak Aktif</option>
                    </x-select>
                </x-form-field>

                <div class="flex flex-col-reverse gap-2 border-t border-border pt-5 sm:flex-row sm:justify-end">
                    <x-button :href="route('dosen.jadwal')" variant="secondary">Batal</x-button>
                    <x-button type="submit">
                        <x-icon name="check" class="h-4 w-4" />
                        Simpan Perubahan
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
