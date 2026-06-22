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
            <div class="text-xs text-base-content/70 font-semibold">Asset No</div>
            <div class="font-medium">{{ $record->MachineNo ?? '-' }}</div>
        </div>

        <div class="col-span-2 md:col-span-4 border-t pt-4 mt-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-xs text-base-content/70 font-semibold">Description</div>
                    <div class="mt-1">
                        @if($record->description)
                            <x-badge value="{{ $record->description }}" class="{{ match($record->description) {
                                'TPM' => 'badge-primary',
                                'Preventive' => 'badge-info',
                                'Repair' => 'badge-warning',
                                default => 'badge-ghost'
                            } }} rounded-full" />
                        @else
                            -
                        @endif
                    </div>
                </div>
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
            </div>
        </div>
    </div>
</x-card>
