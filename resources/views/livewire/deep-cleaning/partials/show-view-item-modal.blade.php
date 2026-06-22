<x-modal wire:model="viewItemModal" title="Finding Details" separator box-class="max-w-3xl">
    @if($viewingItemId)
        @php
            $viewItem = $record->items->firstWhere('id', $viewingItemId);
        @endphp
        @if($viewItem)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- BEFORE -->
                <div class="space-y-3">
                    <div class="font-bold text-center text-md bg-error/10 text-error py-1 rounded-t-xl">BEFORE</div>
                    <div class="border rounded-b-xl aspect-[4/3] flex items-center justify-center bg-base-100 overflow-hidden shadow-sm">
                        @if($viewItem->before_photo)
                            <img src="{{ asset('storage/' . $viewItem->before_photo) }}" class="object-cover w-full h-full" alt="Before" />
                        @else
                            <x-icon name="o-photo" class="w-16 h-16 text-base-content/20" />
                        @endif
                    </div>
                </div>

                <!-- AFTER -->
                <div class="space-y-3">
                    <div class="font-bold text-center text-md bg-success/10 text-success py-1 rounded-t-xl">AFTER</div>
                    <div class="border rounded-b-xl aspect-[4/3] flex items-center justify-center bg-base-100 overflow-hidden shadow-sm">
                        @if($viewItem->after_photo)
                            <img src="{{ asset('storage/' . $viewItem->after_photo) }}" class="object-cover w-full h-full" alt="After" />
                        @else
                            <x-icon name="o-photo" class="w-16 h-16 text-base-content/20" />
                        @endif
                    </div>
                </div>
            </div>

        @endif
    @endif

    <x-slot:actions>
        <x-button label="Close" class="btn-ghost" wire:click="$set('viewItemModal', false)" />
    </x-slot:actions>
</x-modal>
