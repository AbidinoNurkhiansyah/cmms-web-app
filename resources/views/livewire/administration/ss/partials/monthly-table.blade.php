<x-card class="shadow-sm">
    <div class="mb-6 w-full sm:w-48">
        <x-select label="Pilih Tahun" :options="$this->yearOptions" wire:model.live="tahun" icon="o-calendar" />
    </div>

    <div class="overflow-x-auto border border-base-200 rounded-lg">
        <table class="table table-sm w-full table-zebra table-pin-rows table-pin-cols">
            <thead>
                <tr class="bg-base-200 text-base-content text-center">
                    <th class="text-left font-bold">Nama</th>
                    <th class="font-bold bg-base-300">Ttl</th>
                    <th>Jan</th>
                    <th>Feb</th>
                    <th>Mar</th>
                    <th>Apr</th>
                    <th>May</th>
                    <th>Jun</th>
                    <th>Jul</th>
                    <th>Aug</th>
                    <th>Sep</th>
                    <th>Oct</th>
                    <th>Nov</th>
                    <th>Des</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->monthlyData as $row)
                    <tr class="hover:bg-base-200/50 transition-colors text-center">
                        <td class="text-left font-medium whitespace-nowrap">{{ $row['name'] }}</td>
                        <td class="font-bold bg-base-200/50 text-primary">{{ $row['ttl'] }}</td>
                        @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nop', 'Des'] as $m)
                            <td>{{ $row[$m] ?: '-' }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="text-center py-8 text-base-content/50">
                            <x-icon name="o-inbox" class="w-12 h-12 mx-auto opacity-20 mb-2" />
                            Belum ada data usulan untuk tahun {{ $this->tahun }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>
