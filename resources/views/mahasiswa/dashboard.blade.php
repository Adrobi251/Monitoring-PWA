<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Mahasiswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-blue-900">Total Pengajuan</h3>
                            <p class="text-2xl font-bold text-blue-800">{{ $stats['total_pengajuan'] }}</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-yellow-900">Menunggu</h3>
                            <p class="text-2xl font-bold text-yellow-800">{{ $stats['pending'] }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-green-900">Disetujui</h3>
                            <p class="text-2xl font-bold text-green-800">{{ $stats['approved'] }}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-purple-900">Selesai</h3>
                            <p class="text-2xl font-bold text-purple-800">{{ $stats['completed'] }}</p>
                        </div>
                    </div>

                    @if($dosenWali)
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-semibold text-gray-800 mb-2">Dosen Wali Anda</h3>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                                <span class="text-xl font-semibold text-primary-700">{{ strtoupper($dosenWali->name[0]) }}</span>
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-gray-900">{{ $dosenWali->name }}</p>
                                <p class="text-sm text-gray-600">NIDN: {{ $dosenWali->nidn ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($jadwalTerdekat)
                    <div class="mb-6 p-4 bg-green-50 rounded-lg">
                        <h3 class="font-semibold text-gray-800 mb-2">Jadwal Konsultasi Terdekat</h3>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                            <div>
                                <p class="font-medium text-gray-900">{{ $jadwalTerdekat->jadwal->tanggal->format('d M Y') }}</p>
                                <p class="text-sm text-gray-600">{{ $jadwalTerdekat->jadwal->jam_mulai }} - {{ $jadwalTerdekat->jadwal->jam_selesai }} | {{ $jadwalTerdekat->jadwal->lokasi ?? 'Tidak ditentukan' }}</p>
                            </div>
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $jadwalTerdekat->status }}</span>
                        </div>
                    </div>
                    @endif

                    <h3 class="font-semibold text-gray-800 mb-4">Riwayat Konsultasi Terbaru</h3>
                    @if($riwayatTerbaru->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Topik</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($riwayatTerbaru as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->jadwal->tanggal->format('d M Y') ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $item->topik }}</td>
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
                                        <a href="{{ route('mahasiswa.riwayat') }}" class="text-primary-600 hover:text-primary-900">Lihat</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8 text-gray-500">Belum ada riwayat konsultasi.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>