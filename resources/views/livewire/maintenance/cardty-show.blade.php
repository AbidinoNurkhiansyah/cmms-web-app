<?php

use App\Services\CartyService;
use Livewire\Volt\Component;

new class extends Component {
    public $detailRecord;

    public function mount(int $id, CartyService $service): void
    {
        $this->detailRecord = $service->getById($id);
        if (!$this->detailRecord) {
            abort(404);
        }
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
    :class="isFullscreen ? 'p-4 md:p-8 bg-base-100 overflow-y-auto' : ''">
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
            <x-button icon="o-arrows-pointing-out" label="Full Screen" @click="toggleFullscreen" x-show="!isFullscreen"
                class="btn-ghost btn-sm" />
            <x-button icon="o-arrows-pointing-in" label="Exit Full Screen" @click="toggleFullscreen"
                x-show="isFullscreen" style="display: none;" class="btn-ghost btn-sm" />

            @can('wr.update')
                <x-button icon="o-pencil-square" label="Edit Record"
                    link="{{ route('maintenance.cardty.edit', $this->detailRecord->id) }}" class="btn-primary btn-sm" />
            @endcan
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
                            <span class="text-gray-500 block">Spare Part</span>
                            <span class="font-semibold">{{ $detailRecord->sparepartName ?: '-' }} <span
                                    class="text-gray-400 font-normal">(Qty:
                                    {{ $detailRecord->sparepartQty ?: 0 }})</span></span>
                        </div>
                    </div>
                    <hr class="border-base-300" />
                    <div>
                        <span class="text-gray-500 block mb-1">Problem</span>
                        <p class="font-semibold whitespace-pre-wrap bg-base-200 p-3 rounded-lg">
                            {{ $detailRecord->Problem ?: '-' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 block mb-1">Cause</span>
                        <p class="font-semibold whitespace-pre-wrap bg-base-200 p-3 rounded-lg">
                            {{ $detailRecord->Cause ?: '-' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 block mb-1">Action (Countermeasures)</span>
                        <p class="font-semibold whitespace-pre-wrap bg-base-200 p-3 rounded-lg">
                            {{ $detailRecord->Action ?: '-' }}</p>
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