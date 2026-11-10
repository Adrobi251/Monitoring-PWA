@props(['name', 'id' => null])

<select
    name="{{ $name }}"
    id="{{ $id ?? $name }}"
    {{ $attributes->merge(['class' => 'h-11 w-full rounded-lg border border-border bg-surface px-3 text-sm text-foreground shadow-sm focus:border-primary focus:ring-primary']) }}
>
    {{ $slot }}
</select>
