<?php

use App\Models\Overhaul;
use Livewire\Volt\Component;

new class extends Component {
    public Overhaul $record;
    public string $selectedTab = 'general-tab';
    
    // Additional PIC names
    public string $pic1Name = '';
    public string $pic2Name = '';
    public string $pic3Name = '';

    public function mount(int $id)
    {
        $this->record = Overhaul::with(['steps', 'spareparts'])->findOrFail($id);
        
        $this->pic1Name = $this->getPicName($this->record->pic1);
        $this->pic2Name = $this->getPicName($this->record->pic2);
        $this->pic3Name = $this->getPicName($this->record->pic3);
    }
    
    private function getPicName(?string $jid)
    {
        if (!$jid) return '-';
        $user = \App\Models\User::where('jid_no', $jid)->first();
        return $user ? $user->repair : '-';
    }
};
?>

<div>
    <x-header separator class="!mb-4">
        <x-slot:title>
            <div class="flex items-center gap-3">
                <x-button icon="o-arrow-left" class="btn-circle btn-ghost btn-sm" link="{{ route('overhaul.report.index') }}" wire:navigate />
                <span>Overhaul Details [{{ $record->id }}]</span>
            </div>
        </x-slot:title>
    </x-header>

    <div class="grid grid-cols-1 gap-4">
        <x-card>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Asset Info -->
                <div class="space-y-4">
                    <h3 class="font-bold text-lg border-b border-base-300 pb-2 mb-4">Asset Details</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-base-content/70 font-medium">Date</div>
                            <div class="mt-1 font-semibold text-base">{{ $record->date?->format('Y-m-d') }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/70 font-medium">Line</div>
                            <div class="mt-1 font-semibold text-base">{{ $record->LineName }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/70 font-medium">Machine</div>
                            <div class="mt-1 font-semibold text-base">{{ $record->MachineName }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/70 font-medium">Asset No</div>
                            <div class="mt-1 font-semibold text-base">{{ $record->MachineNo ?: '-' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Timing Info -->
                <div class="space-y-4">
                    <h3 class="font-bold text-lg border-b border-base-300 pb-2 mb-4">Timing Details</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-base-content/70 font-medium">Start Time</div>
                            <div class="mt-1 font-semibold text-base">{{ $record->start_time?->format('H:i') ?: '-' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/70 font-medium">Finish Time</div>
                            <div class="mt-1 font-semibold text-base">{{ $record->end_time?->format('H:i') ?: '-' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/70 font-medium">Repair Time</div>
                            <div class="mt-1 font-semibold text-base">{{ (int)$record->repair_time }} mins</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/70 font-medium">Work Time</div>
                            <div class="mt-1 font-semibold text-base">{{ $record->work_time ?: '-' }} jam</div>
                        </div>
                    </div>
                </div>

                <!-- Personnel -->
                <div class="space-y-4">
                    <h3 class="font-bold text-lg border-b border-base-300 pb-2 mb-4">Personnel</h3>
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-primary/10 p-2 rounded-lg">
                                <x-icon name="o-user" class="w-5 h-5 text-primary" />
                            </div>
                            <div>
                                <div class="text-xs text-base-content/70 font-medium uppercase tracking-wider">PIC 1</div>
                                <div class="font-semibold text-base">{{ $pic1Name }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="bg-primary/10 p-2 rounded-lg">
                                <x-icon name="o-user" class="w-5 h-5 text-primary" />
                            </div>
                            <div>
                                <div class="text-xs text-base-content/70 font-medium uppercase tracking-wider">PIC 2</div>
                                <div class="font-semibold text-base">{{ $pic2Name }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="bg-primary/10 p-2 rounded-lg">
                                <x-icon name="o-user" class="w-5 h-5 text-primary" />
                            </div>
                            <div>
                                <div class="text-xs text-base-content/70 font-medium uppercase tracking-wider">PIC 3</div>
                                <div class="font-semibold text-base">{{ $pic3Name }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Problem & Explanation -->
            <div class="mt-8 pt-6 border-t border-base-300 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-base-200/50 p-5 rounded-xl border border-base-200">
                    <div class="flex items-center gap-2 mb-3">
                        <x-icon name="o-exclamation-triangle" class="w-5 h-5 text-warning" />
                        <h4 class="font-bold text-base-content">Problem</h4>
                    </div>
                    <p class="text-sm whitespace-pre-line text-base-content/80">{{ $record->problem ?: 'No problem described.' }}</p>
                </div>
                <div class="bg-base-200/50 p-5 rounded-xl border border-base-200">
                    <div class="flex items-center gap-2 mb-3">
                        <x-icon name="o-information-circle" class="w-5 h-5 text-info" />
                        <h4 class="font-bold text-base-content">Explanation</h4>
                    </div>
                    <p class="text-sm whitespace-pre-line text-base-content/80">{{ $record->explanation ?: 'No explanation provided.' }}</p>
                </div>
            </div>
        </x-card>

        <div x-data="{ tab: 'general' }">
            <div class="w-full flex overflow-x-auto border-b border-base-content/10 mb-4">
                <button @click.prevent="tab = 'general'" :class="tab === 'general' ? 'bg-neutral text-neutral-content' : 'hover:bg-base-300 text-base-content/70'" class="flex-1 text-center font-bold py-3 transition-all cursor-pointer flex justify-center items-center gap-2">
                    <x-icon name="o-photo" class="w-5 h-5" />
                    <span class="hidden sm:block">Photos & Improvement</span>
                </button>
                <button @click.prevent="tab = 'steps'" :class="tab === 'steps' ? 'bg-neutral text-neutral-content' : 'hover:bg-base-300 text-base-content/70'" class="flex-1 text-center font-bold py-3 transition-all cursor-pointer flex justify-center items-center gap-2">
                    <x-icon name="o-list-bullet" class="w-5 h-5" />
                    <span class="hidden sm:block">Repair Steps</span>
                </button>
                <button @click.prevent="tab = 'spareparts'" :class="tab === 'spareparts' ? 'bg-neutral text-neutral-content' : 'hover:bg-base-300 text-base-content/70'" class="flex-1 text-center font-bold py-3 transition-all cursor-pointer flex justify-center items-center gap-2">
                    <x-icon name="o-wrench" class="w-5 h-5" />
                    <span class="hidden sm:block">Spareparts Used</span>
                </button>
            </div>

            <!-- Tab 1: Photos & Improvement -->
            <div x-show="tab === 'general'" x-cloak>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <x-card title="Photo Documentation">
                        <div class="grid grid-cols-2 gap-4">
                            @if($record->photo_before_1)
                                <div>
                                    <p class="font-semibold text-sm mb-1 text-center">Before 1</p>
                                    <a href="{{ asset('storage/' . $record->photo_before_1) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $record->photo_before_1) }}" class="rounded-lg w-full object-cover max-h-48 border" />
                                    </a>
                                </div>
                            @endif
                            @if($record->photo_after_1)
                                <div>
                                    <p class="font-semibold text-sm mb-1 text-center">After 1</p>
                                    <a href="{{ asset('storage/' . $record->photo_after_1) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $record->photo_after_1) }}" class="rounded-lg w-full object-cover max-h-48 border" />
                                    </a>
                                </div>
                            @endif
                            @if($record->photo_before_2)
                                <div>
                                    <p class="font-semibold text-sm mb-1 text-center">Before 2</p>
                                    <a href="{{ asset('storage/' . $record->photo_before_2) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $record->photo_before_2) }}" class="rounded-lg w-full object-cover max-h-48 border" />
                                    </a>
                                </div>
                            @endif
                            @if($record->photo_after_2)
                                <div>
                                    <p class="font-semibold text-sm mb-1 text-center">After 2</p>
                                    <a href="{{ asset('storage/' . $record->photo_after_2) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $record->photo_after_2) }}" class="rounded-lg w-full object-cover max-h-48 border" />
                                    </a>
                                </div>
                            @endif
                            @if(!$record->photo_before_1 && !$record->photo_after_1 && !$record->photo_before_2 && !$record->photo_after_2)
                                <div class="col-span-2 text-center text-gray-500 py-4">No photos available.</div>
                            @endif
                        </div>
                    </x-card>

                    <div class="grid grid-cols-1 gap-4">
                        <x-card title="Next Improvement">
                            <p class="whitespace-pre-line">{{ $record->next_improvement ?: 'Not recorded yet.' }}</p>
                        </x-card>
                        <x-card title="Yokotenkai">
                            <p class="whitespace-pre-line">{{ $record->yokotenkai ?: 'Not recorded yet.' }}</p>
                        </x-card>
                    </div>
                </div>
            </div>
            
            <!-- Tab 2: Repair Steps -->
            <div x-show="tab === 'steps'" x-cloak>
                <x-card>
                    @if($record->steps->isNotEmpty())
                        <div class="overflow-x-auto w-full">
                            <table class="table table-zebra w-full">
                                <thead>
                                    <tr>
                                        <th class="w-12">No</th>
                                        <th>Step Repair</th>
                                        <th class="w-24">Minutes</th>
                                        <th>Obstacle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($record->steps as $i => $step)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $step->step_repair }}</td>
                                            <td>{{ $step->minutes }}</td>
                                            <td>{{ $step->obstacle }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-gray-500 py-4">No repair steps recorded.</div>
                    @endif
                </x-card>
            </div>

            <!-- Tab 3: Spareparts -->
            <div x-show="tab === 'spareparts'" x-cloak>
                <x-card>
                    @if($record->spareparts->isNotEmpty())
                        <div class="overflow-x-auto w-full">
                            <table class="table table-zebra w-full">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th class="w-24">Qty</th>
                                        <th>Maker</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($record->spareparts as $sp)
                                        <tr>
                                            <td>{{ $sp->type }}</td>
                                            <td>{{ $sp->qty }}</td>
                                            <td>{{ $sp->maker }}</td>
                                            <td>{{ $sp->remarks }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-gray-500 py-4">No spare parts used.</div>
                    @endif
                </x-card>
            </div>
        </div>
    </div>
</div>
