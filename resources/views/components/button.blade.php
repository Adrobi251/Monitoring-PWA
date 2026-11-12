@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'submit',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-medium rounded-lg transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:pointer-events-none disabled:opacity-50';

    $variants = [
        'primary' => 'bg-primary text-primary-foreground shadow-sm hover:bg-primary-dark',
        'secondary' => 'bg-surface text-foreground border border-border shadow-sm hover:bg-muted',
        'ghost' => 'text-muted-foreground hover:bg-muted hover:text-foreground',
        'danger' => 'bg-danger text-danger-foreground shadow-sm hover:bg-danger/90',
        'success' => 'bg-success text-success-foreground shadow-sm hover:bg-success/90',
        'outline' => 'border border-border bg-transparent text-foreground hover:bg-muted',
    ];

    $sizes = [
        'sm' => 'h-9 px-3 text-sm',
        'md' => 'h-11 px-4 text-sm',
        'lg' => 'h-12 px-6 text-base',
    ];

    $classes = trim($base.' '.($variants[$variant] ?? $variants['primary']).' '.($sizes[$size] ?? $sizes['md']));
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
