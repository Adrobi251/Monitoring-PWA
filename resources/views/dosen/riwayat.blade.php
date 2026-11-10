<x-app-layout>
    <x-page-header
        title="Pengajuan Konsultasi"
        subtitle="Tinjau, setujui, atau tolak pengajuan konsultasi mahasiswa bimbingan."
    >
        <x-slot:breadcrumb>
            <x-breadcrumb :items="[['label' => 'Pengajuan Konsultasi']]" />
        </x-slot:breadcrumb>
    </x-page-header>

    <x-filter-bar class="mb-5">
        <x-form-field label="Cari" name="q" class="w-full sm:w-56">
            <x-search-input name="q" placeholder="Nama, NIM, atau topik" />
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
            <x-button type="submit">
                <x-icon name="filter" class="h-4 w-4" />
                Terapkan
            </x-button>
            <x-button :href="route('dosen.riwayat')" variant="secondary">Reset</x-button>
        </div>
    </x-filter-bar>

    @if ($riwayat->isEmpty())
        <x-card>
            <x-empty-state
                icon="clipboard"
                title="Tidak ada pengajuan"
                description="Belum ada pengajuan konsultasi yang sesuai dengan filter Anda."
            />
        </x-card>
    @else
        <div class="space-y-3 md:hidden">
            @foreach ($riwayat as $item)
                <div class="rounded-xl border border-border bg-surface p-4 shadow-card">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-foreground">{{ $item->mahasiswa->name ?? '-' }}</p>
                            <p class="text-xs text-muted-foreground">{{ $item->mahasiswa->nim ?? '-' }}</p>
                        </div>
                        <x-status-badge :status="$item->status" class="shrink-0" />
                    </div>

                    <p class="mt-3 text-sm font-medium text-foreground">{{ $item->topik }}</p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        {{ $item->jadwal?->tanggal?->translatedFormat('d M Y') ?? '-' }}
                        @if ($item->jadwal)
                            &middot; {{ $item->jadwal->jam_mulai }}–{{ $item->jadwal->jam_selesai }}
                        @endif
                    </p>

                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <x-button :href="route('dosen.riwayat.show', $item)" variant="secondary" size="sm">Detail</x-button>
                        @if ($item->status === 'pending')
                            <x-button type="button" size="sm" x-data x-on:click="$dispatch('open-modal', 'setujui-{{ $item->id }}')">Setujui</x-button>
                            <x-button type="button" variant="ghost" size="sm" class="text-danger hover:bg-danger/10" x-data x-on:click="$dispatch('open-modal', 'tolak-{{ $item->id }}')">Tolak</x-button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <x-table class="hidden md:block">
            <thead class="border-b border-border bg-muted/60">
                <tr>
                    <th scope="col" class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Mahasiswa</th>
                    <th scope="col" class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Topik</th>
                    <th scope="col" class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Jadwal</th>
                    <th scope="col" class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Status</th>
                    <th scope="col" class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-muted-foreground">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @foreach ($riwayat as $item)
                    <tr class="transition-colors hover:bg-muted/50">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <x-avatar :name="$item->mahasiswa->name ?? '?'" size="sm" />
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-foreground">{{ $item->mahasiswa->name ?? '-' }}</p>
                                    <p class="text-xs text-muted-foreground">{{ $item->mahasiswa->nim ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="max-w-[16rem] truncate text-sm text-foreground">{{ $item->topik }}</p>
                            <p class="mt-0.5 text-xs text-muted-foreground">{{ $item->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-5 py-4 text-sm text-muted-foreground">
                            @if ($item->jadwal)
                                <p class="text-foreground">{{ $item->jadwal->tanggal->translatedFormat('d M Y') }}</p>
                                <p class="text-xs">{{ $item->jadwal->jam_mulai }}–{{ $item->jadwal->jam_selesai }}</p>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-5 py-4"><x-status-badge :status="$item->status" /></td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1">
                                <x-button :href="route('dosen.riwayat.show', $item)" variant="ghost" size="sm">Detail</x-button>
                                @if ($item->status === 'pending')
                                    <x-button type="button" size="sm" x-data x-on:click="$dispatch('open-modal', 'setujui-{{ $item->id }}')">Setujui</x-button>
                                    <x-button type="button" variant="ghost" size="sm" class="text-danger hover:bg-danger/10" x-data x-on:click="$dispatch('open-modal', 'tolak-{{ $item->id }}')">Tolak</x-button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>

        <div class="mt-5">
            <x-pagination :paginator="$riwayat" />
        </div>

        @foreach ($riwayat as $item)
            @if ($item->status === 'pending')
                <x-modal :name="'setujui-'.$item->id" maxWidth="md" focusable>
                    <form method="POST" action="{{ route('dosen.pengajuan.status', $item) }}" class="p-6">
                        @csrf
                        @method('PUT')

                        <div class="flex items-start gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-success/10 text-success">
                                <x-icon name="check-circle" class="h-5 w-5" />
                            </div>
                            <div class="min-w-0">
                                <h2 class="text-lg font-semibold text-foreground">Setujui pengajuan?</h2>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    Pengajuan <span class="font-medium text-foreground">{{ $item->topik }}</span> dari {{ $item->mahasiswa->name ?? 'mahasiswa' }} akan disetujui.
                                </p>
                            </div>
                        </div>

                        <input type="hidden" name="status" value="approved">

                        <div class="mt-6 flex justify-end gap-2">
                            <x-button type="button" variant="secondary" x-on:click="$dispatch('close')">Batal</x-button>
                            <x-button variant="success">Setujui</x-button>
                        </div>
                    </form>
                </x-modal>

                <x-modal :name="'tolak-'.$item->id" maxWidth="md" focusable>
                    <form method="POST" action="{{ route('dosen.pengajuan.status', $item) }}" class="p-6">
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
        @endforeach
    @endif
</x-app-layout>
