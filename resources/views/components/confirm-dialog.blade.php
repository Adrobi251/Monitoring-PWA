@props([
    'name',
    'title',
    'description' => null,
    'action',
    'method' => 'DELETE',
    'confirmLabel' => 'Hapus',
])

<x-modal :name="$name" maxWidth="md" focusable>
    <form method="POST" action="{{ $action }}" class="p-6">
        @csrf
        @method($method)

        <div class="flex items-start gap-4">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-danger/10 text-danger">
                <x-icon name="alert-triangle" class="h-5 w-5" />
            </div>

            <div class="min-w-0">
                <h2 class="text-lg font-semibold text-foreground">{{ $title }}</h2>

                @if ($description)
                    <p class="mt-1 text-sm text-muted-foreground">{{ $description }}</p>
                @endif
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <x-button type="button" variant="secondary" x-on:click="$dispatch('close')">Batal</x-button>
            <x-button variant="danger">{{ $confirmLabel }}</x-button>
        </div>
    </form>
</x-modal>
