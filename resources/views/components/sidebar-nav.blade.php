@php
    $user = auth()->user();
    $isDosen = $user->role === 'dosen';
@endphp

<div class="flex h-full flex-col gap-6 overflow-y-auto px-4 py-5">
    <a href="{{ $isDosen ? route('dosen.dashboard') : route('mahasiswa.dashboard') }}" class="flex items-center gap-3 rounded-lg px-2 py-1 focus-ring">
        <x-brand-mark class="h-10 w-10 shrink-0 text-primary" />
        <span class="min-w-0">
            <span class="block truncate text-sm font-semibold leading-tight text-foreground">Konsultasi Akademik</span>
            <span class="block text-xs text-muted-foreground">{{ $isDosen ? 'Dosen Wali' : 'Mahasiswa' }}</span>
        </span>
    </a>

    @if ($isDosen)
        <nav class="flex flex-1 flex-col gap-6" aria-label="Navigasi utama">
            <div>
                <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Menu Utama</p>
                <div class="space-y-1">
                    <x-nav-item :href="route('dosen.dashboard')" :active="request()->routeIs('dosen.dashboard')" icon="dashboard">Dashboard</x-nav-item>
                    <x-nav-item :href="route('dosen.jadwal')" :active="request()->routeIs('dosen.jadwal*')" icon="calendar">Kelola Jadwal</x-nav-item>
                    <x-nav-item :href="route('dosen.riwayat')" :active="request()->routeIs('dosen.riwayat*', 'dosen.pengajuan.*')" icon="clipboard">Pengajuan Konsultasi</x-nav-item>
                    <x-nav-item :href="route('dosen.mahasiswa.index')" :active="request()->routeIs('dosen.mahasiswa.*')" icon="users">Mahasiswa Bimbingan</x-nav-item>
                </div>
            </div>

            <div>
                <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Akun</p>
                <div class="space-y-1">
                    <x-nav-item :href="route('notifications.index')" :active="request()->routeIs('notifications.*')" icon="bell">Notifikasi</x-nav-item>
                    <x-nav-item :href="route('profile.edit')" :active="request()->routeIs('profile.*')" icon="user">Profil</x-nav-item>
                </div>
            </div>
        </nav>
    @else
        <nav class="flex flex-1 flex-col gap-6" aria-label="Navigasi utama">
            <div>
                <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Menu Utama</p>
                <div class="space-y-1">
                    <x-nav-item :href="route('mahasiswa.dashboard')" :active="request()->routeIs('mahasiswa.dashboard')" icon="dashboard">Dashboard</x-nav-item>
                    <x-nav-item :href="route('mahasiswa.jadwal')" :active="request()->routeIs('mahasiswa.jadwal')" icon="calendar">Jadwal Konsultasi</x-nav-item>
                    <x-nav-item :href="route('mahasiswa.pengajuan.create')" :active="request()->routeIs('mahasiswa.pengajuan.create')" icon="plus">Ajukan Konsultasi</x-nav-item>
                    <x-nav-item :href="route('mahasiswa.riwayat')" :active="request()->routeIs('mahasiswa.riwayat', 'mahasiswa.pengajuan.show')" icon="clipboard">Riwayat Konsultasi</x-nav-item>
                    <x-nav-item :href="route('mahasiswa.dosen-wali')" :active="request()->routeIs('mahasiswa.dosen-wali')" icon="academic-cap">Dosen Wali</x-nav-item>
                </div>
            </div>

            <div>
                <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Akun</p>
                <div class="space-y-1">
                    <x-nav-item :href="route('notifications.index')" :active="request()->routeIs('notifications.*')" icon="bell">Notifikasi</x-nav-item>
                    <x-nav-item :href="route('profile.edit')" :active="request()->routeIs('profile.*')" icon="user">Profil</x-nav-item>
                </div>
            </div>
        </nav>
    @endif

    <div class="rounded-lg bg-secondary/70 px-3 py-3">
        <p class="text-xs font-medium text-foreground">{{ $user->name }}</p>
        <p class="mt-0.5 truncate text-xs text-muted-foreground">{{ $isDosen ? $user->nidn : $user->nim }}</p>
    </div>
</div>
