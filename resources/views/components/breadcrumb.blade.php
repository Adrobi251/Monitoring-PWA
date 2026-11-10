@props(['items' => []])

<nav aria-label="Breadcrumb">
    <ol class="flex flex-wrap items-center gap-1.5 text-xs text-muted-foreground">
        <li>
            <a href="{{ route('dashboard') }}" class="rounded transition-colors hover:text-foreground">Dashboard</a>
        </li>

        @foreach ($items as $item)
            <li class="flex items-center gap-1.5">
                <x-icon name="chevron-right" class="h-3.5 w-3.5 text-border" />
                @if (!empty($item['url']))
                    <a href="{{ $item['url'] }}" class="rounded transition-colors hover:text-foreground">{{ $item['label'] }}</a>
                @else
                    <span class="font-medium text-foreground" aria-current="page">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
