{{-- Data Table --}}
<x-card class="bg-base-100 shadow-sm">
    @if($skills->isEmpty())
        <div class="py-10 text-center text-base-content/50">
            <x-icon name="o-academic-cap" class="w-12 h-12 mx-auto mb-3 opacity-50" />
            <p>No skills found for this associate.</p>
            <x-button label="Add First Skill" icon="o-plus" class="btn-sm btn-outline mt-4" wire:click="openAdd" />
        </div>
    @else
        <x-table :headers="$headers" :rows="$skills" striped>
            
            @scope('cell_category', $skill)
                @php
                    $color = match($skill->category) {
                        'OFFICE' => 'badge-primary',
                        'GENBA' => 'badge-secondary',
                        'ELECTRICAL' => 'badge-success',
                        'MECHANICAL' => 'badge-warning',
                        'ADV ELECTRICAL' => 'badge-error text-white',
                        default => 'badge-ghost',
                    };
                @endphp
                <x-badge :value="$skill->category" class="{{ $color }}" />
            @endscope

            @scope('cell_actual_level', $skill)
                <div class="flex items-center gap-1">
                    @for($i = 1; $i <= 4; $i++)
                        <div class="w-3 h-3 rounded-full {{ $i <= $skill->actual_level ? 'bg-success' : 'bg-base-300' }}"></div>
                    @endfor
                </div>
            @endscope

            @scope('cell_target_level', $skill)
                <div class="flex items-center gap-1">
                    @for($i = 1; $i <= 4; $i++)
                        <div class="w-3 h-3 rounded-full {{ $i <= $skill->target_level ? 'bg-primary' : 'bg-base-300' }}"></div>
                    @endfor
                </div>
            @endscope

            @scope('cell_actions', $skill)
                <div class="flex gap-2 justify-center">
                    <x-button icon="o-pencil-square" wire:click="openEdit({{ $skill->id }})" spinner
                        class="btn-sm btn-circle btn-ghost text-primary" tooltip="Edit" />
                    <x-button icon="o-trash" wire:click="confirmDelete({{ $skill->id }})" spinner
                        class="btn-sm btn-circle btn-ghost text-error" tooltip="Delete" />
                </div>
            @endscope

        </x-table>
    @endif
</x-card>
