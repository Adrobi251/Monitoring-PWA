<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-foreground">Atur ulang kata sandi</h1>
        <p class="mt-1 text-sm text-muted-foreground">Buat kata sandi baru untuk akun Anda.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-form-field label="Email" name="email">
            <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
        </x-form-field>

        <x-form-field label="Kata Sandi Baru" name="password">
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
        </x-form-field>

        <x-form-field label="Konfirmasi Kata Sandi" name="password_confirmation">
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi" />
        </x-form-field>

        <x-primary-button class="w-full">Simpan Kata Sandi</x-primary-button>
    </form>
</x-guest-layout>
