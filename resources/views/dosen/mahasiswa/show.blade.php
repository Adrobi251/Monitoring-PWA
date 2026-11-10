<x-app-layout>
    <x-page-header
        title="Detail Mahasiswa"
        :subtitle="$mahasiswa->name"
    >
        <x-slot:breadcrumb>
            <x-breadcrumb :items="[['label' => 'Mahasiswa Bimbingan', 'url' => route('dosen.mahasiswa.index')], ['label' => 'Detail Mahasiswa']]" />
        </x-slot:breadcrumb>
        <x-slot:actions>
            <x-button :href="route('dosen.mahasiswa.index')" variant="secondary">
                <x-icon name="arrow-left" class="h-4 w-4" />
                Kembali
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <x-card>
                <div class="flex flex-col items-center text-center">
                    <x-avatar :name="$mahasiswa->name" size="xl" />
                    <h2 class="mt-3 text-lg font-semibold text-foreground">{{ $mahasiswa->name }}</h2>
                    <p class="text-sm text-muted-foreground">NIM {{ $mahasiswa->nim ?? '-' }}</p>
                    <a href="mailto:{{ $mahasiswa->email }}" class="mt-2 inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-primary-dark">
                        <x-icon name="mail" class="h-4 w-4" />
                        {{ $mahasiswa->email }}
                    </a>
                </div>

                <dl class="mt-6 grid grid-cols-3 gap-2 border-t border-border pt-4 text-center">
                    <div>
                        <dt class="text-xs text-muted-foreground">Total</dt>
                        <dd class="mt-1 text-lg font-semibold text-foreground">{{ $stats['total'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted-foreground">Menunggu</dt>
                        <dd class="mt-1 text-lg font-semibold text-warning">{{ $stats['pending'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted-foreground">Selesai</dt>
                        <dd class="mt-1 text-lg font-semibold text-success">{{ $stats['completed'] }}</dd>
                    </div>
                </dl>
            </x-card>
        </div>

        <div class="space-y-4 lg:col-span-2">
            <x-card :padding="false">
                <x-slot:header>
                    <h2 class="text-sm font-semibold text-foreground">Riwayat Pengajuan Konsultasi</h2>
                </x-slot:header>

                @if ($pengajuan->isEmpty())
                    <x-empty-state
                        icon="clipboard"
                        title="Belum ada pengajuan"
                        description="Mahasiswa ini belum pernah mengajukan konsultasi kepada Anda."
                    />
                @else
                    <ul class="divide-y divide-border">
                        @foreach ($pengajuan as $item)
                            <li>
                                <a href="{{ route('dosen.riwayat.show', $item) }}" class="flex items-center gap-3 px-5 py-4 transition-colors hover:bg-muted sm:px-6">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-muted text-muted-foreground">
                                        <x-icon name="document" class="h-4 w-4" />
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-medium text-foreground">{{ $item->topik }}</span>
                                        <span class="mt-0.5 block text-xs text-muted-foreground">
                                            {{ $item->jadwal?->tanggal?->translatedFormat('d M Y') ?? $item->created_at->translatedFormat('d M Y') }}
                                        </span>
                                    </span>
                                    <x-status-badge :status="$item->status" />
                                    <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-muted-foreground" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-card>

            <x-pagination :paginator="$pengajuan" />
        </div>
    </div>
</x-app-layout>
