@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'h-11 w-full rounded-lg border border-border bg-surface px-3 text-sm text-foreground shadow-sm placeholder:text-muted-foreground focus:border-primary focus:ring-primary disabled:cursor-not-allowed disabled:bg-muted disabled:text-muted-foreground']) }}>
