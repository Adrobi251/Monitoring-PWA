@php
    $isDosen = $user->role === 'dosen';
@endphp

<x-app-layout>
    <x-page-header
        title="Profil Pengguna"
        subtitle="Kelola informasi akun dan keamanan Anda."
    >
        <x-slot:breadcrumb>
            <x-breadcrumb :items="[['label' => 'Profil']]" />
        </x-slot:breadcrumb>
    </x-page-header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <x-card>
                <div class="flex flex-col items-center text-center">
                    <x-avatar :name="$user->name" size="xl" />
                    <h2 class="mt-3 text-lg font-semibold text-foreground">{{ $user->name }}</h2>
                    <p class="text-sm text-muted-foreground">{{ $user->email }}</p>
                    <span class="mt-2 inline-flex items-center rounded-full bg-primary/10 px-2.5 py-1 text-xs font-medium text-primary">
                        {{ $isDosen ? 'Dosen Wali' : 'Mahasiswa' }}
                    </span>
                </div>

                <dl class="mt-6 space-y-3 border-t border-border pt-4 text-sm">
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted-foreground">{{ $isDosen ? 'NIDN' : 'NIM' }}</dt>
                        <dd class="font-medium text-foreground">{{ $isDosen ? ($user->nidn ?? '-') : ($user->nim ?? '-') }}</dd>
                    </div>

                    @if (! $isDosen)
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-muted-foreground">Dosen Wali</dt>
                            <dd class="text-right font-medium text-foreground">{{ $user->dosenWali?->name ?? '-' }}</dd>
                        </div>
                    @endif

                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted-foreground">Terdaftar</dt>
                        <dd class="font-medium text-foreground">{{ $user->created_at->translatedFormat('d M Y') }}</dd>
                    </div>
                </dl>
            </x-card>
        </div>

        <div class="space-y-6 lg:col-span-2">
            <x-card>
                @include('profile.partials.update-profile-information-form')
            </x-card>

            <x-card>
                @include('profile.partials.update-password-form')
            </x-card>

            <x-card>
                @include('profile.partials.delete-user-form')
            </x-card>
        </div>
    </div>
</x-app-layout>
