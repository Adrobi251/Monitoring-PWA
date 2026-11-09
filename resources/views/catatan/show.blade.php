<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-2xl text-foreground leading-tight">
            {{ __('Catatan Konsultasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-card overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-6 bg-card border-b border-border">
                    <div class="mb-6">
                        <h3 class="font-semibold text-foreground mb-2">Detail Pengajuan</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-muted-foreground">Mahasiswa:</span>
                                <span class="font-medium">{{ $catatan->pengajuan->mahasiswa->name }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground">NIM:</span>
                                <span class="font-medium">{{ $catatan->pengajuan->mahasiswa->nim }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground">Topik:</span>
                                <span class="font-medium">{{ $catatan->pengajuan->topik }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground">Tanggal:</span>
                                <span class="font-medium">{{ $catatan->pengajuan->jadwal->tanggal->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-foreground">Ringkasan</h4>
                            <p class="mt-1 text-sm text-foreground whitespace-pre-line">{{ $catatan->ringkasan }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-foreground">Arahan</h4>
                            <p class="mt-1 text-sm text-foreground whitespace-pre-line">{{ $catatan->arahan }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-foreground">Tindak Lanjut</h4>
                            <p class="mt-1 text-sm text-foreground whitespace-pre-line">{{ $catatan->tindak_lanjut ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        @if(auth()->user()->role === 'dosen')
                            <a href="{{ route('pengajuan.catatan.edit', [$catatan->pengajuan, $catatan]) }}" class="text-sm text-primary hover:text-primary/80 mr-4">Edit Catatan</a>
                        @endif
                        <a href="{{ auth()->user()->role === 'dosen' ? route('dosen.riwayat.show', $catatan->pengajuan) : route('mahasiswa.riwayat') }}" class="text-sm text-muted-foreground hover:text-foreground">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
