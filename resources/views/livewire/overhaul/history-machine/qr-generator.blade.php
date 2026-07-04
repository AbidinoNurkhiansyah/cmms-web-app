<?php

use Livewire\Volt\Component;
use App\Models\Asset;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new #[Layout('layouts.app')] class extends Component {
    use Toast, WithPagination;

    public string $search = '';
    public array $selectedAssets = [];
    public bool $selectAll = false;

    public function with(): array
    {
        $query = Asset::query();
        if ($this->search) {
            $query->where('asset_no', 'like', '%' . $this->search . '%')
                  ->orWhere('machine_name', 'like', '%' . $this->search . '%');
        }

        return [
            'assets' => $query->orderBy('asset_no')->paginate(15),
        ];
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedAssets = [];
            $this->selectAll = false;
        } else {
            // Get all IDs matching the current search without pagination
            $query = Asset::query();
            if ($this->search) {
                $query->where('asset_no', 'like', '%' . $this->search . '%')
                      ->orWhere('machine_name', 'like', '%' . $this->search . '%');
            }
            $this->selectedAssets = $query->pluck('id')->map(fn($id) => (string)$id)->toArray();
            $this->selectAll = true;
        }
    }

    public function printSelected()
    {
        if (empty($this->selectedAssets)) {
            $this->warning('Pilih minimal satu aset untuk dicetak.');
            return;
        }
        
        $ids = implode(',', $this->selectedAssets);
        $url = route('overhaul.history-machine.qr-print', ['assets' => $ids]);
        $this->js("window.open('{$url}', '_blank')");
    }
}; ?>

<div class="space-y-6">
    <div class="flex items-center gap-4 pb-4 border-b border-base-200">
        <x-button icon="o-arrow-left" link="{{ route('overhaul.history-machine.index') }}" class="btn-circle btn-ghost"
            tooltip="Kembali" spinner wire:navigate />
        <div>
            <h1 class="text-2xl font-bold">Generator QR Code Massal</h1>
        </div>
    </div>

    <div class="text-sm text-base-content/70">
        <p>Pilih asset-asset di bawah ini untuk mencetak QR Code secara bersamaan. Link QR Code akan diarahkan ke riwayat mesin sesuai server aktif saat ini.</p>
        <p class="mt-1"><strong>Format URL Target:</strong> <code class="bg-base-200 px-1 py-0.5 rounded text-primary">{{ url('/overhaul/history-machine?filter_asset_id={ASSET_ID}') }}</code></p>
    </div>

    <div class="flex flex-col sm:flex-row justify-between gap-4 bg-base-200/50 p-4 rounded-lg items-center border border-base-200">
        <div class="w-full sm:w-1/3">
            <x-input wire:model.live.debounce.300ms="search" placeholder="Cari Asset No / Nama Mesin..." icon="o-magnifying-glass" clearable />
        </div>
        <div class="flex gap-2 w-full sm:w-auto overflow-x-auto">
            <x-button label="{{ $selectAll ? 'Batal Semua' : 'Pilih Semua' }}" icon="{{ $selectAll ? 'o-x-mark' : 'o-check' }}" wire:click="toggleSelectAll" class="btn-outline" spinner />
            <x-button label="Cetak QR Terpilih" icon="o-printer" wire:click="printSelected" class="btn-primary" spinner />
        </div>
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead class="bg-base-200">
                    <tr>
                        <th class="w-16 text-center">Pilih</th>
                        <th>Asset No</th>
                        <th>Nama Mesin</th>
                        <th>Line</th>
                        <th>Machine Rank</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                        <tr class="hover:bg-base-200/50 transition-colors">
                            <td class="text-center">
                                <x-checkbox wire:model.live="selectedAssets" value="{{ $asset->id }}" class="checkbox-primary" />
                            </td>
                            <td class="font-bold">{{ $asset->asset_no }}</td>
                            <td>{{ $asset->machine_name }}</td>
                            <td>{{ $asset->line_name ?? '-' }}</td>
                            <td>
                                @if($asset->machine_rank)
                                    <x-badge value="{{ $asset->machine_rank }}" class="badge-neutral" />
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-base-content/50">
                                <div class="flex flex-col items-center justify-center">
                                    <x-icon name="o-inbox" class="w-12 h-12 mb-2 text-base-content/30" />
                                    <p>Tidak ada mesin yang ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
    
    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $assets->links() }}
    </div>
</div>
