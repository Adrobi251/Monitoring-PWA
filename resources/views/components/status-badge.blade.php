@props(['status'])

@php
    $map = [
        'pending' => ['Menunggu', 'bg-warning/10 text-warning ring-warning/25'],
        'approved' => ['Disetujui', 'bg-success/10 text-success ring-success/25'],
        'rejected' => ['Ditolak', 'bg-danger/10 text-danger ring-danger/25'],
        'completed' => ['Selesai', 'bg-primary/10 text-primary ring-primary/25'],
        'cancelled' => ['Dibatalkan', 'bg-muted text-muted-foreground ring-border'],
        'active' => ['Aktif', 'bg-success/10 text-success ring-success/25'],
        'inactive' => ['Tidak Aktif', 'bg-muted text-muted-foreground ring-border'],
    ];

    [$label, $classes] = $map[$status] ?? [ucfirst($status), 'bg-muted text-muted-foreground ring-border'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset '.$classes]) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
    {{ $label }}
</span>
