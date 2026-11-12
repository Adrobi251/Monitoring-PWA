@props(['padding' => 'p-5 sm:p-6'])

<div {{ $attributes->merge(['class' => 'glass rounded-xl']) }}>
    @isset($header)
        <div class="border-b border-border px-5 py-4 sm:px-6">
            {{ $header }}
        </div>
    @endisset

    <div class="{{ $padding }}">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="border-t border-border px-5 py-4 sm:px-6">
            {{ $footer }}
        </div>
    @endisset
</div>
