<x-app-layout>
    <x-page-header
        title="Kelola Jadwal Konsultasi"
        subtitle="Atur ketersediaan waktu konsultasi untuk mahasiswa bimbingan Anda."
    >
        <x-slot:breadcrumb>
            <x-breadcrumb :items="[['label' => 'Kelola Jadwal']]" />
        </x-slot:breadcrumb>
        <x-slot:actions>
            <x-button :href="route('dosen.jadwal.create')">
                <x-icon name="plus" class="h-4 w-4" />
                Tambah Jadwal
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-filter-bar class="mb-5">
        <x-form-field label="Status" name="status" class="w-full sm:w-44">
            <x-select name="status">
                <option value="">Semua status</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Tidak Aktif</option>
            </x-select>
        </x-form-field>

        <div class="flex gap-2">
            <x-button type="submit">
                <x-icon name="filter" class="h-4 w-4" />
                Terapkan
            </x-button>
            <x-button :href="route('dosen.jadwal')" variant="secondary">Reset</x-button>
        </div>
    </x-filter-bar>

    @if ($jadwal->isEmpty())
        <x-card>
            <x-empty-state
                icon="calendar"
                title="Belum ada jadwal konsultasi"
                description="Tambahkan jadwal agar mahasiswa bimbingan dapat mengajukan konsultasi."
            >
                <x-slot:action>
                    <x-button :href="route('dosen.jadwal.create')" size="sm">Tambah Jadwal</x-button>
                </x-slot:action>
            </x-empty-state>
        </x-card>
    @else
        <div class="space-y-3 md:hidden">
            @foreach ($jadwal as $item)
                <div class="rounded-xl border border-border bg-surface p-4 shadow-card">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-foreground">{{ $item->tanggal->translatedFormat('l, d F Y') }}</p>
                            <p class="mt-0.5 text-xs text-muted-foreground">{{ $item->jam_mulai }}–{{ $item->jam_selesai }} &middot; {{ $item->lokasi ?? 'Lokasi belum ditentukan' }}</p>
                        </div>
                        <x-status-badge :status="$item->status" class="shrink-0" />
                    </div>

                    <div class="mt-3 flex items-center justify-between gap-2">
                        <span class="text-xs text-muted-foreground">{{ $item->terisi_count }} pengajuan aktif</span>
                        <div class="flex items-center gap-1">
                            <x-button :href="route('dosen.jadwal.edit', $item)" variant="ghost" size="sm">Edit</x-button>
                            <x-button type="button" variant="ghost" size="sm" class="text-danger hover:bg-danger/10" x-data x-on:click="$dispatch('open-modal', 'hapus-jadwal-{{ $item->id }}')">Hapus</x-button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <x-table class="hidden md:block">
            <thead class="border-b border-border bg-muted/60">
                <tr>
                    <th scope="col" class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Tanggal</th>
                    <th scope="col" class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Waktu</th>
                    <th scope="col" class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Lokasi</th>
                    <th scope="col" class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Pengajuan</th>
                    <th scope="col" class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Status</th>
                    <th scope="col" class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-muted-foreground">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @foreach ($jadwal as $item)
                    <tr class="transition-colors hover:bg-muted/50">
                        <td class="px-5 py-4 text-sm font-medium text-foreground">{{ $item->tanggal->translatedFormat('d M Y') }}</td>
                        <td class="px-5 py-4 text-sm text-muted-foreground">{{ $item->jam_mulai }}–{{ $item->jam_selesai }}</td>
                        <td class="px-5 py-4 text-sm text-muted-foreground">{{ $item->lokasi ?? '-' }}</td>
                        <td class="px-5 py-4 text-sm text-muted-foreground">{{ $item->terisi_count }}</td>
                        <td class="px-5 py-4"><x-status-badge :status="$item->status" /></td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1">
                                <x-button :href="route('dosen.jadwal.edit', $item)" variant="ghost" size="sm">Edit</x-button>
                                <x-button type="button" variant="ghost" size="sm" class="text-danger hover:bg-danger/10" x-data x-on:click="$dispatch('open-modal', 'hapus-jadwal-{{ $item->id }}')">Hapus</x-button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>

        <div class="mt-5">
            <x-pagination :paginator="$jadwal" />
        </div>

        @foreach ($jadwal as $item)
            <x-confirm-dialog
                :name="'hapus-jadwal-'.$item->id"
                title="Hapus jadwal konsultasi?"
                :description="'Jadwal tanggal '.$item->tanggal->translatedFormat('d F Y').' pukul '.$item->jam_mulai.'–'.$item->jam_selesai.' akan dihapus permanen. Jadwal yang sudah memiliki pengajuan tidak dapat dihapus; nonaktifkan melalui Edit.'"
                :action="route('dosen.jadwal.destroy', $item)"
                confirmLabel="Hapus Jadwal"
            />
        @endforeach
    @endif
</x-app-layout>
