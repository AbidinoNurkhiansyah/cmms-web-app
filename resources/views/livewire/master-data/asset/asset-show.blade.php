<?php

use App\Services\AssetService;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    public int $assetId;

    public function mount(int $id): void
    {
        $this->assetId = $id;
    }

    public function with(AssetService $assetService): array
    {
        return [
            'asset' => $assetService->getAssetById($this->assetId),
        ];
    }
};
?>

<div>
    <x-header title="{{ $asset->machine_name }}" subtitle="Asset No: {{ $asset->asset_no }}" separator>
        <x-slot:actions>
            <x-button label="Back" icon="o-arrow-left" class="btn-ghost" link="/assets" wire:navigate />
        </x-slot:actions>
    </x-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Asset Info Card --}}
        <x-card class="lg:col-span-1">
            @if($asset->machine_photo)
                <img src="{{ Storage::url($asset->machine_photo) }}" alt="{{ $asset->machine_name }}"
                     class="w-full rounded-lg mb-4 object-cover" style="max-height:220px">
            @else
                <div class="w-full rounded-lg mb-4 bg-base-200 flex items-center justify-center" style="height:180px">
                    <x-icon name="o-photo" class="w-16 h-16 opacity-20" />
                </div>
            @endif

            <table class="table table-sm w-full">
                <tr><td class="font-semibold opacity-60 w-1/2">Asset No</td><td class="font-mono">{{ $asset->asset_no }}</td></tr>
                <tr><td class="font-semibold opacity-60">Line</td><td>{{ $asset->line_name ?? '—' }}</td></tr>
                <tr><td class="font-semibold opacity-60">Machine</td><td>{{ $asset->machine_name ?? '—' }}</td></tr>
                <tr><td class="font-semibold opacity-60">Maker</td><td>{{ $asset->maker ?? '—' }}</td></tr>
                <tr><td class="font-semibold opacity-60">Year</td><td>{{ $asset->manufacture_year ?? '—' }}</td></tr>
                <tr>
                    <td class="font-semibold opacity-60">Rank</td>
                    <td>
                        @if($asset->machine_rank)
                            <x-badge label="{{ $asset->machine_rank }}"
                                class="{{
                                    match($asset->machine_rank) {
                                        'A' => 'badge-error',
                                        'B' => 'badge-warning',
                                        'C' => 'badge-info',
                                        'D' => 'badge-success',
                                        default => 'badge-ghost'
                                    }
                                }}" />
                        @else
                            —
                        @endif
                    </td>
                </tr>
                <tr><td class="font-semibold opacity-60">Classification</td><td>{{ $asset->classification ?? '—' }}</td></tr>
            </table>
        </x-card>

        {{-- History tabs placeholder (will be filled as other modules are built) --}}
        <div class="lg:col-span-2 space-y-4">

            <x-card title="Cardty History">
                <div class="text-center py-8 opacity-40">
                    <x-icon name="o-clipboard-document-list" class="w-10 h-10 mx-auto mb-2" />
                    <p class="text-sm">Cardty module will populate this section.</p>
                </div>
            </x-card>

            <x-card title="Checksheet History">
                <div class="text-center py-8 opacity-40">
                    <x-icon name="o-document-check" class="w-10 h-10 mx-auto mb-2" />
                    <p class="text-sm">Checksheet module will populate this section.</p>
                </div>
            </x-card>

            <x-card title="Overhaul History">
                <div class="text-center py-8 opacity-40">
                    <x-icon name="o-cog-8-tooth" class="w-10 h-10 mx-auto mb-2" />
                    <p class="text-sm">Overhaul module will populate this section.</p>
                </div>
            </x-card>

        </div>
    </div>
</div>
