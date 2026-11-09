<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-2xl text-foreground leading-tight">
            {{ __('Detail Permohonan Konsultasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-card overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-6 bg-card border-b border-border">
                    <div class="mb-6">
                        <h3 class="font-semibold text-foreground mb-2">Mahasiswa</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-muted-foreground">Nama:</span>
                                <span class="font-medium">{{ $pengajuan->mahasiswa->name }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground">NIM:</span>
                                <span class="font-medium">{{ $pengajuan->mahasiswa->nim }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground">Topik:</span>
                                <span class="font-medium">{{ $pengajuan->topik }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground">Jadwal:</span>
                                <span class="font-medium">{{ $pengajuan->jadwal->tanggal->format('d M Y') }} - {{ $pengajuan->jadwal->jam_mulai }} - {{ $pengajuan->jadwal->jam_selesai }}</span>
                            </div>
                        </div>
                    </div>

                    @if($catatan)
                    <div class="mt-6">
                        <h3 class="font-semibold text-foreground mb-2">Catatan Konsultasi</h3>
                        <div class="bg-muted rounded-xl p-4">
                            <p class="text-sm text-foreground whitespace-pre-line"><strong>Ringkasan:</strong> {{ $catatan->ringkasan }}</p>
                            @if($catatan->arahan)
                                <p class="text-sm text-foreground mt-2 whitespace-pre-line"><strong>Arahan:</strong> {{ $catatan->arahan }}</p>
                            @endif
                            @if($catatan->tindak_lanjut)
                                <p class="text-sm text-foreground mt-2 whitespace-pre-line"><strong>Tindak Lanjut:</strong> {{ $catatan->tindak_lanjut }}</p>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="mt-6 text-muted-foreground">
                        <p>Belum ada catatan konsultasi untuk permohonan ini.</p>
                        <a href="{{ route('pengajuan.catatan.create', $pengajuan) }}" class="text-primary hover:text-primary/80 mt-2 block">Tambah Catatan</a>
                    </div>
                    @endif

                    <div class="mt-6 flex items-center justify-between">
                        <span class="text-sm text-muted-foreground">Status: <strong class="font-medium @switch($pengajuan->status)
                            @case('approved') text-accent
                            @case('rejected') text-destructive
                            @default text-amber-600 @endif">{{ ucfirst($pengajuan->status) }}</span>

                        @if(auth()->user()->role === 'dosen')
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('dosen.riwayat') }}" class="text-sm text-primary hover:text-primary/80">Kembali ke Riwayat</a>
                                @if($pengajuan->status === 'approved')
                                    <form method="POST" action="{{ route('pengajuan.status', $pengajuan) }}" style="display:inline">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()">
                                            <option value="approved">Disetujui</option>
                                            <option value="rejected" selected>Ditolak</option>
                                        </select>
                                        <input type="text" name="alasan_penolakan" placeholder="Alasan penolakan" class="ml-2 text-sm border-border rounded-lg p-1 shadow-sm" />
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>