@php
    $unreadCount = auth()->user()->unreadNotifications()->count();
    $latestNotifications = auth()->user()->notifications()->limit(5)->get();
@endphp

<div class="relative" x-data="{ open: false }" x-on:click.outside="open = false" x-on:keydown.escape.window="open = false">
    <button
        type="button"
        x-on:click="open = ! open"
        class="relative inline-flex h-10 w-10 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground focus-ring"
        aria-label="Notifikasi"
        :aria-expanded="open"
    >
        <x-icon name="bell" class="h-5 w-5" />
        @if ($unreadCount > 0)
            <span class="absolute right-1.5 top-1.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-danger px-1 text-[10px] font-semibold text-white">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="absolute right-0 z-50 mt-2 w-80 overflow-hidden rounded-xl border border-border bg-surface shadow-pop"
    >
        <div class="flex items-center justify-between border-b border-border px-4 py-3">
            <p class="text-sm font-semibold text-foreground">Notifikasi</p>
            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-xs font-medium text-primary hover:text-primary-dark">Tandai semua dibaca</button>
                </form>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto">
            @forelse ($latestNotifications as $notification)
                @php
                    $status = $notification->data['status'] ?? null;
                    $topik = $notification->data['topik'] ?? 'Pengajuan konsultasi';
                @endphp
                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                    @csrf
                    <button type="submit" class="flex w-full items-start gap-3 border-b border-border px-4 py-3 text-left transition-colors last:border-0 hover:bg-muted">
                        <span class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $notification->read_at ? 'bg-muted text-muted-foreground' : 'bg-primary/10 text-primary' }}">
                            <x-icon name="bell" class="h-4 w-4" />
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-medium text-foreground">{{ $topik }}</span>
                            <span class="mt-0.5 block text-xs text-muted-foreground">{{ $notification->created_at->diffForHumans() }}</span>
                        </span>
                        @if ($status)
                            <x-status-badge :status="$status" class="shrink-0" />
                        @endif
                    </button>
                </form>
            @empty
                <p class="px-4 py-8 text-center text-sm text-muted-foreground">Belum ada notifikasi.</p>
            @endforelse
        </div>

        <div class="border-t border-border px-4 py-2.5 text-center">
            <a href="{{ route('notifications.index') }}" class="text-xs font-medium text-primary hover:text-primary-dark">Lihat semua notifikasi</a>
        </div>
    </div>
</div>
