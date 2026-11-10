<x-app-layout>
    <x-page-header
        title="Riwayat Konsultasi"
        subtitle="Seluruh pengajuan konsultasi akademik Anda."
    >
        <x-slot:breadcrumb>
            <x-breadcrumb :items="[['label' => 'Riwayat Konsultasi']]" />
        </x-slot:breadcrumb>
        <x-slot:actions>
            <x-button :href="route('mahasiswa.pengajuan.create')">
                <x-icon name="plus" class="h-4 w-4" />
                Ajukan Konsultasi
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-filter-bar class="mb-5">
        <x-form-field label="Cari topik" name="q" class="w-full sm:w-56">
            <x-search-input name="q" placeholder="Cari topik konsultasi" />
        </x-form-field>

        <x-form-field label="Status" name="status" class="w-full sm:w-40">
            <x-select name="status">
                <option value="">Semua status</option>
                <option value="pending" @selected(request('status') === 'pending')>Menunggu</option>
                <option value="approved" @selected(request('status') === 'approved')>Disetujui</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Ditolak</option>
                <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
            </x-select>
        </x-form-field>

        <x-form-field label="Dari tanggal" name="from" class="w-full sm:w-40">
            <x-text-input type="date" name="from" :value="request('from')" />
        </x-form-field>

        <x-form-field label="Sampai tanggal" name="to" class="w-full sm:w-40">
            <x-text-input type="date" name="to" :value="request('to')" />
        </x-form-field>

        <div class="flex gap-2">
            <x-button type="submit" size="md">
                <x-icon name="filter" class="h-4 w-4" />
                Terapkan
            </x-button>
            <x-button :href="route('mahasiswa.riwayat')" variant="secondary">Reset</x-button>
        </div>
    </x-filter-bar>

    @if ($riwayat->isEmpty())
        <x-card>
            <x-empty-state
                icon="clipboard"
                title="Tidak ada data konsultasi"
                description="Belum ada pengajuan yang sesuai dengan filter Anda."
            >
                <x-slot:action>
                    <x-button :href="route('mahasiswa.pengajuan.create')" size="sm">Ajukan Konsultasi</x-button>
                </x-slot:action>
            </x-empty-state>
        </x-card>
    @else
        <div class="space-y-3 md:hidden">
            @foreach ($riwayat as $item)
                <a href="{{ route('mahasiswa.pengajuan.show', $item) }}" class="block rounded-xl border border-border bg-surface p-4 shadow-card">
                    <div class="flex items-start justify-between gap-3">
                        <p class="min-w-0 text-sm font-semibold text-foreground">{{ $item->topik }}</p>
                        <x-status-badge :status="$item->status" class="shrink-0" />
                    </div>
                    <p class="mt-1 text-xs text-muted-foreground">
                        {{ $item->jadwal?->tanggal?->translatedFormat('d M Y') ?? $item->created_at->translatedFormat('d M Y') }}
                        @if ($item->jadwal)
                            &middot; {{ $item->jadwal->jam_mulai }}–{{ $item->jadwal->jam_selesai }}
                        @endif
                    </p>
                    <p class="mt-2 text-xs text-muted-foreground">Dosen: {{ $item->dosen?->name ?? '-' }}</p>
                </a>
            @endforeach
        </div>

        <x-table class="hidden md:block">
            <thead class="border-b border-border bg-muted/60">
                <tr>
                    <th scope="col" class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Topik</th>
                    <th scope="col" class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Jadwal</th>
                    <th scope="col" class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Diajukan</th>
                    <th scope="col" class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Status</th>
                    <th scope="col" class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-muted-foreground">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @foreach ($riwayat as $item)
                    <tr class="transition-colors hover:bg-muted/50">
                        <td class="px-5 py-4">
                            <p class="max-w-xs truncate text-sm font-medium text-foreground">{{ $item->topik }}</p>
                            <p class="mt-0.5 text-xs text-muted-foreground">Dosen: {{ $item->dosen?->name ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-4 text-sm text-muted-foreground">
                            @if ($item->jadwal)
                                <p class="text-foreground">{{ $item->jadwal->tanggal->translatedFormat('d M Y') }}</p>
                                <p class="text-xs">{{ $item->jadwal->jam_mulai }}–{{ $item->jadwal->jam_selesai }}</p>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm text-muted-foreground">{{ $item->created_at->translatedFormat('d M Y') }}</td>
                        <td class="px-5 py-4"><x-status-badge :status="$item->status" /></td>
                        <td class="px-5 py-4 text-right">
                            <x-button :href="route('mahasiswa.pengajuan.show', $item)" variant="ghost" size="sm">Detail</x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>

        <div class="mt-5">
            <x-pagination :paginator="$riwayat" />
        </div>
    @endif
</x-app-layout>
