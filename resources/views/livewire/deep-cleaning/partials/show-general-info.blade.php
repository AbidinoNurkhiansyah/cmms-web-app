<div class="bg-base-100 border border-base-300 rounded-xl p-4 shadow-sm">
    <div class="flex items-center justify-between mb-3 border-b border-base-200 pb-2">
        <h3 class="font-bold text-base">General Information</h3>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
        <div>
            <div class="text-[11px] text-base-content/60 font-semibold uppercase tracking-wider">Date</div>
            <div class="font-medium text-sm">{{ $record->Date ? $record->Date->format('d M Y') : '-' }}</div>
        </div>
        
        <div>
            <div class="text-[11px] text-base-content/60 font-semibold uppercase tracking-wider">Line</div>
            <div class="font-medium text-sm truncate">{{ $record->LineName ?? '-' }}</div>
        </div>
        
        <div class="md:col-span-2">
            <div class="text-[11px] text-base-content/60 font-semibold uppercase tracking-wider">Machine & Asset No</div>
            <div class="font-medium text-sm truncate">{{ $record->MachineName ?? '-' }} <span class="text-base-content/50 font-normal">({{ $record->MachineNo ?? '-' }})</span></div>
        </div>
        
        <div>
            <div class="text-[11px] text-base-content/60 font-semibold uppercase tracking-wider">Type</div>
            <div class="mt-0.5">
                @if($record->description)
                    <x-badge value="{{ $record->description }}" class="{{ match($record->description) {
                        'TPM' => 'badge-primary',
                        'Preventive' => 'badge-info',
                        'Repair' => 'badge-warning',
                        default => 'badge-ghost'
                    } }} badge-sm rounded-full" />
                @else
                    <span class="text-sm text-base-content/50">-</span>
                @endif
            </div>
        </div>
        
        <div>
            <div class="text-[11px] text-base-content/60 font-semibold uppercase tracking-wider">PICs</div>
            <div class="flex flex-wrap gap-1 mt-0.5">
                @if(is_array($record->pics) && count($record->pics) > 0)
                    @foreach($record->pics as $pic)
                        <x-badge value="{{ $pic }}" class="badge-neutral badge-sm" />
                    @endforeach
                @else
                    <span class="text-sm text-base-content/50">-</span>
                @endif
            </div>
        </div>
    </div>
</div>