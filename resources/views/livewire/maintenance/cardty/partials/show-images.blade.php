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
