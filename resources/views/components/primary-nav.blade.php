@props(['variant' => 'pill'])

@php
    $items = auth()->user()->role === 'dosen'
        ? [
            ['route' => 'dosen.dashboard', 'active' => ['dosen.dashboard'], 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['route' => 'dosen.jadwal', 'active' => ['dosen.jadwal*'], 'icon' => 'calendar', 'label' => 'Jadwal'],
            ['route' => 'dosen.riwayat', 'active' => ['dosen.riwayat*', 'dosen.pengajuan.*'], 'icon' => 'clipboard', 'label' => 'Pengajuan'],
            ['route' => 'dosen.mahasiswa.index', 'active' => ['dosen.mahasiswa.*'], 'icon' => 'users', 'label' => 'Mahasiswa'],
        ]
        : [
            ['route' => 'mahasiswa.dashboard', 'active' => ['mahasiswa.dashboard'], 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['route' => 'mahasiswa.jadwal', 'active' => ['mahasiswa.jadwal'], 'icon' => 'calendar', 'label' => 'Jadwal'],
            ['route' => 'mahasiswa.pengajuan.create', 'active' => ['mahasiswa.pengajuan.create'], 'icon' => 'plus', 'label' => 'Ajukan'],
            ['route' => 'mahasiswa.riwayat', 'active' => ['mahasiswa.riwayat', 'mahasiswa.pengajuan.show'], 'icon' => 'clipboard', 'label' => 'Riwayat'],
            ['route' => 'mahasiswa.dosen-wali', 'active' => ['mahasiswa.dosen-wali'], 'icon' => 'academic-cap', 'label' => 'Dosen Wali'],
        ];
@endphp

@if ($variant === 'pill')
    <div {{ $attributes->merge(['class' => 'flex items-center gap-1']) }}>
        @foreach ($items as $item)
            @php($isActive = request()->routeIs(...$item['active']))
            <a href="{{ route($item['route']) }}"
               @if ($isActive) aria-current="page" @endif
               class="press-glow inline-flex items-center gap-2 whitespace-nowrap rounded-full px-2.5 py-1.5 text-sm font-medium transition-colors focus-ring {{ $isActive ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-secondary hover:text-foreground' }}">
                <x-icon :name="$item['icon']" class="h-4 w-4 shrink-0" />
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>
@else
    <ul {{ $attributes->merge(['class' => 'flex items-stretch']) }}>
        @foreach ($items as $item)
            @php($isActive = request()->routeIs(...$item['active']))
            <li class="flex-1">
                <a href="{{ route($item['route']) }}"
                   @if ($isActive) aria-current="page" @endif
                   class="press-glow flex flex-col items-center gap-1 rounded-xl px-1 py-1.5 text-xs font-medium transition-colors focus-ring {{ $isActive ? 'text-primary' : 'text-muted-foreground hover:text-foreground' }}">
                    <span class="flex h-7 w-12 items-center justify-center rounded-full transition-colors {{ $isActive ? 'bg-primary text-primary-foreground' : '' }}">
                        <x-icon :name="$item['icon']" class="h-5 w-5" />
                    </span>
                    <span class="max-w-full truncate">{{ $item['label'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>
@endif
