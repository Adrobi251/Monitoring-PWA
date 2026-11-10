@props([
    'jadwal',
    'pengajuanStatus' => null,
    'actionUrl' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-4 rounded-xl border border-border bg-surface p-4 shadow-card sm:flex-row sm:items-center sm:justify-between']) }}>
    <div class="flex items-start gap-4">
        <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-lg bg-primary/10 text-primary">
            <span class="text-lg font-semibold leading-none">{{ $jadwal->tanggal->format('d') }}</span>
            <span class="mt-0.5 text-[10px] font-medium uppercase tracking-wide">{{ $jadwal->tanggal->translatedFormat('M') }}</span>
        </div>

        <div class="min-w-0">
            <p class="text-sm font-semibold text-foreground">
                {{ $jadwal->tanggal->translatedFormat('l, d F Y') }}
            </p>
            <p class="mt-1 flex items-center gap-1.5 text-sm text-muted-foreground">
                <x-icon name="clock" class="h-4 w-4" />
                {{ $jadwal->jam_mulai }}–{{ $jadwal->jam_selesai }}
                @if ($jadwal->lokasi)
                    <span class="text-border">•</span>
                    {{ $jadwal->lokasi }}
                @endif
            </p>
        </div>
    </div>

    <div class="flex items-center gap-2 sm:shrink-0">
        <x-status-badge :status="$jadwal->status" />

        @if ($pengajuanStatus)
            <x-status-badge :status="$pengajuanStatus" />
        @elseif ($actionUrl)
            <x-button :href="$actionUrl" size="sm">
                Ajukan
                <x-icon name="arrow-right" class="h-4 w-4" />
            </x-button>
        @endif
    </div>
</div>
