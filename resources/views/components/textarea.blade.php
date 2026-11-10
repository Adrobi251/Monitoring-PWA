@props(['name', 'id' => null, 'rows' => 3])

<textarea
    name="{{ $name }}"
    id="{{ $id ?? $name }}"
    rows="{{ $rows }}"
    {{ $attributes->merge(['class' => 'w-full rounded-lg border border-border bg-surface px-3 py-2.5 text-sm text-foreground shadow-sm placeholder:text-muted-foreground focus:border-primary focus:ring-primary']) }}
>{{ $slot }}</textarea>
