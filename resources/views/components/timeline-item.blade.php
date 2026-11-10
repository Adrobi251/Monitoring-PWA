@props([
    'title',
    'time' => null,
    'tone' => 'primary',
])

@php
    $tones = [
        'primary' => 'bg-primary',
        'success' => 'bg-success',
        'warning' => 'bg-warning',
        'danger' => 'bg-danger',
        'neutral' => 'bg-muted-foreground',
    ];
@endphp

<li {{ $attributes->merge(['class' => 'relative']) }}>
    <span class="absolute -left-[26px] top-1.5 h-2.5 w-2.5 rounded-full ring-4 ring-surface {{ $tones[$tone] ?? $tones['primary'] }}"></span>

    <div class="flex flex-col gap-0.5 sm:flex-row sm:items-baseline sm:justify-between">
        <p class="text-sm font-medium text-foreground">{{ $title }}</p>
        @if ($time)
            <p class="text-xs text-muted-foreground">{{ $time }}</p>
        @endif
    </div>

    @if (trim($slot) !== '')
        <div class="mt-1 text-sm text-muted-foreground">{{ $slot }}</div>
    @endif
</li>
