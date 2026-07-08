<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @forelse($spareParts as $part)
        <x-card class="shadow-sm flex flex-col h-full rounded-2xl" body-class="flex flex-col flex-1">
            <x-slot:figure class="relative">
                @if($part->part_photo)
                    <img src="{{ asset('storage/' . $part->part_photo) }}" alt="{{ $part->part_name }}"
                        class="h-40 w-full object-cover" />
                @else
                    <div class="h-40 w-full bg-base-200 flex items-center justify-center text-base-content/50">
                        <x-icon name="o-photo" class="w-10 h-10" />
                    </div>
                @endif

                {{-- Dropdown Action Menu --}}
                <div class="absolute top-2 right-2">
                    <x-dropdown right>
                        <x-slot:trigger>
                            <x-button icon="o-ellipsis-vertical" class="btn-circle btn-sm bg-base-100/80 border-none backdrop-blur-md" />
                        </x-slot:trigger>
                        <x-menu-item title="Print Label" icon="o-printer" wire:click="openPrintModal({{ $part->id }})" />
                        <x-menu-item title="Detail Machine" icon="o-cpu-chip" wire:click="openMachineModal({{ $part->id }})" />
                    </x-dropdown>
                </div>
            </x-slot:figure>

            <div class="font-bold line-clamp-2 min-h-[2rem]">
                {{ $part->part_name }}
            </div>

            <div class="text-sm space-y-2 mt-auto">
                <div class="flex justify-between border-b border-base-200 pb-1 items-center">
                    <span class="font-semibold text-error">Rack:</span>
                    <span class="text-right truncate ml-2 font-bold">{{ $part->no_rack ?: '-' }}</span>
                </div>
                <div class="flex justify-between border-b border-base-200 pb-1">
                    <span class="font-semibold">Maker:</span>
                    <span class="text-right truncate ml-2" title="{{ $part->maker }}">{{ $part->maker ?: '-' }}</span>
                </div>
                <div class="flex justify-between border-b border-base-200 pb-1">
                    <span class="font-semibold">Machine:</span>
                    <span class="text-right truncate ml-2" title="{{ $part->machine }}">{{ $part->machine ?: '-' }}</span>
                </div>
                <div class="flex justify-between border-b border-base-200 pb-1">
                    <span class="font-semibold text-success">Stock:</span>
                    <span>{{ $part->last_stock ?? 0 }}</span>
                </div>

                @if($part->status === 'N')
                    <div class="text-center text-error italic font-bold mt-2">
                        Discontinued
                    </div>
                @endif

                @if($part->repair_stock > 0)
                    <div class="mt-3 flex justify-between items-center bg-warning/20 p-2 rounded">
                        <span class="text-warning-content font-semibold">Repair: {{ $part->repair_stock }}</span>
                        <x-button size="btn-xs" class="btn-primary"
                            wire:click="openEditRepair({{ $part->id }}, {{ $part->repair_stock }}, '{{ addslashes($part->repair_rack) }}')">
                            {{ $part->repair_rack ?: 'Edit' }}
                        </x-button>
                    </div>
                @endif
            </div>
        </x-card>
    @empty
        <div class="col-span-full">
            <x-alert icon="o-exclamation-triangle" class="alert-warning">
                No spare parts found.
            </x-alert>
        </div>
    @endforelse
</div>

<div class="mt-6 [&_*]:cursor-pointer">
    {{ $spareParts->links() }}
</div>