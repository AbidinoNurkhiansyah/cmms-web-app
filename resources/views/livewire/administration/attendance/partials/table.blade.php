@php
    $teams = collect($this->attendanceData['grouped']->keys())->map(fn($t) => ['id' => $t, 'name' => $t])->prepend(['id' => 'all', 'name' => 'Semua Section'])->toArray();
@endphp

<x-card class="shadow-sm">
    <!-- Filter & Action Bar -->
    <div
        class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-4 bg-base-200/50 p-4 rounded-xl border border-base-200">
        <div class="flex flex-col sm:flex-row items-end gap-4 w-full md:w-auto">
            <!-- Date Navigation -->
            <div class="flex items-end gap-2">
                <x-button icon="o-chevron-left" wire:click="previousDay"
                    class="btn-circle btn-sm btn-outline bg-base-100" />
                <div class="text-center px-2">
                    <div class="text-xs font-semibold text-base-content/70 mb-1">Tanggal</div>
                    <div class="font-bold whitespace-nowrap">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</div>
                </div>
                <x-button icon="o-chevron-right" wire:click="nextDay"
                    class="btn-circle btn-sm btn-outline bg-base-100" />
                <x-button label="Today" wire:click="setDate('{{ now()->format('Y-m-d') }}')"
                    class="btn-sm btn-primary ml-1" />
            </div>

            <div class="divider divider-horizontal hidden sm:flex mx-0"></div>

            <!-- Team Selection -->
            <div class="w-full sm:w-48">
                <x-select label="Pilih Section" :options="$teams" wire:model.live="selectedTeam"
                    icon="o-building-office" class="!bg-base-100 select-bordered" />
            </div>

            <!-- Search -->
            <div class="w-full sm:w-56">
                <x-input label="Cari Karyawan" wire:model.live.debounce.300ms="search" icon="o-magnifying-glass"
                    placeholder="Nama..." class="!bg-base-100 input-bordered" clearable />
            </div>
        </div>

        <!-- Bulk Action -->
        <div class="w-full md:w-auto">
            @if($this->selectedTeam)
                <x-button
                    label="{{ $this->selectedTeam === 'all' ? 'Hadirkan Semua' : 'Hadirkan Semua (' . $this->selectedTeam . ')' }}"
                    wire:click="markAllPresent('{{ $this->selectedTeam }}')" icon="o-check-circle"
                    class="btn-success btn-sm text-white w-full md:w-auto" tooltip="Tandai 'P' untuk status kosong"
                    onclick="return confirm('Yakin ingin menandai hadir (P) untuk semua status yang kosong?');" />
            @endif
        </div>
    </div>

    <!-- Data Grid -->
    <div class="overflow-x-auto border border-base-200 rounded-lg">
        <table class="table table-sm w-full table-zebra table-pin-rows table-pin-cols">
            <thead>
                <tr class="bg-base-200 text-base-content">
                    <th>No</th>
                    <th>Nama Karyawan</th>
                    <th class="text-center w-32">Status</th>
                    <th class="text-center w-48">Update</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->currentTeamData as $index => $record)
                    @php
                        $isEmpty = empty(trim($record->status));
                        $rowClass = 'hover:bg-base-200/50';
                    @endphp
                    <tr class="{{ $rowClass }} transition-colors">
                        <td class="text-base-content/50 w-12">{{ $index + 1 }}</td>
                        <td class="font-medium">
                            <div class="flex items-center gap-2">
                                {{ $record->user->name }}
                            </div>
                        </td>
                        <td class="text-center">
                            @if(!$isEmpty)
                                <div
                                    class="px-2 py-0.5 text-xs font-bold rounded shadow-sm {{ $this->getStatusColor($record->status) }} inline-block min-w-[3rem]">
                                    {{ $record->status }}
                                </div>
                            @else
                                <span class="text-base-content/30 text-xs italic">- Kosong -</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <select class="select select-bordered select-xs w-full max-w-xs focus:ring-1 focus:ring-primary"
                                wire:change="updateStatus({{ $record->id }}, $event.target.value)">
                                @foreach($this->getStatusOptions() as $opt)
                                    <option value="{{ $opt }}" @selected($record->status === $opt)>
                                        {{ $opt === ' ' ? 'Pilih Status' : $opt }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-base-content/50">
                            <x-icon name="o-inbox" class="w-12 h-12 mx-auto opacity-20 mb-2" />
                            Belum ada data untuk pencarian/seksi ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>