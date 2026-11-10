@php
    $user = auth()->user();
    $roleLabel = $user->role === 'dosen' ? 'Dosen Wali' : 'Mahasiswa';
@endphp

<div class="relative" x-data="{ open: false }" x-on:click.outside="open = false" x-on:keydown.escape.window="open = false">
    <button
        type="button"
        x-on:click="open = ! open"
        class="flex items-center gap-2 rounded-lg p-1 pr-2 text-left transition-colors hover:bg-muted focus-ring"
        :aria-expanded="open"
        aria-label="Menu pengguna"
    >
        <x-avatar :name="$user->name" size="sm" />
        <span class="hidden min-w-0 sm:block">
            <span class="block max-w-[10rem] truncate text-sm font-medium leading-tight text-foreground">{{ $user->name }}</span>
            <span class="block text-xs text-muted-foreground">{{ $roleLabel }}</span>
        </span>
        <x-icon name="chevron-down" class="hidden h-4 w-4 text-muted-foreground sm:block" />
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
        class="absolute right-0 z-50 mt-2 w-60 overflow-hidden rounded-xl border border-border bg-surface shadow-pop"
    >
        <div class="border-b border-border px-4 py-3">
            <p class="truncate text-sm font-semibold text-foreground">{{ $user->name }}</p>
            <p class="truncate text-xs text-muted-foreground">{{ $user->email }}</p>
            <p class="mt-1 text-xs text-muted-foreground">{{ $user->role === 'dosen' ? 'NIDN' : 'NIM' }}: {{ $user->role === 'dosen' ? ($user->nidn ?? '-') : ($user->nim ?? '-') }}</p>
        </div>

        <div class="p-1.5">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-foreground transition-colors hover:bg-muted">
                <x-icon name="user" class="h-4 w-4 text-muted-foreground" />
                Profil Saya
            </a>
            <a href="{{ route('notifications.index') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-foreground transition-colors hover:bg-muted">
                <x-icon name="bell" class="h-4 w-4 text-muted-foreground" />
                Notifikasi
            </a>
        </div>

        <div class="border-t border-border p-1.5">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-danger transition-colors hover:bg-danger/10">
                    <x-icon name="logout" class="h-4 w-4" />
                    Keluar
                </button>
            </form>
        </div>
    </div>
</div>
