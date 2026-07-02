    <div x-show="localViewMode === 'calendar'" x-cloak>
        <x-card>
            <div class="grid grid-cols-7 gap-1">
                <!-- Days Header -->
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                    <div class="text-center font-bold text-[11px] uppercase text-base-content/70 py-1">{{ $dayName }}</div>
                @endforeach

                <!-- Empty slots for first week -->
                @for($i = 0; $i < $startDayOfWeek; $i++)
                    <div class="p-1 border border-transparent"></div>
                @endfor

                <!-- Days -->
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dateStr = \Carbon\Carbon::parse($selectedMonthYear . '-' . $day)->format('Y-m-d');
                        $daySchedules = $calendarSchedules->get($dateStr, collect());
                        $isToday = $dateStr === now()->format('Y-m-d');
                    @endphp
                    <div class="min-h-[70px] border {{ $isToday ? 'border-primary bg-primary/5' : 'border-base-200 bg-base-100' }} rounded-md p-1 flex flex-col gap-0.5 transition-all">
                        <div class="text-right text-[11px] font-semibold {{ $isToday ? 'text-primary' : 'text-base-content/50' }}">{{ $day }}</div>
                        @if($daySchedules->count() > 0)
                            @php $first = $daySchedules->first(); @endphp
                            <div class="text-[10px] p-1 rounded bg-base-200 border border-base-300 hover:border-primary/50 cursor-pointer transition-colors flex flex-col leading-tight shadow-sm"
                                 onclick="document.getElementById('modal_cal_{{ $dateStr }}').showModal()">
                                <span class="font-bold truncate text-base-content">{{ $first->NameMachine }}</span>
                                <span class="truncate text-base-content/70">{{ $first->LineName }}</span>
                            </div>
                            @if($daySchedules->count() > 1)
                                <div class="text-[10px] text-center text-primary font-semibold cursor-pointer hover:underline mt-0.5"
                                     onclick="document.getElementById('modal_cal_{{ $dateStr }}').showModal()">
                                    + {{ $daySchedules->count() - 1 }} more
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Modal Detail Harian (Rendered di dalam loop agar instan) --}}
                    @if($daySchedules->count() > 0)
                    <dialog id="modal_cal_{{ $dateStr }}" class="modal modal-bottom sm:modal-middle backdrop-blur-sm">
                        <div class="modal-box">
                            <h3 class="font-bold text-lg mb-4">Jadwal Tanggal {{ \Carbon\Carbon::parse($dateStr)->format('d M Y') }}</h3>
                            <div class="flex flex-col gap-2 max-h-[60vh] overflow-y-auto pr-2">
                                @foreach($daySchedules as $sch)
                                    <div class="flex items-center justify-between p-3 border border-base-200 rounded-lg bg-base-100 shadow-sm">
                                        <div class="flex flex-col overflow-hidden mr-2">
                                            <span class="font-bold text-sm truncate">{{ $sch->NameMachine }}</span>
                                            <span class="text-xs text-base-content/70 truncate">{{ $sch->LineName }} &bull; {{ $sch->machine_no }}</span>
                                        </div>
                                        <div class="flex items-center gap-3 shrink-0">
                                            @if($sch->is_approved)
                                                <x-badge value="Approved" class="badge-success text-white badge-sm" />
                                            @elseif($sch->postponed)
                                                <x-badge value="Postponed" class="badge-error text-white badge-sm" />
                                            @elseif($sch->act_date)
                                                <x-badge value="In Progress" class="badge-warning badge-sm" />
                                            @else
                                                <x-badge value="Planning" class="badge-neutral badge-sm" />
                                            @endif
                                            
                                            @if(!$sch->is_approved)
                                                <x-button icon="o-play" class="btn-primary btn-sm" wire:click="openItemCheckModal({{ $sch->id }})" />
                                            @else
                                                <x-button icon="o-eye" class="btn-ghost btn-sm" wire:click="openItemCheckModal({{ $sch->id }})" />
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <form method="dialog" class="modal-backdrop">
                            <button>close</button>
                        </form>
                    </dialog>
                    @endif
                @endfor
            </div>
        </x-card>
    </div>
