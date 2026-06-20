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
