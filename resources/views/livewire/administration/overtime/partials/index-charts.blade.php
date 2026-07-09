<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 items-start">
    <!-- Bar Chart (2/3 width on large screens) -->
    <div class="lg:col-span-2">
        <x-card class="shadow-sm" shadow title="Komparasi Lembur Tim" subtitle="Jam Aktual vs Target Maksimal">
            <x-chart wire:model="myChart" />
        </x-card>
    </div>

    <!-- Top 5 Overtime Widget (1/3 width on large screens) -->
    <div class="lg:col-span-1">
        <x-card class="shadow-sm" shadow title="Top 5 Karyawan" subtitle="Jam lembur tertinggi periode ini">
            <div class="overflow-x-auto">
                <table class="table table-sm w-full">
                    <thead>
                        <tr class="bg-base-200">
                            <th>Nama</th>
                            <th class="text-right">Jam</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->topOvertime as $user)
                            <tr>
                                <td>
                                    <div class="font-bold">{{ $user->name }}</div>
                                    <div class="text-xs text-base-content/60">{{ $user->team }} - {{ $user->rank }}</div>
                                </td>
                                <td class="text-right font-bold {{ $user->total_jam1 > ($user->{$this->targetField} ?? 0) ? 'text-error' : 'text-primary' }}">
                                    {{ number_format($user->total_jam2, 1, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-gray-500 py-4">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</div>
