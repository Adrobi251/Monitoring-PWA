<button {{ $attributes->merge(['type' => 'submit', 'class' => 'press-glow inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-transparent bg-primary px-4 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary-dark focus:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:pointer-events-none disabled:opacity-50']) }}>
    {{ $slot }}
</button>
