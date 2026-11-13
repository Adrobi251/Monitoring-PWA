<button {{ $attributes->merge(['type' => 'submit', 'class' => 'press-glow inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-transparent bg-danger px-4 text-sm font-medium text-danger-foreground shadow-sm transition-colors hover:bg-danger/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-danger focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:pointer-events-none disabled:opacity-50']) }}>
    {{ $slot }}
</button>
