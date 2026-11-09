<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-2xl text-foreground leading-tight">
            {{ __('Manajemen Jadwal Konsultasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-card overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-6 bg-card border-b border-border">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-foreground">Daftar Jadwal Konsultasi</h3>
                        <x-primary-button onclick="window.location='{{ route('dosen.jadwal.create') }}'">
                            {{ __('Tambah Jadwal') }}
                        </x-primary-button>
                    </div>

                    @if($jadwal->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-border">
                            <thead class="bg-muted">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Lokasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-card divide-y divide-border">
                                @foreach($jadwal as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground">{{ $item->tanggal->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground">{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</td>
                                    <td class="px-6 py-4 text-sm text-foreground">{{ $item->lokasi ?? 'Tidak ditentukan' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($item->status === 'active') bg-accent/10 text-accent
                                            @else bg-destructive/10 text-destructive @endif">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('dosen.jadwal.edit', $item->id) }}" class="text-primary hover:text-primary/80 mr-3">Edit</a>
                                        <form method="POST" action="{{ route('dosen.jadwal.destroy', $item->id) }}" class="inline" onsubmit="return confirm('Hapus jadwal ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-destructive hover:text-destructive">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $jadwal->links() }}</div>
                    @else
                    <div class="text-center py-8 text-muted-foreground">Belum ada jadwal konsultasi.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>