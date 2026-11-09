<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Catatan Konsultasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-800 mb-2">Detail Pengajuan</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Mahasiswa:</span>
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
                                <span class="text-gray-500">Tanggal:</span>
                                <span class="font-medium">{{ $pengajuan->jadwal->tanggal->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('pengajuan.catatan.store', $pengajuan) }}">
                        @csrf

                        <div class="mb-4">
                            <label for="ringkasan" class="block text-sm font-medium text-gray-700">Ringkasan</label>
                            <textarea name="ringkasan" id="ringkasan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ old('ringkasan') }}</textarea>
                            @error('ringkasan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="arahan" class="block text-sm font-medium text-gray-700">Arahan</label>
                            <textarea name="arahan" id="arahan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ old('arahan') }}</textarea>
                            @error('arahan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="tindak_lanjut" class="block text-sm font-medium text-gray-700">Tindak Lanjut</label>
                            <textarea name="tindak_lanjut" id="tindak_lanjut" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ old('tindak_lanjut') }}</textarea>
                            @error('tindak_lanjut')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('dosen.riwayat.show', $pengajuan) }}" class="mr-4 text-sm text-gray-600 hover:text-gray-800">Batal</a>
                            <x-primary-button>{{ __('Simpan Catatan') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
