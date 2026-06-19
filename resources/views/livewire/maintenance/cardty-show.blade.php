<?php

use App\Services\CartyService;
use Livewire\Volt\Component;

new class extends Component {
    public $detailRecord;
    public $previousRecordId;
    public $nextRecordId;

    public function mount(int $id, CartyService $service): void
    {
        $this->detailRecord = $service->getById($id);
        if (!$this->detailRecord) {
            abort(404);
        }

        // Previous record (newer date/id since list is sorted desc)
        $prev = \App\Models\Carty::where('Date', '>', $this->detailRecord->Date)
            ->orWhere(function ($query) {
                $query->where('Date', $this->detailRecord->Date)
                      ->where('id', '>', $this->detailRecord->id);
            })
            ->orderBy('Date')->orderBy('id')
            ->first();
            
        $this->previousRecordId = $prev ? $prev->id : null;

        // Next record (older date/id)
        $next = \App\Models\Carty::where('Date', '<', $this->detailRecord->Date)
            ->orWhere(function ($query) {
                $query->where('Date', $this->detailRecord->Date)
                      ->where('id', '<', $this->detailRecord->id);
            })
            ->orderByDesc('Date')->orderByDesc('id')
            ->first();
            
        $this->nextRecordId = $next ? $next->id : null;
    }

    public function switchRecord(int $id, CartyService $service)
    {
        $this->mount($id, $service);
        $url = route('maintenance.cardty.show', $id);
        $this->js("history.pushState(null, '', '{$url}')");
    }

    public function getImageUrl($path)
    {
        if (!$path)
            return null;
        if (\Illuminate\Support\Str::startsWith($path, 'images/')) {
            return asset($path);
        }
        return asset('storage/' . $path);
    }
};
?>

