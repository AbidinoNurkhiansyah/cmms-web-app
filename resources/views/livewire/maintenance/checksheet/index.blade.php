<?php

use App\Models\Asset;
use Livewire\WithPagination;
use Livewire\Volt\Component;

new class extends Component {
    use WithPagination;
    public ?string $selectedLine = null;
    public string $searchAsset = '';

    public function mount()
    {
        $userLine = auth()->user()?->line_name;
        if (!empty($userLine)) {
            $this->selectedLine = $userLine;
        }
    }

    public function getIsSpecialUserProperty(): bool
    {
        return !empty(auth()->user()?->line_name);
    }

    public function getLinesProperty()
    {
        if ($this->isSpecialUser) {
            return collect([auth()->user()->line_name]);
        }
        return Asset::select('line_name')->distinct()->orderBy('line_name')->pluck('line_name');
    }

    public function getAssetsProperty()
    {
        return Asset::select('id', 'asset_no', 'line_name', 'machine_name')
            ->when($this->selectedLine, function ($query) {
                $query->where('line_name', $this->selectedLine);
            })
            ->when($this->searchAsset, function ($query) {
                $query->where(function ($q) {
                    $q->where('asset_no', 'like', "%{$this->searchAsset}%")
                        ->orWhere('machine_name', 'like', "%{$this->searchAsset}%");
                });
            })
            ->orderBy('asset_no')
            ->paginate(24);
    }

    public function selectAsset($assetNo)
    {
        return redirect()->route('maintenance.checksheet.detail', ['assetNo' => $assetNo]);
    }
};
?>

<div>
    @include('livewire.maintenance.checksheet.partials.header')
    @include('livewire.maintenance.checksheet.partials.filter')
    @include('livewire.maintenance.checksheet.partials.table')
</div>