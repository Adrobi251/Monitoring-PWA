@props(['href', 'active' => false, 'icon' => 'dashboard'])

<a href="{{ $href }}"
   @if ($active) aria-current="page" @endif
   {{ $attributes->merge(['class' => 'group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors '.($active ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-secondary hover:text-foreground')]) }}>
    <x-icon :name="$icon" class="h-5 w-5 shrink-0" />
    <span class="truncate">{{ $slot }}</span>
</a>
