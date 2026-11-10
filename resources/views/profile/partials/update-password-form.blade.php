<section>
    <header>
        <h2 class="text-sm font-semibold text-foreground">Ubah Kata Sandi</h2>
        <p class="mt-1 text-sm text-muted-foreground">Gunakan kata sandi yang panjang dan unik agar akun tetap aman.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-5 space-y-4">
        @csrf
        @method('put')

        <x-form-field label="Kata Sandi Saat Ini" name="current_password">
            <x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </x-form-field>

        <x-form-field label="Kata Sandi Baru" name="password">
            <x-text-input id="update_password_password" name="password" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </x-form-field>

        <x-form-field label="Konfirmasi Kata Sandi Baru" name="password_confirmation">
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </x-form-field>

        <div class="flex items-center gap-3 pt-1">
            <x-primary-button>Perbarui Kata Sandi</x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-sm text-success">
                    Tersimpan.
                </p>
            @endif
        </div>
    </form>
</section>
