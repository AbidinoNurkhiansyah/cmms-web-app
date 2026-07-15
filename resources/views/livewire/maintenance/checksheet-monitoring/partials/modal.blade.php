<x-modal wire:model="noteModal" title="Follow Up Problem" class="backdrop-blur">
    <div class="bg-base-200 p-3 rounded-lg mb-4 text-sm">
        <div class="grid grid-cols-2 gap-2">
            <div><span class="opacity-60 text-xs block">Machine</span> <span class="font-bold">{{ $modalMachine }}</span></div>
            <div><span class="opacity-60 text-xs block">Date</span> <span class="font-bold">{{ $modalDate }}</span></div>
            <div><span class="opacity-60 text-xs block">Inspector</span> <span class="font-bold">{{ $modalInspector }}</span></div>
        </div>
    </div>
    
    <div class="mb-4">
        <label class="text-xs font-bold opacity-70 mb-1 block">Laporan Keterangan / NG:</label>
        <div class="p-3 bg-error/10 text-error-content rounded-lg text-sm whitespace-pre-wrap border border-error/20 max-h-40 overflow-y-auto font-mono">
            {{ $modalNotes }}
        </div>
    </div>

    <x-textarea 
        label="Follow Up Action (Leader)" 
        wire:model="leaderFollowUp" 
        placeholder="Ketik tindakan penyelesaian di sini..." 
        rows="3" />

    <x-slot:actions>
        <x-button label="Batal" @click="$wire.noteModal = false" />
        <x-button label="Simpan Follow Up" icon="o-check" class="btn-primary" wire:click="saveFollowUp" spinner />
    </x-slot:actions>
</x-modal>
