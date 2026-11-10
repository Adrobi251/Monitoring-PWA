<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-foreground">Verifikasi email</h1>
        <p class="mt-1 text-sm text-muted-foreground">
            Terima kasih telah mendaftar. Sebelum memulai, verifikasi alamat email Anda melalui tautan yang telah kami kirim.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-lg border border-success/25 bg-success/10 px-4 py-3 text-sm font-medium text-success">
            Tautan verifikasi baru telah dikirim ke alamat email Anda.
        </div>
    @endif

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>Kirim Ulang Email Verifikasi</x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-muted-foreground underline underline-offset-2 hover:text-foreground">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
