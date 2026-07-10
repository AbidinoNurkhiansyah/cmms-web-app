    {{-- Spareparts Modal --}}
    <x-modal wire:model="showSparepartModal" title="Spare Part Information" class="backdrop-blur">
        @if($spareparts)
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead><tr><th>Part Name</th><th>Part Type</th><th>Qty</th><th>Rank</th></tr></thead>
                    <tbody>
                        @foreach($spareparts as $sp)
                            <tr>
                                <td>{{ $sp->part_name }}</td>
                                <td>{{ $sp->part_type }}</td>
                                <td>{{ $sp->qty }}</td>
                                <td>{{ $sp->Rangking }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $spareparts->links() }}</div>
        @else
            <div class="flex justify-center"><span class="loading loading-spinner"></span></div>
        @endif
        <x-slot:actions><x-button label="Close" wire:click="$set('showSparepartModal', false)" /></x-slot:actions>
    </x-modal>

    {{-- TPM Modal --}}
    <x-modal wire:model="showTpmModal" title="TPM Records" class="backdrop-blur" box-class="max-w-4xl">
        @if($tpmRecords)
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead><tr><th>Type</th><th>Date</th><th>PIC</th><th>Remark</th></tr></thead>
                    <tbody>
                        @foreach($tpmRecords as $tpm)
                            <tr>
                                <td>{{ $tpm->type }}</td>
                                <td>{{ \Carbon\Carbon::parse($tpm->checked_date)->format('d M y') }}</td>
                                <td>{{ $tpm->pic ?? '-' }}</td>
                                <td>{{ $tpm->remark ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $tpmRecords->links() }}</div>
        @else
            <div class="flex justify-center"><span class="loading loading-spinner"></span></div>
        @endif
        <x-slot:actions><x-button label="Close" wire:click="$set('showTpmModal', false)" /></x-slot:actions>
    </x-modal>

    {{-- Problem Log Modal --}}
    <x-modal wire:model="showProblemModal" title="Problem Log (Carty)" class="backdrop-blur" box-class="max-w-6xl">
        @if($problemRecords)
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead><tr><th>Date</th><th>Problem</th><th>Cause</th><th>Action</th><th>Downtime</th></tr></thead>
                    <tbody>
                        @foreach($problemRecords as $prob)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($prob->Date)->format('d M y') }}</td>
                                <td>{{ $prob->Problem }}</td>
                                <td>{{ $prob->Cause }}</td>
                                <td>{{ $prob->Action }}</td>
                                <td>{{ $prob->DownTime }} min</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $problemRecords->links() }}</div>
        @else
            <div class="flex justify-center"><span class="loading loading-spinner"></span></div>
        @endif
        <x-slot:actions><x-button label="Close" wire:click="$set('showProblemModal', false)" /></x-slot:actions>
    </x-modal>

    {{-- Overhaul Modal --}}
    <x-modal wire:model="showOverhaulModal" title="Overhaul" class="backdrop-blur" box-class="max-w-4xl">
        @if($overhaulRecords)
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead><tr><th>Date</th><th>Problem</th><th>Start Time</th><th>End Time</th></tr></thead>
                    <tbody>
                        @foreach($overhaulRecords as $oh)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($oh->date)->format('d M Y') }}</td>
                                <td>{{ $oh->problem }}</td>
                                <td>{{ \Carbon\Carbon::parse($oh->start_time)->format('H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($oh->end_time)->format('H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $overhaulRecords->links() }}</div>
        @else
            <div class="flex justify-center"><span class="loading loading-spinner"></span></div>
        @endif
        <x-slot:actions><x-button label="Close" wire:click="$set('showOverhaulModal', false)" /></x-slot:actions>
    </x-modal>

    {{-- Work Order Modal --}}
    <x-modal wire:model="showWorkOrderModal" title="Work Orders" class="backdrop-blur" box-class="max-w-5xl">
        @if($workOrders)
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead><tr><th>Date</th><th>Status</th><th>PIC</th><th>Description</th></tr></thead>
                    <tbody>
                        @foreach($workOrders as $wo)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($wo->date)->format('d M y') }}</td>
                                <td>{{ $wo->status ?? 'N/A' }}</td>
                                <td>{{ $wo->pic ?? 'N/A' }}</td>
                                <td>{{ $wo->problem_description ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $workOrders->links() }}</div>
        @else
            <div class="flex justify-center"><span class="loading loading-spinner"></span></div>
        @endif
        <x-slot:actions><x-button label="Close" wire:click="$set('showWorkOrderModal', false)" /></x-slot:actions>
    </x-modal>

    {{-- One Hour Over Modal --}}
    <x-modal wire:model="showOneHourModal" title="One Hour Over" class="backdrop-blur" box-class="max-w-4xl">
        @if($oneHourOver)
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead><tr><th>Date</th><th>Problem</th><th>Files</th></tr></thead>
                    <tbody>
                        @foreach($oneHourOver as $oho)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($oho->date)->format('d M y') }}</td>
                                <td>{{ $oho->problem }}</td>
                                <td>
                                    @if($oho->file_rsa)
                                        <a href="{{ asset('mtc_img/one_hour/' . $oho->file_rsa) }}" target="_blank" class="badge badge-error">RSA</a>
                                    @endif
                                    @if($oho->file_rca)
                                        <a href="{{ asset('mtc_img/one_hour/' . $oho->file_rca) }}" target="_blank" class="badge badge-primary">RCA</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $oneHourOver->links() }}</div>
        @else
            <div class="flex justify-center"><span class="loading loading-spinner"></span></div>
        @endif
        <x-slot:actions><x-button label="Close" wire:click="$set('showOneHourModal', false)" /></x-slot:actions>
    </x-modal>
