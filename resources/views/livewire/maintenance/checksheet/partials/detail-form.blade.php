<x-card class="mb-6">
    <h3 class="text-xl font-bold mb-4">Checksheet Items</h3>
    
    @if($items->isEmpty())
        <x-alert icon="o-information-circle" class="alert-info">
            Belum ada poin checksheet yang dikonfigurasi untuk mesin ini.
        </x-alert>
    @else
        <form wire:submit="save">
                <div class="bg-base-100 rounded-lg border border-base-200 divide-y divide-base-200">
                    @foreach($items as $index => $item)
                        @php
                            $hasNumber = preg_match('/\d/', $item->standard);
                        @endphp
                        <div class="p-4 hover:bg-base-200/50 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4">
                            
                            {{-- Info Section --}}
                            <div class="flex-1">
                                <div class="flex items-start md:items-center gap-2 mb-1">
                                    <span class="font-bold text-primary">{{ $index + 1 }}.</span>
                                    <span class="font-semibold">{{ $item->item_check }}</span>
                                    <x-badge value="{{ $item->periode }}" class="badge-sm badge-neutral" />
                                    @if($item->photo_path)
                                        <x-button icon="o-photo" class="btn-circle btn-xs btn-info" tooltip="View Photo" wire:click.prevent="viewPhoto('{{ addslashes($item->item_check) }}', '{{ $item->photo_path }}')" />
                                    @endif
                                </div>
                                <div class="text-xs opacity-75 flex flex-col md:flex-row md:gap-6 mt-2 md:mt-1">
                                    <div><span class="font-semibold">Std:</span> <span class="text-success">{{ $item->standard }}</span></div>
                                    <div class="mt-1 md:mt-0"><span class="font-semibold">Method:</span> <span class="text-warning">{{ $item->method }}</span></div>
                                </div>
                            </div>

                            {{-- Input Section --}}
                            <div class="w-full md:w-56 shrink-0 mt-2 md:mt-0">
                                @if($hasNumber)
                                    <x-input type="number" step="0.01" wire:model="results.{{ $item->id }}" placeholder="Nilai aktual..." class="input-sm w-full" required />
                                @else
                                    <div class="flex items-center gap-6 bg-base-200 px-4 py-2 rounded-lg justify-center border border-base-300">
                                        <label class="flex items-center gap-2 cursor-pointer hover:opacity-80">
                                            <input type="radio" wire:model="results.{{ $item->id }}" value="OK" class="radio radio-sm radio-primary" required />
                                            <span class="text-sm font-bold text-success">OK</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer hover:opacity-80">
                                            <input type="radio" wire:model="results.{{ $item->id }}" value="NG" class="radio radio-sm radio-error" required />
                                            <span class="text-sm font-bold text-error">NG</span>
                                        </label>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

            <div class="mt-6 border-t pt-4">
                <x-checkbox label="Production Checked" wire:model="apvProd" />
                @if($isFriday)
                    <div class="mt-2">
                        <x-checkbox label="STL Prod Approval (Friday Only)" wire:model="apvWeek" />
                    </div>
                @endif
            </div>

            <div class="mt-6 flex justify-end">
                <x-button type="submit" label="{{ $hasExistingData ? 'Update Checksheet' : 'Save Checksheet' }}" icon="o-check" class="btn-primary" spinner="save" />
            </div>
        </form>
    @endif
</x-card>
