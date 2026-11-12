<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-foreground">Masuk ke akun Anda</h1>
        <p class="mt-1 text-sm text-muted-foreground">Gunakan email dan kata sandi yang terdaftar.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <x-form-field label="Email" name="email">
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com" />
        </x-form-field>

        <x-form-field label="Kata Sandi" name="password">
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi" />
        </x-form-field>

        <div class="flex items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center gap-2">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-border text-primary shadow-sm focus:ring-primary">
                <span class="text-sm text-muted-foreground">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="rounded-sm text-sm font-medium text-primary hover:text-primary-dark focus-ring">Lupa kata sandi?</a>
            @endif
        </div>

        <x-primary-button class="w-full">Masuk</x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-muted-foreground">
        Belum punya akun?
        <a href="{{ route('register') }}" class="rounded-sm font-medium text-primary hover:text-primary-dark focus-ring">Daftar sebagai mahasiswa</a>
    </p>
</x-guest-layout>
