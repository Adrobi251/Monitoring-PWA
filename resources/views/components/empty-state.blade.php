@props([
    'title',
    'description' => null,
    'icon' => 'document',
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center px-6 py-12 text-center']) }}>
    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
        <x-icon :name="$icon" class="h-6 w-6" />
    </div>

    <h3 class="mt-4 text-sm font-semibold text-foreground">{{ $title }}</h3>

    @if ($description)
        <p class="mt-1 max-w-sm text-sm text-muted-foreground">{{ $description }}</p>
    @endif

    @isset($action)
        <div class="mt-5">{{ $action }}</div>
    @endisset
</div>
