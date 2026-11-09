<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-2xl text-foreground leading-tight">
            {{ __('Riwayat Konsultasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-card overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-6 bg-card border-b border-border">
                    <h3 class="font-semibold text-foreground mb-4">Riwayat Konsultasi Saya</h3>

                    @if($riwayat->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-border">
                            <thead class="bg-muted">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Topik</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Dosen</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Alasan Penolakan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-card divide-y divide-border">
                                @foreach($riwayat as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground">{{ $item->jadwal->tanggal->format('d M Y') ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-foreground">{{ $item->topik }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground">{{ $item->dosen->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($item->status === 'pending') bg-amber-100 text-amber-800
                                            @elseif($item->status === 'approved') bg-accent/10 text-accent
                                            @elseif($item->status === 'rejected') bg-destructive/10 text-destructive
                                            @elseif($item->status === 'completed') bg-secondary/10 text-secondary
                                            @else bg-muted text-foreground @endif">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-foreground">{{ $item->alasan_penolakan ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $riwayat->links() }}</div>
                    @else
                    <div class="text-center py-8 text-muted-foreground">Belum ada riwayat konsultasi.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>