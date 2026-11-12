@props([
    'label',
    'value',
    'tone' => 'primary',
    'hint' => null,
])

@php
    $tones = [
        'primary' => 'bg-primary/10 text-primary',
        'success' => 'bg-success/10 text-success',
        'warning' => 'bg-warning/10 text-warning',
        'danger' => 'bg-danger/10 text-danger',
        'neutral' => 'bg-muted text-muted-foreground',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'glass rounded-xl p-5']) }}>
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-sm font-medium text-muted-foreground">{{ $label }}</p>
            <p class="mt-2 text-3xl font-semibold tracking-tight text-foreground">{{ $value }}</p>
            @if ($hint)
                <p class="mt-1 text-xs text-muted-foreground">{{ $hint }}</p>
            @endif
        </div>

        @isset($icon)
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg {{ $tones[$tone] ?? $tones['primary'] }}">
                {{ $icon }}
            </div>
        @endisset
    </div>
</div>
