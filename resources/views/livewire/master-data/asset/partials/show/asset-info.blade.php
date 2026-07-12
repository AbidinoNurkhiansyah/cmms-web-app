    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Asset Info Card --}}
        <x-card class="lg:col-span-2">
            <div class="flex flex-col md:flex-row gap-6">
                <div class="w-full md:w-1/3">
                    @if($asset->machine_photo)
                        <img src="{{ Storage::url($asset->machine_photo) }}" alt="{{ $asset->machine_name }}"
                            class="w-full rounded-lg object-cover shadow" style="max-height:220px">
                    @else
                        <div class="w-full rounded-lg bg-base-200 flex items-center justify-center shadow" style="height:220px">
                            <x-icon name="o-photo" class="w-16 h-16 opacity-20" />
                        </div>
                    @endif
                </div>
                <div class="w-full md:w-2/3">
                    <table class="table table-sm w-full">
                        <tr><td class="font-semibold opacity-60 w-1/3">Asset No</td><td class="font-mono">{{ $asset->asset_no }}</td></tr>
                        <tr><td class="font-semibold opacity-60">Line</td><td>{{ $asset->line_name ?? '—' }}</td></tr>
                        <tr><td class="font-semibold opacity-60">Machine</td><td>{{ $asset->machine_name ?? '—' }}</td></tr>
                        <tr><td class="font-semibold opacity-60">Maker</td><td>{{ $asset->maker ?? '—' }}</td></tr>
                        <tr><td class="font-semibold opacity-60">Year</td><td>{{ $asset->manufacture_year ?? '—' }}</td></tr>
                        <tr>
                            <td class="font-semibold opacity-60">Rank</td>
                            <td>
                                @if($asset->machine_rank)
                                    <x-badge value="{{ $asset->machine_rank }}"
                                        class="{{
                                            match($asset->machine_rank) {
                                                'A' => 'badge-error text-white',
                                                'B' => 'badge-warning',
                                                'C' => 'badge-info text-white',
                                                'D' => 'badge-success text-white',
                                                default => 'badge-ghost'
                                            }
                                        }}" />
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </x-card>

        {{-- Spare Parts Card with Chart --}}
        <x-card class="hover:shadow-lg transition-shadow text-center flex flex-col justify-center items-center relative">
            <div class="flex justify-center items-center w-full mb-2">
                <h6 class="font-semibold">Spare Parts</h6>
            </div>
            <div class="cursor-pointer w-full flex flex-col justify-center items-center" wire:click="$set('showSparepartModal', true)">
            @if(empty($sparePartsChartData['data']))
                <div class="w-full max-w-[200px] aspect-square flex flex-col justify-center items-center opacity-50 bg-base-200 rounded-full my-4">
                    <x-icon name="o-archive-box-x-mark" class="w-8 h-8 mb-2" />
                    <span class="text-xs">No Data</span>
                </div>
            @else
                <div class="w-full max-w-[200px] aspect-square" wire:ignore
                     x-data='{
                         labels: @json($sparePartsChartData["labels"]),
                         data: @json($sparePartsChartData["data"]),
                         colors: @json($sparePartsChartData["colors"]),
                         init() {
                             new Chart(this.$refs.spCanvas, {
                                 type: "pie",
                                 data: {
                                     labels: this.labels,
                                     datasets: [{
                                         data: this.data,
                                         backgroundColor: this.colors,
                                     }]
                                 },
                                 options: {
                                     responsive: true,
                                     maintainAspectRatio: false,
                                     plugins: { legend: { display: false } }
                                 }
                             });
                         }
                     }'>
                    <canvas x-ref="spCanvas"></canvas>
                </div>
            @endif
            <p class="text-xs opacity-60 mt-2">Click to view details</p>
            </div>
        </x-card>
    </div>
