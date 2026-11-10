@props([
    'label',
    'name',
    'for' => null,
    'hint' => null,
])

<div {{ $attributes->only('class') }}>
    <label for="{{ $for ?? $name }}" class="block text-sm font-medium text-foreground">
        {{ $label }}
    </label>

    <div class="mt-1.5">
        {{ $slot }}
    </div>

    @if ($hint)
        <p class="mt-1.5 text-xs text-muted-foreground">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="mt-1.5 text-sm text-danger">{{ $message }}</p>
    @enderror
</div>
