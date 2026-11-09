<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-2xl text-foreground leading-tight">
            {{ __('Edit Jadwal Konsultasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-card overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-6 bg-card border-b border-border">
                    <form method="POST" action="{{ route('dosen.jadwal.update', $jadwal) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="tanggal" class="block text-sm font-medium text-foreground">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $jadwal->tanggal->format('Y-m-d') ?? $jadwal->tanggal->format('Y-m-d') ?? '') }}" class="mt-1 block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary">
                            @error('tanggal')
                                <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="jam_mulai" class="block text-sm font-medium text-foreground">Jam Mulai</label>
                                <input type="time" name="jam_mulai" id="jam_mulai" value="{{ old('jam_mulai', $jadwal->jam_mulai) }}" class="mt-1 block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary">
                                @error('jam_mulai')
                                    <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="jam_selesai" class="block text-sm font-medium text-foreground">Jam Selesai</label>
                                <input type="time" name="jam_selesai" id="jam_selesai" value="{{ old('jam_selesai', $jadwal->jam_selesai) }}" class="mt-1 block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary">
                                @error('jam_selesai')
                                    <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="lokasi" class="block text-sm font-medium text-foreground">Lokasi</label>
                            <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi', $jadwal->lokasi) }}" class="mt-1 block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary">
                            @error('lokasi')
                                <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-foreground">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary">
                                <option value="active" {{ old('status', $jadwal->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status', $jadwal->status) === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>

                        @if($sudahDipakai)
                        <div class="mb-4 p-3 bg-amber-100 border border-amber-400 text-amber-800 rounded-lg">
                            <strong>Peringatan:</strong> Jadwal ini sudah digunakan untuk konsultasi yang disetujui. Perubahan mungkin mempengaruhi jadwal konsultasi yang sudah direncanakan.
                        </div>
                        @endif

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('dosen.jadwal') }}" class="mr-4 text-sm text-muted-foreground hover:text-foreground">Batal</a>
                            <x-primary-button>{{ __('Perbarui Jadwal') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>