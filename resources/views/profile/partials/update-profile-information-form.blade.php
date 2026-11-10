<section>
    <header>
        <h2 class="text-sm font-semibold text-foreground">Informasi Profil</h2>
        <p class="mt-1 text-sm text-muted-foreground">Perbarui nama dan alamat email akun Anda.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-5 space-y-4">
        @csrf
        @method('patch')

        <x-form-field label="Nama Lengkap" name="name">
            <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
        </x-form-field>

        <x-form-field label="Email" name="email">
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-foreground">
                        Email Anda belum terverifikasi.
                        <button form="send-verification" class="font-medium text-primary underline underline-offset-2 hover:text-primary-dark">
                            Kirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-success">Tautan verifikasi baru telah dikirim ke email Anda.</p>
                    @endif
                </div>
            @endif
        </x-form-field>

        <div class="flex items-center gap-3 pt-1">
            <x-primary-button>Simpan Perubahan</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-sm text-success">
                    Tersimpan.
                </p>
            @endif
        </div>
    </form>
</section>
