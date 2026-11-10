@props(['padding' => true])

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-xl border border-border bg-surface shadow-card']) }}>
    @isset($toolbar)
        <div class="border-b border-border px-4 py-3 sm:px-5">{{ $toolbar }}</div>
    @endisset

    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] text-left text-sm">
            {{ $slot }}
        </table>
    </div>

    @isset($footer)
        <div class="border-t border-border px-4 py-3 sm:px-5">{{ $footer }}</div>
    @endisset
</div>
