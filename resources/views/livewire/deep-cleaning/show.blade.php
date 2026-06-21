<?php

use App\Models\DeepCleaning;
use Livewire\Volt\Component;

new class extends Component {
    public DeepCleaning $record;

    public function mount(int $id)
    {
        $this->record = DeepCleaning::findOrFail($id);
    }
};
?>

<div>
    <x-header separator>
        <x-slot:title>
            <div class="flex items-center gap-3">
                <x-button icon="o-arrow-left" class="btn-circle btn-ghost btn-sm" link="{{ route('deep-cleaning.index') }}" wire:navigate />
                <span>Deep Cleaning Details</span>
            </div>
        </x-slot:title>
    </x-header>

    <div class="grid grid-cols-1 gap-6">
        <!-- General Info -->
        <x-card title="General Information">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <div class="text-xs text-base-content/70 font-semibold">Date</div>
                    <div class="font-medium">{{ $record->Date ? $record->Date->format('l, d F Y') : '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-base-content/70 font-semibold">Line</div>
                    <div class="font-medium">{{ $record->LineName ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-base-content/70 font-semibold">Machine</div>
                    <div class="font-medium">{{ $record->MachineName ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-base-content/70 font-semibold">Status</div>
                    <div>
                        <x-badge label="{{ $record->status }}" 
                            class="{{ match(strtolower($record->status ?? '')) {
                                'scheduled' => 'badge-info',
                                'in progress' => 'badge-warning',
                                'done' => 'badge-success',
                                default => 'badge-ghost'
                            } }}" 
                        />
                    </div>
                </div>
                <div class="col-span-2 md:col-span-4 border-t pt-4 mt-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-base-content/70 font-semibold">PICs</div>
                            <div>
                                @if(is_array($record->pics) && count($record->pics) > 0)
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach($record->pics as $pic)
                                            <x-badge value="{{ $pic }}" class="badge-neutral" />
                                        @endforeach
                                    </div>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-base-content/70 font-semibold">Item Check</div>
                            <div class="font-medium text-warning">{{ $record->itemcheck ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Before & After Details -->
        <x-card title="Before & After Details">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- BEFORE -->
                <div class="space-y-3">
                    <div class="font-bold text-center text-lg bg-base-200 py-2 rounded-t-xl">BEFORE</div>
                    <div class="border rounded-b-xl aspect-[4/3] flex items-center justify-center bg-base-100 overflow-hidden shadow-sm">
                        @if($record->before_photo)
                            <img src="{{ asset('storage/' . $record->before_photo) }}" class="object-cover w-full h-full" alt="Before" />
                        @else
                            <x-icon name="o-photo" class="w-16 h-16 text-base-content/20" />
                        @endif
                    </div>
                    <div>
                        <div class="text-xs font-bold text-base-content/70 mb-1">FINDING / PROBLEM</div>
                        <div class="bg-base-200 p-3 rounded-lg text-sm min-h-16">{{ $record->description ?: '-' }}</div>
                    </div>
                </div>

                <!-- AFTER -->
                <div class="space-y-3">
                    <div class="font-bold text-center text-lg bg-base-200 py-2 rounded-t-xl">AFTER</div>
                    <div class="border rounded-b-xl aspect-[4/3] flex items-center justify-center bg-base-100 overflow-hidden shadow-sm">
                        @if($record->after_photo)
                            <img src="{{ asset('storage/' . $record->after_photo) }}" class="object-cover w-full h-full" alt="After" />
                        @else
                            <x-icon name="o-photo" class="w-16 h-16 text-base-content/20" />
                        @endif
                    </div>
                    <div>
                        <div class="text-xs font-bold text-base-content/70 mb-1">ACTION</div>
                        <div class="bg-base-200 p-3 rounded-lg text-sm min-h-16">{{ $record->action ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Spareparts -->
        @if($record->sparepart_id)
        <x-card title="Sparepart Used">
            <div class="table-responsive">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>Sparepart Name / ID</th>
                            <th class="text-center">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $record->sparepart_id }}</td>
                            <td class="text-center">{{ $record->sparepart_qty ?: '0' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-card>
        @endif
    </div>
</div>
