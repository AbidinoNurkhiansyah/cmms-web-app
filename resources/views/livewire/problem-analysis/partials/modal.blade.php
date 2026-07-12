<x-modal wire:model="detailModal" title="Detail Information - {{ $detailCategory }}" class="backdrop-blur-sm" box-class="w-11/12 max-w-6xl">
    
    @if($details && $details->count() > 0)
        <div class="overflow-x-auto w-full">
            <table class="table table-zebra table-sm w-full">
                <thead>
                    <tr>
                        <th>Date</th>
                        @if($selectedBy !== 'Machine')
                            <th>Machine</th>
                        @endif
                        <th>Group</th>
                        <th>Time / Stop Line</th>
                        <th>Problem</th>
                        <th>Cause</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $detail)
                        <tr>
                            <td class="whitespace-nowrap">{{ $detail->Date ? $detail->Date->format('d M y') : '-' }}</td>
                            @if($selectedBy !== 'Machine')
                                <td>{{ $detail->MachineName }}</td>
                            @endif
                            <td>{{ $detail->groupline ? substr($detail->groupline, -1, 1) : '-' }}</td>
                            <td>
                                <span class="px-2 py-1 rounded {{ $detail->DownTime > 60 ? 'bg-error text-error-content font-bold' : '' }}">
                                    {{ $detail->worktime ?? 0 }} / {{ $detail->DownTime ?? 0 }}
                                </span>
                            </td>
                            <td>{{ $detail->Problem }}</td>
                            <td>{{ $detail->Cause }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $details->links() }}
        </div>
    @else
        <div class="text-center py-10">
            <x-icon name="o-inbox" class="w-12 h-12 mx-auto mb-3 opacity-30" />
            <p>No details found.</p>
        </div>
    @endif
    
    <x-slot:actions>
        <x-button label="Close" wire:click="$set('detailModal', false)" />
    </x-slot:actions>
</x-modal>
