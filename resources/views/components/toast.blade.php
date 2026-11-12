@props([
    'type' => 'success',
    'message' => null,
])

@php
    $styles = [
        'success' => ['bg-success/10 text-success border-success/25', 'check-circle'],
        'error' => ['bg-danger/10 text-danger border-danger/25', 'x-circle'],
        'warning' => ['bg-warning/10 text-warning border-warning/25', 'alert-triangle'],
        'info' => ['bg-primary/10 text-primary border-primary/25', 'information-circle'],
    ];

    [$classes, $icon] = $styles[$type] ?? $styles['info'];
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-transition
    x-init="setTimeout(() => show = false, 4500)"
    {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-lg border bg-surface/90 px-4 py-3 text-sm shadow-card backdrop-blur-md '.$classes]) }}
    role="status"
>
    <x-icon :name="$icon" class="mt-0.5 h-4 w-4 shrink-0" />

    <p class="flex-1">{{ $message ?? $slot }}</p>

    <button type="button" x-on:click="show = false" class="shrink-0 rounded p-0.5 transition-opacity hover:opacity-70" aria-label="Tutup notifikasi">
        <x-icon name="x" class="h-4 w-4" />
    </button>
</div>
