@props(['class' => ''])

<div
    data-offline-banner
    style="display: none"
    role="status"
    aria-live="polite"
    {{ $attributes->merge(['class' => 'fixed inset-x-4 bottom-4 z-[60] mx-auto max-w-md items-start gap-3 rounded-xl border border-warning/30 bg-surface px-4 py-3 shadow-pop sm:inset-x-auto sm:right-6 sm:mx-0']) }}
>
    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-warning/10 text-warning">
        <x-icon name="wifi-off" class="h-4 w-4" />
    </span>
    <div class="min-w-0">
        <p class="text-sm font-medium text-foreground">Anda sedang offline</p>
        <p class="mt-0.5 text-xs leading-relaxed text-muted-foreground">
            Periksa koneksi internet. Pengajuan dan perubahan data tidak dapat dikirim sampai koneksi kembali.
        </p>
    </div>
</div>
