<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Permohonan Konsultasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-800 mb-2">Mahasiswa</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Nama:</span>
                                <span class="font-medium">{{ $pengajuan->mahasiswa->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">NIM:</span>
                                <span class="font-medium">{{ $pengajuan->mahasiswa->nim }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Topik:</span>
                                <span class="font-medium">{{ $pengajuan->topik }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Jadwal:</span>
                                <span class="font-medium">{{ $pengajuan->jadwal->tanggal->format('d M Y') }} - {{ $pengajuan->jadwal->jam_mulai }} - {{ $pengajuan->jadwal->jam_selesai }}</span>
                            </div>
                        </div>
                    </div>

                    @if($catatan)
                    <div class="mt-6">
                        <h3 class="font-semibold text-gray-800 mb-2">Catatan Konsultasi</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-700 whitespace-pre-line"><strong>Ringkasan:</strong> {{ $catatan->ringkasan }}</p>
                            @if($catatan->arahan)
                                <p class="text-sm text-gray-700 mt-2 whitespace-pre-line"><strong>Arahan:</strong> {{ $catatan->arahan }}</p>
                            @endif
                            @if($catatan->tindak_lanjut)
                                <p class="text-sm text-gray-700 mt-2 whitespace-pre-line"><strong>Tindak Lanjut:</strong> {{ $catatan->tindak_lanjut }}</p>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="mt-6 text-gray-500">
                        <p>Belum ada catatan konsultasi untuk permohonan ini.</p>
                        <a href="{{ route('pengajuan.catatan.create', $pengajuan) }}" class="text-primary-600 hover:text-primary-900 mt-2 block">Tambah Catatan</a>
                    </div>
                    @endif

                    <div class="mt-6 flex items-center justify-between">
                        <span class="text-sm text-gray-600">Status: <strong class="font-medium @switch($pengajuan->status)
                            @case('approved') text-green-600
                            @case('rejected') text-red-600
                            @default text-yellow-600 @endif">{{ ucfirst($pengajuan->status) }}</span>

                        @if(auth()->user()->role === 'dosen')
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('dosen.riwayat') }}" class="text-sm text-primary-600 hover:text-primary-900">Kembali ke Riwayat</a>
                                @if($pengajuan->status === 'approved')
                                    <form method="POST" action="{{ route('pengajuan.status', $pengajuan) }}" style="display:inline">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()">
                                            <option value="approved">Disetujui</option>
                                            <option value="rejected" selected>Ditolak</option>
                                        </select>
                                        <input type="text" name="alasan_penolakan" placeholder="Alasan penolakan" class="ml-2 text-sm border rounded p-1" />
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