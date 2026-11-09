<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-2xl text-foreground leading-tight">
            {{ __('Dashboard Mahasiswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-card overflow-hidden shadow-lg sm:rounded-xl border border-border">
                <div class="p-6 bg-card border-b border-border">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="bg-primary/5 p-4 rounded-xl border border-primary/10">
                            <h3 class="text-sm font-medium text-primary">Total Pengajuan</h3>
                            <p class="text-2xl font-bold text-primary">{{ $stats['total_pengajuan'] }}</p>
                        </div>
                        <div class="bg-accent/5 p-4 rounded-xl border border-accent/10">
                            <h3 class="text-sm font-medium text-accent">Menunggu</h3>
                            <p class="text-2xl font-bold text-accent">{{ $stats['pending'] }}</p>
                        </div>
                        <div class="bg-secondary/5 p-4 rounded-xl border border-secondary/10">
                            <h3 class="text-sm font-medium text-secondary">Disetujui</h3>
                            <p class="text-2xl font-bold text-secondary">{{ $stats['approved'] }}</p>
                        </div>
                        <div class="bg-muted p-4 rounded-xl border border-border">
                            <h3 class="text-sm font-medium text-muted-foreground">Selesai</h3>
                            <p class="text-2xl font-bold text-foreground">{{ $stats['completed'] }}</p>
                        </div>
                    </div>

                    @if($dosenWali)
                    <div class="mb-6 p-4 bg-muted rounded-xl border border-border">
                        <h3 class="font-serif font-semibold text-foreground mb-2">Dosen Wali Anda</h3>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center">
                                <span class="text-xl font-semibold text-primary">{{ strtoupper($dosenWali->name[0]) }}</span>
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-foreground">{{ $dosenWali->name }}</p>
                                <p class="text-sm text-muted-foreground">NIDN: {{ $dosenWali->nidn ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($jadwalTerdekat)
                    <div class="mb-6 p-4 bg-accent/5 rounded-xl border border-accent/10">
                        <h3 class="font-serif font-semibold text-foreground mb-2">Jadwal Konsultasi Terdekat</h3>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                            <div>
                                <p class="font-medium text-foreground">{{ $jadwalTerdekat->jadwal->tanggal->format('d M Y') }}</p>
                                <p class="text-sm text-muted-foreground">{{ $jadwalTerdekat->jadwal->jam_mulai }} - {{ $jadwalTerdekat->jadwal->jam_selesai }} | {{ $jadwalTerdekat->jadwal->lokasi ?? 'Tidak ditentukan' }}</p>
                            </div>
                            <span class="px-3 py-1 bg-accent/10 text-accent text-sm font-medium rounded-full">{{ $jadwalTerdekat->status }}</span>
                        </div>
                    </div>
                    @endif

                    <h3 class="font-serif font-semibold text-foreground mb-4">Riwayat Konsultasi Terbaru</h3>
                    @if($riwayatTerbaru->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-border">
                            <thead class="bg-muted">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Topik</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-card divide-y divide-border">
                                @foreach($riwayatTerbaru as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground">{{ $item->jadwal->tanggal->format('d M Y') ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-foreground">{{ $item->topik }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($item->status === 'pending') bg-amber-100 text-amber-800
                                            @elseif($item->status === 'approved') bg-accent/10 text-accent
                                            @elseif($item->status === 'rejected') bg-destructive/10 text-destructive
                                            @elseif($item->status === 'completed') bg-secondary/10 text-secondary
                                            @else bg-muted text-muted-foreground @endif">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('mahasiswa.riwayat') }}" class="text-primary hover:text-primary/80">Lihat</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8 text-muted-foreground">Belum ada riwayat konsultasi.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>