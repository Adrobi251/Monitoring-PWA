@props([
    'name' => 'q',
    'value' => null,
    'placeholder' => 'Cari...',
])

<div class="relative">
    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-muted-foreground">
        <x-icon name="search" class="h-4 w-4" />
    </span>

    <input
        type="search"
        name="{{ $name }}"
        value="{{ $value ?? request($name) }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'h-11 w-full rounded-lg border border-border bg-surface pl-9 pr-3 text-sm text-foreground shadow-sm placeholder:text-muted-foreground focus:border-primary focus:ring-primary']) }}
    >
</div>
