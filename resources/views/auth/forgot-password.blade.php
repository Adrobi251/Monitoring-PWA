<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-foreground">Lupa kata sandi?</h1>
        <p class="mt-1 text-sm text-muted-foreground">Masukkan email Anda, kami akan mengirim tautan untuk mengatur ulang kata sandi.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <x-form-field label="Email" name="email">
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@email.com" />
        </x-form-field>

        <x-primary-button class="w-full">Kirim Tautan Reset</x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-muted-foreground">
        <a href="{{ route('login') }}" class="font-medium text-primary hover:text-primary-dark">Kembali ke halaman masuk</a>
    </p>
</x-guest-layout>
