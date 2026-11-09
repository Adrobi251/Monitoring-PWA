<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-2xl text-foreground leading-tight">
            {{ __('Dashboard Dosen Wali') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-card overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-6 bg-card border-b border-border">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="bg-primary/5 p-4 rounded-xl">
                            <h3 class="text-sm font-medium text-primary">Total Mahasiswa</h3>
                            <p class="text-2xl font-bold text-primary">{{ $stats['total_mahasiswa'] }}</p>
                        </div>
                        <div class="bg-amber-50 p-4 rounded-xl">
                            <h3 class="text-sm font-medium text-amber-900">Total Pengajuan</h3>
                            <p class="text-2xl font-bold text-amber-800">{{ $stats['total_pengajuan'] }}</p>
                        </div>
                        <div class="bg-accent/5 p-4 rounded-xl">
                            <h3 class="text-sm font-medium text-accent">Menunggu Persetujuan</h3>
                            <p class="text-2xl font-bold text-accent">{{ $stats['pending'] }}</p>
                        </div>
                        <div class="bg-secondary/5 p-4 rounded-xl">
                            <h3 class="text-sm font-medium text-secondary">Disetujui</h3>
                            <p class="text-2xl font-bold text-secondary">{{ $stats['approved'] }}</p>
                        </div>
                    </div>

                    <div class="mb-6 p-4 bg-card rounded-xl border">
                        <h3 class="font-semibold text-foreground mb-2">Tugaskan Mahasiswa</h3>
                        <form method="POST" action="{{ route('dosen.mahasiswa.assign') }}" class="flex gap-2">
                            @csrf
                            <input type="text" name="nim" placeholder="NIM Mahasiswa" class="border-border rounded-lg px-3 py-2 flex-1 shadow-sm" required>
                            <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg">Tugaskan</button>
                        </form>
                        @if(session('success'))
                            <p class="text-accent mt-2">{{ session('success') }}</p>
                        @endif
                        @error('nim')
                            <p class="text-destructive mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-6 p-4 bg-accent/5 rounded-xl">
                        <h3 class="font-semibold text-foreground mb-2">Jadwal Konsultasi Mendatang</h3>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                            <div>
                                <p class="font-medium text-foreground">{{ $jadwalMendatang->tanggal->format('d M Y') }}</p>
                                <p class="text-sm text-muted-foreground">{{ $jadwalMendatang->jam_mulai }} - {{ $jadwalMendatang->jam_selesai }} | {{ $jadwalMendatang->lokasi ?? 'Tidak ditentukan' }}</p>
                            </div>
                            <span class="px-3 py-1 bg-accent/10 text-accent text-sm font-medium rounded-full">Aktif</span>
                        </div>
                    </div>
                    @endif

                    <h3 class="font-semibold text-foreground mb-4">Permohonan Terbaru</h3>
                    @if($riwayatTerbaru->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-border">
                            <thead class="bg-muted">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Mahasiswa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Topik</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-card divide-y divide-border">
                                @foreach($riwayatTerbaru as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-sm font-semibold text-primary">{{ strtoupper(substr($item->mahasiswa->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-foreground">{{ $item->mahasiswa->name }}</p>
                                                <p class="text-xs text-muted-foreground">{{ $item->mahasiswa->nim ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-foreground">{{ $item->topik }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground">{{ $item->created_at->format('d M Y') }}</td>
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
                                        <a href="{{ route('dosen.riwayat') }}" class="text-primary hover:text-primary/80">Detail</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8 text-muted-foreground">Belum ada permohonan konsultasi.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>