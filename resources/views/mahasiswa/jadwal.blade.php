<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-2xl text-foreground leading-tight">
            {{ __('Jadwal Konsultasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-card overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-6 bg-card border-b border-border">
                    <h3 class="font-semibold text-foreground mb-4">Jadwal Konsultasi Dosen Wali</h3>

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
                                            @if($item->status === 'pending') bg-amber-100 text-amber-800
                                            @elseif($item->status === 'approved') bg-accent/10 text-accent
                                            @elseif($item->status === 'rejected') bg-destructive/10 text-destructive
                                            @elseif($item->status === 'completed') bg-secondary/10 text-secondary
                                            @else bg-muted text-foreground @endif">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($item->status === 'active')
                                        <form method="POST" action="{{ route('mahasiswa.pengajuan.store') }}">
                                            @csrf
                                            <input type="hidden" name="jadwal_id" value="{{ $item->id }}">
                                            <input type="text" name="topik" placeholder="Topik" required class="block w-full mb-2 text-sm border-border rounded-md shadow-sm focus:border-primary focus:ring-primary">
                                            <textarea name="deskripsi" placeholder="Deskripsi" required rows="2" class="block w-full mb-2 text-sm border-border rounded-md shadow-sm focus:border-primary focus:ring-primary"></textarea>
                                            <x-primary-button class="text-sm">Ajukan Konsultasi</x-primary-button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8 text-muted-foreground">Tidak ada jadwal konsultasi aktif.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>