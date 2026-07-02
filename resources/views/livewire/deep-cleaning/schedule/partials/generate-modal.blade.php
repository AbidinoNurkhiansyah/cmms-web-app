    {{-- Generate Modal --}}
    <x-modal wire:model="generateModal" title="Generate Jadwal TPM" class="backdrop-blur !transition-none !duration-0" box-class="w-10/12 max-w-4xl max-h-[80vh] !overflow-hidden !transition-none !transform-none !duration-0">
        <div class="flex flex-col gap-4">
            <div class="flex flex-col sm:flex-row gap-2 items-end">
                <div class="flex gap-2 w-full sm:w-auto">
                    <div class="flex-1 sm:w-48">
                        <x-input type="month" label="Plan Bulan & Tahun" wire:model.live="generateMonthYear" />
                    </div>
                    <div class="flex-1 sm:w-48">
                        <x-select label="Filter Line" wire:model.live="generateLineFilter" :options="$generateLines" option-value="id" option-label="name" placeholder="Semua Line" placeholder-value="" />
                    </div>
                </div>
                <div class="w-full sm:flex-1">
                    <x-input icon="o-magnifying-glass" placeholder="Cari nama mesin atau nomor aset..." wire:model.live.debounce.300ms="generateSearch" />
                </div>
            </div>

            <x-tabs wire:model="generateTab" active-class="bg-neutral text-neutral-content">
                <x-tab name="planning" label="Belum Terjadwal">
                    <div class="overflow-x-auto overflow-y-auto max-h-[calc(80vh-16rem)]">
                        <table class="table table-sm table-pin-rows">
                            <thead>
                                <tr>
                                    <th>Line</th>
                                    <th>Mesin</th>
                                    <th>Mesin No</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(collect($generateMachines)->where('added', false) as $m)
                                    <tr>
                                        <td>{{ $m['line'] }}</td>
                                        <td>{{ $m['machine'] }}</td>
                                        <td>{{ $m['machine_no'] }}</td>
                                        <td>
                                            <x-button label="Tambah" class="btn-sm btn-success"
                                                wire:click="addToSchedule('{{ addslashes($m['line']) }}', '{{ addslashes($m['machine']) }}', '{{ addslashes($m['machine_no']) }}')" />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Semua mesin sudah terjadwal di bulan ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-tab>
                <x-tab name="done" label="Sudah Terjadwal">
                    <div class="overflow-x-auto overflow-y-auto max-h-[calc(80vh-16rem)]">
                        <table class="table table-sm table-pin-rows">
                            <thead>
                                <tr>
                                    <th>Line</th>
                                    <th>Mesin</th>
                                    <th>Mesin No</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(collect($generateMachines)->where('added', true) as $m)
                                    <tr class="bg-success/20">
                                        <td>{{ $m['line'] }}</td>
                                        <td>{{ $m['machine'] }}</td>
                                        <td>{{ $m['machine_no'] }}</td>
                                        <td><x-badge value="Added" class="badge-success" /></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada mesin yang terjadwal.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-tab>
            </x-tabs>
        </div>
        <x-slot:actions>
            <x-button label="Tutup" @click="$wire.generateModal = false" />
        </x-slot:actions>
    </x-modal>
