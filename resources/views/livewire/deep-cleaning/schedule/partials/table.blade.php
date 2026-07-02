    <div x-show="localViewMode === 'table'">
        <x-card>
            <div class="overflow-x-auto w-full">
            <x-table :headers="[
        ['key' => 'LineName', 'label' => 'Line'],
        ['key' => 'NameMachine', 'label' => 'Machine'],
        ['key' => 'machine_no', 'label' => 'Asset No'],
        ['key' => 'planDate', 'label' => 'Plan Date'],
        ['key' => 'status_badge', 'label' => 'Status'],
        ['key' => 'progress', 'label' => 'Checklist'],
        ['key' => 'actions', 'label' => 'Actions', 'class' => 'w-24 text-center'],
    ]" :rows="$schedules" with-pagination
                class="text-sm" no-hover>

                @scope('cell_planDate', $row)
                {{ $row->planDate ? $row->planDate->format('Y-m-d') : '-' }}
                @endscope

                @scope('cell_status_badge', $row)
                @if($row->is_approved)
                    <x-badge value="Approved" class="badge-success text-white" />
                @elseif($row->postponed)
                    <x-badge value="Postponed" class="badge-error text-white" />
                @elseif($row->act_date)
                    <x-badge value="In Progress" class="badge-warning" />
                @else
                    <x-badge value="Planning" class="badge-neutral" />
                @endif

                @if($row->act_date)
                    <div class="text-xs text-base-content/60 mt-1">{{ $row->act_date->format('Y-m-d') }}</div>
                @endif
                @endscope

                @scope('cell_progress', $row)
                @php $filledCount = is_array($row->items) ? count($row->items) : 0; @endphp
                @if($filledCount > 0)
                    <x-badge value="{{ $filledCount }} Items Filled" class="badge-ghost" />
                @else
                    <span class="text-base-content/40">-</span>
                @endif
                @endscope

                @scope('cell_actions', $row)
                <div class="flex gap-2 justify-center items-center">
                    <x-button label="{{ $row->is_approved ? 'View' : 'Execute' }}"
                        class="{{ $row->is_approved ? 'btn-ghost' : 'btn-primary' }} btn-sm"
                        wire:click="openItemCheckModal({{ $row->id }})"
                        icon="{{ $row->is_approved ? 'o-eye' : 'o-play' }}" />

                    <!-- Alpine.js Teleported Dropdown -->
                    <div x-data="{ open: false, top: 0, left: 0 }" @scroll.window="open = false"
                        @close-dropdowns.window="if ($event.detail.id !== {{ $row->id }}) open = false" @click.stop>
                        <button x-ref="triggerBtn"
                            @click="open = !open; if(open) { window.dispatchEvent(new CustomEvent('close-dropdowns', { detail: { id: {{ $row->id }} } })); let r = $refs.triggerBtn.getBoundingClientRect(); top = r.top - 4; left = r.left - 200; }"
                            class="btn btn-ghost btn-sm">
                            <x-icon name="o-ellipsis-vertical" class="w-5 h-5" />
                        </button>

                        <template x-teleport="body">
                            <div x-show="open" @click.away="open = false" x-transition.opacity.duration.200ms
                                class="fixed z-[9999]" :style="`top: ${top}px; left: ${left}px; width: 192px;`">
                                <ul class="menu bg-base-100 shadow-xl rounded-box border border-base-200">
                                    @if(!$row->is_approved)
                                        <li><a @click="open = false; $wire.togglePostpone({{ $row->id }})"><x-icon
                                                    name="{{ $row->postponed ? 'o-play-circle' : 'o-pause-circle' }}"
                                                    class="w-4 h-4" />
                                                {{ $row->postponed ? 'Activate' : 'Postpone' }}</a>
                                        </li>
                                        <li><a @click="open = false; $wire.openEditModal({{ $row->id }})"><x-icon
                                                    name="o-pencil" class="w-4 h-4" /> Edit</a></li>
                                    @else
                                        <li><a @click="open = false; $wire.toggleReport({{ $row->id }})"><x-icon
                                                    name="o-x-circle" class="w-4 h-4" /> Cancel</a></li>
                                    @endif
                                    <li><a @click="open = false; $wire.confirmDelete({{ $row->id }})"
                                            class="text-error"><x-icon name="o-trash" class="w-4 h-4" /> Delete</a></li>
                                </ul>
                            </div>
                        </template>
                    </div>
                </div>
                @endscope
            </x-table>
        </div>
    </x-card>
    </div>
