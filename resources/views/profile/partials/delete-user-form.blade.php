<section>
    <header>
        <h2 class="text-sm font-semibold text-danger">Hapus Akun</h2>
        <p class="mt-1 text-sm text-muted-foreground">
            Setelah akun dihapus, seluruh data dan sumber daya terkait akan dihapus permanen. Pastikan Anda sudah menyimpan informasi yang diperlukan.
        </p>
    </header>

    <x-danger-button
        class="mt-4"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Hapus Akun</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-foreground">Yakin ingin menghapus akun?</h2>

            <p class="mt-1 text-sm text-muted-foreground">
                Seluruh data akan dihapus permanen. Masukkan kata sandi Anda untuk mengonfirmasi.
            </p>

            <div class="mt-4">
                <x-input-label for="password" value="Kata Sandi" class="sr-only" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" placeholder="Kata sandi" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-danger-button>Hapus Akun</x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
