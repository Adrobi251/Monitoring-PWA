@props([
    'title',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div class="min-w-0">
        @isset($breadcrumb)
            <div class="mb-2">{{ $breadcrumb }}</div>
        @endisset

        <h1 class="text-2xl font-semibold tracking-tight text-foreground sm:text-3xl">{{ $title }}</h1>

        @if ($subtitle)
            <p class="mt-1 text-sm text-muted-foreground">{{ $subtitle }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex flex-wrap items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</div>
