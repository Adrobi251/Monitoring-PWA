<x-app-layout>
    <x-page-header
        title="Mahasiswa Bimbingan"
        subtitle="Daftar mahasiswa yang berada di bawah bimbingan akademik Anda."
    >
        <x-slot:breadcrumb>
            <x-breadcrumb :items="[['label' => 'Mahasiswa Bimbingan']]" />
        </x-slot:breadcrumb>
    </x-page-header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            <x-filter-bar>
                <x-form-field label="Cari mahasiswa" name="q" class="w-full sm:w-72">
                    <x-search-input name="q" placeholder="Nama, NIM, atau email" />
                </x-form-field>
                <div class="flex gap-2">
                    <x-button type="submit">
                        <x-icon name="search" class="h-4 w-4" />
                        Cari
                    </x-button>
                    <x-button :href="route('dosen.mahasiswa.index')" variant="secondary">Reset</x-button>
                </div>
            </x-filter-bar>

            @if ($mahasiswa->isEmpty())
                <x-card>
                    <x-empty-state
                        icon="users"
                        title="Belum ada mahasiswa bimbingan"
                        description="Tugaskan mahasiswa menggunakan NIM pada formulir di samping."
                    />
                </x-card>
            @else
                <div class="space-y-3 md:hidden">
                    @foreach ($mahasiswa as $item)
                        <a href="{{ route('dosen.mahasiswa.show', $item) }}" class="block rounded-xl border border-border bg-surface p-4 shadow-card">
                            <div class="flex items-center gap-3">
                                <x-avatar :name="$item->name" size="md" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-foreground">{{ $item->name }}</p>
                                    <p class="text-xs text-muted-foreground">NIM {{ $item->nim ?? '-' }}</p>
                                </div>
                                <x-icon name="chevron-right" class="h-4 w-4 text-muted-foreground" />
                            </div>
                            <p class="mt-2 truncate text-xs text-muted-foreground">{{ $item->email }}</p>
                            <p class="mt-2 text-xs text-muted-foreground">{{ $item->pengajuan_count }} pengajuan konsultasi</p>
                        </a>
                    @endforeach
                </div>

                <x-table class="hidden md:block">
                    <thead class="border-b border-border bg-muted/60">
                        <tr>
                            <th scope="col" class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Mahasiswa</th>
                            <th scope="col" class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Email</th>
                            <th scope="col" class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Pengajuan</th>
                            <th scope="col" class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-muted-foreground">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($mahasiswa as $item)
                            <tr class="transition-colors hover:bg-muted/50">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <x-avatar :name="$item->name" size="sm" />
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-medium text-foreground">{{ $item->name }}</p>
                                            <p class="text-xs text-muted-foreground">NIM {{ $item->nim ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-muted-foreground">{{ $item->email }}</td>
                                <td class="px-5 py-4 text-sm text-muted-foreground">{{ $item->pengajuan_count }}</td>
                                <td class="px-5 py-4 text-right">
                                    <x-button :href="route('dosen.mahasiswa.show', $item)" variant="ghost" size="sm">Detail</x-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>

                <x-pagination :paginator="$mahasiswa" />
            @endif
        </div>

        <div class="space-y-6">
            <x-card>
                <h2 class="text-sm font-semibold text-foreground">Tugaskan Mahasiswa</h2>
                <p class="mt-1 text-sm text-muted-foreground">Tambahkan mahasiswa sebagai bimbingan Anda menggunakan NIM.</p>

                <form method="POST" action="{{ route('dosen.mahasiswa.assign') }}" class="mt-4 space-y-3">
                    @csrf
                    <x-form-field label="NIM Mahasiswa" name="nim">
                        <x-text-input type="text" name="nim" :value="old('nim')" placeholder="Masukkan NIM" required />
                    </x-form-field>
                    <x-button type="submit" class="w-full">
                        <x-icon name="plus" class="h-4 w-4" />
                        Tugaskan
                    </x-button>
                </form>
            </x-card>

            <x-card>
                <h2 class="text-sm font-semibold text-foreground">Ringkasan</h2>
                <div class="mt-3 flex items-center justify-between text-sm">
                    <span class="text-muted-foreground">Total bimbingan</span>
                    <span class="font-semibold text-foreground">{{ $mahasiswa->total() }}</span>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
