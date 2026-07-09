<!-- Tabs Navigation (Using Native Alpine/DaisyUI to avoid MaryUI parsing bugs) -->
<div x-data="{ activeTab: @entangle('activeTab') }" class="mb-6">
    <div class="w-full flex overflow-x-auto border-b border-base-content/10 mb-4 bg-base-200 rounded-t-lg">
        <a @click="activeTab = 'MTC'" :class="{'bg-primary text-white': activeTab === 'MTC', 'hover:bg-base-300': activeTab !== 'MTC'}" class="flex-1 text-center font-bold py-3 px-6 transition-all cursor-pointer text-base-content/70 rounded-tl-lg">MTC</a>
        <a @click="activeTab = 'PE'" :class="{'bg-primary text-white': activeTab === 'PE', 'hover:bg-base-300': activeTab !== 'PE'}" class="flex-1 text-center font-bold py-3 px-6 transition-all cursor-pointer text-base-content/70">PE</a>
        <a @click="activeTab = 'ME'" :class="{'bg-primary text-white': activeTab === 'ME', 'hover:bg-base-300': activeTab !== 'ME'}" class="flex-1 text-center font-bold py-3 px-6 transition-all cursor-pointer text-base-content/70 rounded-tr-lg">ME</a>
    </div>

    @foreach(['MTC', 'PE', 'ME'] as $section)
    <div x-show="activeTab === '{{ $section }}'" x-cloak>
        <x-card class="mt-4 shadow-sm" shadow>
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-base-200">
                        <tr>
                            <th>Nama Karyawan</th>
                            <th>Level</th>
                            <th class="text-right">Max</th>
                            <th class="text-right">Jam</th>
                            <th class="text-right">Kalkulasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->overtimeData[$section] ?? [] as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->rank }}</td>
                                <td class="text-right">{{ number_format($user->{$this->targetField} ?? 0, 1, ',', '.') }}</td>
                                <td class="text-right {{ ($user->total_jam1 > ($user->{$this->targetField} ?? 0)) ? 'text-error font-bold' : '' }}">{{ number_format($user->total_jam1, 1, ',', '.') }}</td>
                                <td class="text-right font-semibold">{{ number_format($user->total_jam2, 1, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-gray-500 py-6">Tidak ada data SPL di periode ini untuk section {{ $section }}.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
    @endforeach
</div>
