<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jadwal Konsultasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 mb-4">Jadwal Konsultasi Dosen Wali</h3>

                    @if($jadwal->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($jadwal as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->tanggal->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $item->lokasi ?? 'Tidak ditentukan' }}</td>
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
                                        @if($item->status === 'active')
                                        <form method="POST" action="{{ route('mahasiswa.pengajuan.store') }}">
                                            @csrf
                                            <input type="hidden" name="jadwal_id" value="{{ $item->id }}">
                                            <input type="text" name="topik" placeholder="Topik" required class="block w-full mb-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                            <textarea name="deskripsi" placeholder="Deskripsi" required rows="2" class="block w-full mb-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
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
                    <div class="text-center py-8 text-gray-500">Tidak ada jadwal konsultasi aktif.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>