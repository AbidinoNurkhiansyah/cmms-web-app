<?php

use App\Services\SparePartStockTakingService;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $date;
    public string $status;
    public string $search = '';

    public function mount(string $date, string $status = 'all')
    {
        $this->date = $date;
        $this->status = $status;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search']);
        $this->resetPage();
    }

    #[\Livewire\Attributes\Computed]
    public function records()
    {
        return app(SparePartStockTakingService::class)->getDetailData($this->date, $this->status, 15, $this->search);
    }
    
    #[\Livewire\Attributes\Computed]
    public function statusLabel()
    {
        return match($this->status) {
            'ok' => 'Check (OK)',
            'err' => 'Error (Mismatch)',
            'not_found' => 'Not Found (Unchecked)',
            default => 'All Records'
        };
    }
};
?>

<div>
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <x-button icon="o-arrow-left" link="{{ route('spare-parts.stock-taking.index') }}" class="btn-sm btn-ghost" wire:navigate />
                <h1 class="text-2xl font-bold">Detail Stock Taking: {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h1>
            </div>
            <p class="text-sm text-base-content/70 ml-10">Status: <span class="font-semibold">{{ $this->statusLabel }}</span></p>
        </div>
        
        <div class="flex w-full sm:w-auto items-center gap-2">
            <x-input 
                icon="o-magnifying-glass" 
                placeholder="Search Part Number..." 
                wire:model.live.debounce.300ms="search" 
                class="input-sm w-full sm:w-64"
                clearable
            />
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body p-0 overflow-x-auto">
            <table class="table table-zebra table-sm">
                <thead class="bg-base-200 text-base-content/80 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Part Number</th>
                        <th class="px-4 py-3">Part Name</th>
                        @if($status !== 'not_found')
                            <th class="px-4 py-3 text-center">Last Stock (System)</th>
                            <th class="px-4 py-3 text-center">Check Stock (Physical)</th>
                            <th class="px-4 py-3 text-center">Diff</th>
                            <th class="px-4 py-3">Remark</th>
                        @else
                            <th class="px-4 py-3 text-center">System Stock</th>
                            <th class="px-4 py-3 text-right">Price</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->records as $record)
                        @if($status === 'not_found')
                            <tr wire:key="not-found-{{ $record->id }}" class="hover:bg-base-200/50 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium">{{ $record->part_number }}</td>
                                <td class="px-4 py-3 text-sm">{{ $record->part_name }}</td>
                                <td class="px-4 py-3 text-sm text-center">{{ $record->last_stock }}</td>
                                <td class="px-4 py-3 text-sm text-right">Rp {{ number_format($record->price_idr, 0, ',', '.') }}</td>
                            </tr>
                        @else
                            @php
                                $diff = $record->check_stock - $record->last_stock;
                                $diffClass = $diff === 0 ? 'text-success' : 'text-error';
                                $diffSymbol = $diff > 0 ? '+' : '';
                            @endphp
                            <tr wire:key="record-{{ $record->id }}" class="hover:bg-base-200/50 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium">{{ $record->sparePart->part_number ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $record->sparePart->part_name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-center font-medium">{{ $record->last_stock }}</td>
                                <td class="px-4 py-3 text-sm text-center font-bold">{{ $record->check_stock }}</td>
                                <td class="px-4 py-3 text-sm text-center font-bold {{ $diffClass }}">
                                    {{ $diffSymbol }}{{ $diff }}
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $record->remark ?? '-' }}</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="{{ $status === 'not_found' ? 4 : 6 }}" class="text-center py-8 text-base-content/60">
                                <x-icon name="o-inbox" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                                <p>Tidak ada data detail ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($this->records->hasPages())
        <div class="card-footer border-t border-base-200 p-4">
            {{ $this->records->links() }}
        </div>
        @endif
    </div>
</div>