<div class="space-y-6" x-data="{
         isFullscreen: false,
         toggleFullscreen() {
             if (!document.fullscreenElement) {
                 $el.requestFullscreen();
             } else {
                 document.exitFullscreen();
             }
         }
     }" @fullscreenchange.window="isFullscreen = !!document.fullscreenElement"
    @keydown.left.window="if(isFullscreen && $wire.previousRecordId) $wire.switchRecord($wire.previousRecordId)"
    @keydown.right.window="if(isFullscreen && $wire.nextRecordId) $wire.switchRecord($wire.nextRecordId)"
    :class="isFullscreen ? 'p-4 md:p-8 bg-base-100 overflow-y-auto relative' : ''">

    <!-- Floating Navigation in Fullscreen -->
    <div x-show="isFullscreen" style="display: none;" x-transition>
        @if($previousRecordId)
            <button wire:click="switchRecord({{ $previousRecordId }})" class="fixed left-4 top-1/2 transform -translate-y-1/2 btn btn-circle btn-ghost btn-lg bg-base-200/50 opacity-30 hover:opacity-100 z-50">
                <x-icon name="o-chevron-left" class="w-10 h-10" />
            </button>
        @endif
        @if($nextRecordId)
            <button wire:click="switchRecord({{ $nextRecordId }})" class="fixed right-4 top-1/2 transform -translate-y-1/2 btn btn-circle btn-ghost btn-lg bg-base-200/50 opacity-30 hover:opacity-100 z-50">
                <x-icon name="o-chevron-right" class="w-10 h-10" />
            </button>
        @endif
    </div>

    <!-- Header -->
    <x-header separator class="mb-4">
        <x-slot:title>
            <div class="flex items-center gap-2">
                <x-button icon="o-arrow-left" class="btn-circle btn-ghost btn-sm"
                    link="{{ route('maintenance.cardty') }}" tooltip-left="Kembali ke Index" />
                <span class="text-xl">Detail Maintenance Record</span>
            </div>
        </x-slot:title>

        <x-slot:actions>
            <div class="flex items-center gap-2">
                @if($previousRecordId)
                    <x-button icon="o-chevron-left" wire:click="switchRecord({{ $previousRecordId }})" 
                        class="btn-ghost btn-sm" tooltip="Previous Record" />
                @endif
                
                @if($nextRecordId)
                    <x-button icon="o-chevron-right" wire:click="switchRecord({{ $nextRecordId }})" 
                        class="btn-ghost btn-sm" tooltip="Next Record" />
                @endif

                <div class="divider divider-horizontal mx-0"></div>

                <x-button icon="o-arrows-pointing-out" label="Full Screen" @click="toggleFullscreen" x-show="!isFullscreen"
                    class="btn-ghost btn-sm" />
                <x-button icon="o-arrows-pointing-in" label="Exit Full Screen" @click="toggleFullscreen"
                    x-show="isFullscreen" style="display: none;" class="btn-ghost btn-sm" />

                @can('wr.update')
                    <x-button icon="o-pencil-square" label="Edit Record"
                        link="{{ route('maintenance.cardty.edit', $this->detailRecord->id) }}" class="btn-primary btn-sm" />
                @endcan
            </div>
        </x-slot:actions>
    </x-header>

    <div class="flex flex-col lg:flex-row">
        <!-- Left Column: Details -->
        <div class="w-full lg:w-1/2 lg:pr-3">
            <!-- General Info -->
            <div class="mb-6">
                <x-card title="General Information" shadow class="h-fit">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 block">Date</span>
                        <span
                            class="font-semibold">{{ $detailRecord->Date ? $detailRecord->Date->format('Y-m-d') : '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Shift</span>
                        <span class="font-semibold">{{ $detailRecord->Shift }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Group Line</span>
                        <span class="font-semibold">{{ $detailRecord->groupline }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Line Name</span>
                        <span class="font-semibold">{{ $detailRecord->LineName }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Machine Name</span>
                        <span class="font-semibold">{{ $detailRecord->MachineName }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Asset No</span>
                        <span class="font-semibold">{{ $detailRecord->MachineNo }}</span>
                    </div>
                </div>
                </x-card>
            </div>

            <!-- Problem & Action -->
            <div class="mb-6">
                <x-card title="Problem & Action Details" shadow class="h-fit">
                    <div class="space-y-4 text-sm">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-gray-500 block">Type of Problem</span>
                            <span class="font-semibold">{{ $detailRecord->typeofproblem }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Status</span>
                            <span class="font-semibold">{{ $detailRecord->Status }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500 block mb-1">Spare Parts Used</span>
                            @if($detailRecord->spareParts && $detailRecord->spareParts->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($detailRecord->spareParts as $sp)
                                        <span class="badge badge-outline badge-lg gap-2">
                                            {{ $sp->part_name }}
                                            <div class="badge badge-primary badge-sm">Qty: {{ $sp->pivot->qty }}</div>
                                        </span>
                                    @endforeach
                                </div>
                            @elseif($detailRecord->sparepartName)
                                <div class="flex flex-wrap gap-2">
                                    <span class="badge badge-outline badge-lg gap-2">
                                        {{ $detailRecord->sparepartName }}
                                        <div class="badge badge-primary badge-sm">Qty: {{ $detailRecord->sparepartQty ?: 1 }}</div>
                                    </span>
                                </div>
                            @else
                                <span class="font-semibold">-</span>
                            @endif
                        </div>
                    </div>
                    <hr class="border-base-300" />
                    <div class="text-left w-full">
                        <span class="text-gray-500 block mb-1">Problem</span>
                        <p class="font-semibold whitespace-pre-wrap bg-base-200 p-3 rounded-lg text-left">{{ $detailRecord->Problem ?: '-' }}</p>
                    </div>
                    <div class="text-left w-full">
                        <span class="text-gray-500 block mb-1">Cause</span>
                        <p class="font-semibold whitespace-pre-wrap bg-base-200 p-3 rounded-lg text-left">{{ $detailRecord->Cause ?: '-' }}</p>
                    </div>
                    <div class="text-left w-full">
                        <span class="text-gray-500 block mb-1">Action (Countermeasures)</span>
                        <p class="font-semibold whitespace-pre-wrap bg-base-200 p-3 rounded-lg text-left">{{ $detailRecord->Action ?: '-' }}</p>
                    </div>
                </div>
                </x-card>
            </div>

        </div>

        <!-- Right Column: Meta info -->
        <div class="w-full lg:w-1/2 lg:pl-3">
            <div class="mb-6">
                <x-card title="Time & Personnel" shadow class="h-fit">
                    <div class="space-y-4 text-sm">
                    <div>
                        <span class="text-gray-500 block mb-1">Personnel (PIC)</span>
                        <div class="flex flex-wrap gap-2">
                            @if(is_array($detailRecord->pics) && count($detailRecord->pics) > 0)
                                @foreach(array_filter($detailRecord->pics) as $pic)
                                    <span class="badge badge-primary">{{ $pic }}</span>
                                @endforeach
                            @else
                                <span class="badge badge-ghost">{{ $detailRecord->PIC ?? '-' }}</span>
                            @endif
                        </div>
                    </div>
                    <hr class="border-base-300" />
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-gray-500 block">Start Repair</span>
                            <span class="font-semibold">{{ $detailRecord->start_time ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Finish Repair</span>
                            <span class="font-semibold">{{ $detailRecord->finish_time ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">DownTime</span>
                            <span class="font-semibold text-error">{{ $detailRecord->DownTime }} mins</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Work Time</span>
                            <span class="font-semibold text-info">{{ $detailRecord->worktime }} mins</span>
                        </div>
                    </div>
                </div>
                </x-card>
            </div>

            <!-- Images -->
            <div class="mb-6">
                <x-card title="Traceability Images" shadow class="h-fit">
                    <div class="grid grid-cols-2 gap-4">
                    @if($detailRecord->filebefore1)
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-center mb-1">Before 1</span>
                            <a href="{{ $this->getImageUrl($detailRecord->filebefore1) }}" target="_blank">
                                <img src="{{ $this->getImageUrl($detailRecord->filebefore1) }}"
                                    class="rounded shadow object-cover h-32 w-full hover:opacity-80 transition"
                                    alt="Before 1" />
                            </a>
                        </div>
                    @endif
                    @if($detailRecord->filebefore2)
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-center mb-1">Before 2</span>
                            <a href="{{ $this->getImageUrl($detailRecord->filebefore2) }}" target="_blank">
                                <img src="{{ $this->getImageUrl($detailRecord->filebefore2) }}"
                                    class="rounded shadow object-cover h-32 w-full hover:opacity-80 transition"
                                    alt="Before 2" />
                            </a>
                        </div>
                    @endif
                    @if($detailRecord->fileafter1)
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-center mb-1">After 1</span>
                            <a href="{{ $this->getImageUrl($detailRecord->fileafter1) }}" target="_blank">
                                <img src="{{ $this->getImageUrl($detailRecord->fileafter1) }}"
                                    class="rounded shadow object-cover h-32 w-full hover:opacity-80 transition"
                                    alt="After 1" />
                            </a>
                        </div>
                    @endif
                    @if($detailRecord->fileafter2)
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-center mb-1">After 2</span>
                            <a href="{{ $this->getImageUrl($detailRecord->fileafter2) }}" target="_blank">
                                <img src="{{ $this->getImageUrl($detailRecord->fileafter2) }}"
                                    class="rounded shadow object-cover h-32 w-full hover:opacity-80 transition"
                                    alt="After 2" />
                            </a>
                        </div>
                    @endif
                    @if(!$detailRecord->filebefore1 && !$detailRecord->filebefore2 && !$detailRecord->fileafter1 && !$detailRecord->fileafter2)
                        <p class="text-sm text-gray-500 italic col-span-full">No images available for this record.</p>
                    @endif
                </div>
            </x-card>
            </div>
        </div>
    </div>
</div>