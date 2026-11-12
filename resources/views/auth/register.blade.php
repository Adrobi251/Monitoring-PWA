<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-foreground">Daftar akun mahasiswa</h1>
        <p class="mt-1 text-sm text-muted-foreground">Lengkapi data berikut untuk membuat akun konsultasi akademik.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <x-form-field label="Nama Lengkap" name="name">
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama lengkap" />
        </x-form-field>

        <x-form-field label="NIM" name="nim" hint="Nomor Induk Mahasiswa.">
            <x-text-input id="nim" type="text" name="nim" :value="old('nim')" placeholder="Masukkan NIM" autocomplete="username" />
        </x-form-field>

        <x-form-field label="Email" name="email">
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com" />
        </x-form-field>

        <x-form-field label="Kata Sandi" name="password">
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
        </x-form-field>

        <x-form-field label="Konfirmasi Kata Sandi" name="password_confirmation">
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi" />
        </x-form-field>

        <x-primary-button class="w-full">Daftar</x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-muted-foreground">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="rounded-sm font-medium text-primary hover:text-primary-dark focus-ring">Masuk</a>
    </p>
</x-guest-layout>
