<x-app-layout>
    <x-page-header
        title="Notifikasi"
        subtitle="Pemberitahuan pengajuan dan status konsultasi Anda."
    >
        <x-slot:breadcrumb>
            <x-breadcrumb :items="[['label' => 'Notifikasi']]" />
        </x-slot:breadcrumb>
        <x-slot:actions>
            @if (auth()->user()->unreadNotifications()->count() > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <x-button type="submit" variant="secondary">
                        <x-icon name="check" class="h-4 w-4" />
                        Tandai Semua Dibaca
                    </x-button>
                </form>
            @endif
        </x-slot:actions>
    </x-page-header>

    @if ($notifications->isEmpty())
        <x-card>
            <x-empty-state
                icon="bell"
                title="Belum ada notifikasi"
                description="Notifikasi pengajuan dan perubahan status konsultasi akan muncul di sini."
            />
        </x-card>
    @else
        <div class="space-y-2">
            @foreach ($notifications as $notification)
                @php
                    $status = $notification->data['status'] ?? null;
                    $topik = $notification->data['topik'] ?? 'Pengajuan konsultasi';
                @endphp

                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                    @csrf
                    <button type="submit" class="flex w-full items-start gap-3 rounded-xl border border-border bg-surface px-4 py-4 text-left shadow-card transition-colors hover:bg-muted sm:items-center sm:px-5">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $notification->read_at ? 'bg-muted text-muted-foreground' : 'bg-primary/10 text-primary' }}">
                            <x-icon name="bell" class="h-5 w-5" />
                        </span>

                        <span class="min-w-0 flex-1">
                            <span class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-medium text-foreground">{{ $topik }}</span>
                                @if (! $notification->read_at)
                                    <span class="rounded-full bg-primary/10 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-primary">Baru</span>
                                @endif
                            </span>
                            <span class="mt-0.5 block text-xs text-muted-foreground">{{ $notification->created_at->translatedFormat('d M Y · H:i') }} &middot; {{ $notification->created_at->diffForHumans() }}</span>
                        </span>

                        @if ($status)
                            <x-status-badge :status="$status" class="shrink-0" />
                        @endif
                    </button>
                </form>
            @endforeach
        </div>

        <div class="mt-5">
            <x-pagination :paginator="$notifications" />
        </div>
    @endif
</x-app-layout>
