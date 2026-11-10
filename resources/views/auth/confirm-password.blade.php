<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-foreground">Konfirmasi kata sandi</h1>
        <p class="mt-1 text-sm text-muted-foreground">Area ini memerlukan konfirmasi kata sandi sebelum Anda melanjutkan.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <x-form-field label="Kata Sandi" name="password">
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi" />
        </x-form-field>

        <x-primary-button class="w-full">Konfirmasi</x-primary-button>
    </form>
</x-guest-layout>
