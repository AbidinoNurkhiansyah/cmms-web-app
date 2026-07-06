<?php

use App\Services\SparePartStockTakingService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, Toast;

    public string $date;
    public string $status;
    public string $search = '';
    
    // Modal state
    public bool $checkModalOpen = false;
    public $form_spare_part_id;
    public $form_part_number;
    public $form_part_name;
    public $form_last_stock;
    public $form_check_stock;
    public $form_remark;

    public function mount(string $date, string $status = 'all')
    {
        $this->date = $date;
        $this->status = $status;
    }

    public function openCheckModal($id, $partNumber, $partName, $lastStock)
    {
        $this->resetValidation();
        $this->form_spare_part_id = $id;
        $this->form_part_number = $partNumber;
        $this->form_part_name = $partName;
        $this->form_last_stock = $lastStock;
        $this->form_check_stock = null;
        $this->form_remark = null;
        $this->checkModalOpen = true;
    }

    public function saveCheck()
    {
        $this->validate([
            'form_check_stock' => 'required|numeric|min:0',
            'form_remark' => 'nullable|string|max:255',
        ], [
            'form_check_stock.required' => 'Actual Physical Stock harus diisi.',
            'form_check_stock.numeric' => 'Harus berupa angka.',
            'form_check_stock.min' => 'Stok tidak boleh negatif.',
        ]);

        app(SparePartStockTakingService::class)->store([
            'date_stock' => $this->date,
            'spare_part_id' => $this->form_spare_part_id,
            'in_qty' => 0,
            'out_qty' => 0,
            'last_stock' => $this->form_last_stock,
            'check_stock' => $this->form_check_stock,
            'remark' => $this->form_remark,
        ]);

        $this->checkModalOpen = false;
        $this->success('Stock Taking berhasil disimpan.');
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
    public function summary()
    {
        return app(SparePartStockTakingService::class)->getSummaryData($this->date);
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
    <x-header separator>
        <x-slot:title>
            <div class="flex items-center gap-3">
                <x-button icon="o-arrow-left" link="{{ route('spare-parts.stock-taking.index') }}" class="btn-ghost btn-sm" wire:navigate />
                <span>Detail Stock Taking: {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</span>
            </div>
        </x-slot:title>
        <x-slot:subtitle>
            <div class="ml-12 mt-1">
                Status: {{ $this->statusLabel }}
            </div>
        </x-slot:subtitle>
        <x-slot:actions>
            <div class="flex w-full sm:w-auto items-center gap-2">
                <x-input 
                    icon="o-magnifying-glass" 
                    placeholder="Search Part Number..." 
                    wire:model.live.debounce.300ms="search" 
                    class="input-sm w-full sm:w-64"
                    clearable
                />
            </div>
        </x-slot:actions>
    </x-header>

    @if($status === 'err')
    <div class="card bg-base-100 shadow-sm border border-base-200 mb-6">
        <div class="card-body p-4 bg-base-200/30 rounded-xl">
            <h3 class="text-sm font-bold uppercase mb-3 text-base-content/80">Stock Taking Summary</h3>
            <div class="overflow-x-auto">
                <table class="table table-sm table-zebra bg-base-100 border border-base-200 shadow-sm">
                    <thead class="bg-neutral text-neutral-content">
                        <tr>
                            <th class="text-right">Gap Over</th>
                            <th class="text-right">Gap Minus</th>
                            <th class="text-right">Gap Total</th>
                            <th class="text-right">Total Prices</th>
                            <th class="text-right">Prosen (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-right font-medium">{{ number_format($this->summary['gap_over'], 0, ',', '.') }}</td>
                            <td class="text-right font-medium">{{ number_format($this->summary['gap_minus'], 0, ',', '.') }}</td>
                            <td class="text-right font-bold">{{ number_format($this->summary['gap_total'], 0, ',', '.') }}</td>
                            <td class="text-right font-medium">{{ number_format($this->summary['total_prices'], 0, ',', '.') }}</td>
                            <td class="text-right font-bold {{ $this->summary['prosen'] > 0 ? 'text-error' : 'text-success' }}">
                                {{ number_format($this->summary['prosen'], 2, ',', '.') }}%
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

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
                            <th class="px-4 py-3 text-right">RP</th>
                            <th class="px-4 py-3">Remark</th>
                        @else
                            <th class="px-4 py-3 text-center">Location (Rack)</th>
                            <th class="px-4 py-3 text-center">Stock (System)</th>
                            <th class="px-4 py-3 text-center">Actual (Physical)</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->records as $record)
                        @if($status === 'not_found')
                            <tr wire:key="not-found-{{ $record->id }}" class="hover:bg-base-200/50 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium">
                                    <button 
                                        wire:click="openCheckModal({{ $record->id }}, '{{ $record->part_number }}', '{{ $record->part_name }}', {{ $record->last_stock }})"
                                        class="text-primary font-bold hover:underline hover:text-primary-focus focus:outline-none transition-colors"
                                    >
                                        {{ $record->part_number ?? '-' }}
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $record->part_name }}</td>
                                <td class="px-4 py-3 text-sm text-center">{{ $record->no_rack ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-center font-medium">{{ $record->last_stock }}</td>
                                <td class="px-4 py-3 text-sm text-center text-base-content/30 italic">Not Checked</td>
                            </tr>
                        @else
                            @php
                                $diff = $record->check_stock - $record->last_stock;
                                $diffClass = $diff === 0 ? 'text-success' : 'text-error';
                                $diffSymbol = $diff > 0 ? '+' : '';
                                $rp = abs($diff * ($record->sparePart->price_idr ?? 0));
                            @endphp
                            <tr wire:key="record-{{ $record->id }}" class="hover:bg-base-200/50 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium">{{ $record->sparePart->part_number ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $record->sparePart->part_name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-center font-medium">{{ $record->last_stock }}</td>
                                <td class="px-4 py-3 text-sm text-center font-bold">{{ $record->check_stock }}</td>
                                <td class="px-4 py-3 text-sm text-center font-bold {{ $diffClass }}">
                                    {{ $diffSymbol }}{{ $diff }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right">{{ number_format($rp, 0, ',', '.') }}</td>
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

    <!-- Check Stock Modal -->
    <x-modal wire:model="checkModalOpen" title="Check Physical Stock" subtitle="Update actual physical stock for {{ $form_part_number }}" separator>
        <div class="grid grid-cols-1 gap-4 mb-4">
            <div class="bg-base-200/50 p-4 rounded-lg flex flex-col gap-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-base-content/60">Part Name</span>
                    <span class="text-sm font-bold text-right">{{ $form_part_name }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-base-content/60">System Stock</span>
                    <span class="text-sm font-bold">{{ $form_last_stock }}</span>
                </div>
            </div>
            
            <x-input 
                label="Actual Physical Stock" 
                wire:model="form_check_stock" 
                type="number" 
                min="0"
                placeholder="0"
                icon="o-archive-box"
                hint="Enter the actual stock counted physically."
                required
            />
            
            <x-textarea 
                label="Remark" 
                wire:model="form_remark" 
                placeholder="Optional remark or findings..." 
                rows="2"
            />
        </div>
        
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.checkModalOpen = false" class="btn-ghost" />
            <x-button label="Save Stock" wire:click="saveCheck" class="btn-primary" icon="o-check" spinner="saveCheck" />
        </x-slot:actions>
    </x-modal>
</div>
