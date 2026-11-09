<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Dosen Wali') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-blue-900">Total Mahasiswa</h3>
                            <p class="text-2xl font-bold text-blue-800">{{ $stats['total_mahasiswa'] }}</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-yellow-900">Total Pengajuan</h3>
                            <p class="text-2xl font-bold text-yellow-800">{{ $stats['total_pengajuan'] }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-green-900">Menunggu Persetujuan</h3>
                            <p class="text-2xl font-bold text-green-800">{{ $stats['pending'] }}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-purple-900">Disetujui</h3>
                            <p class="text-2xl font-bold text-purple-800">{{ $stats['approved'] }}</p>
                        </div>
                    </div>

                    <div class="mb-6 p-4 bg-white rounded-lg border">
                        <h3 class="font-semibold text-gray-800 mb-2">Tugaskan Mahasiswa</h3>
                        <form method="POST" action="{{ route('dosen.mahasiswa.assign') }}" class="flex gap-2">
                            @csrf
                            <input type="text" name="nim" placeholder="NIM Mahasiswa" class="border rounded px-3 py-2 flex-1" required>
                            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded">Tugaskan</button>
                        </form>
                        @if(session('success'))
                            <p class="text-green-600 mt-2">{{ session('success') }}</p>
                        @endif
                        @error('nim')
                            <p class="text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-6 p-4 bg-green-50 rounded-lg">
                        <h3 class="font-semibold text-gray-800 mb-2">Jadwal Konsultasi Mendatang</h3>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                            <div>
                                <p class="font-medium text-gray-900">{{ $jadwalMendatang->tanggal->format('d M Y') }}</p>
                                <p class="text-sm text-gray-600">{{ $jadwalMendatang->jam_mulai }} - {{ $jadwalMendatang->jam_selesai }} | {{ $jadwalMendatang->lokasi ?? 'Tidak ditentukan' }}</p>
                            </div>
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">Aktif</span>
                        </div>
                    </div>
                    @endif

                    <h3 class="font-semibold text-gray-800 mb-4">Permohonan Terbaru</h3>
                    @if($riwayatTerbaru->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Topik</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($riwayatTerbaru as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-sm font-semibold text-primary-700">{{ strtoupper(substr($item->mahasiswa->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $item->mahasiswa->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $item->mahasiswa->nim ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $item->topik }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($item->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($item->status === 'approved') bg-green-100 text-green-800
                                            @elseif($item->status === 'rejected') bg-red-100 text-red-800
                                            @elseif($item->status === 'completed') bg-purple-100 text-purple-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('dosen.riwayat') }}" class="text-primary-600 hover:text-primary-900">Detail</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8 text-gray-500">Belum ada permohonan konsultasi.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>