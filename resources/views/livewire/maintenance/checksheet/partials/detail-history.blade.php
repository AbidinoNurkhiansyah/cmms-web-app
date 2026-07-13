@if(!$items->isEmpty())
@php
    $history = $this->history;
    $daysInMonth = $history['daysInMonth'];
    $historyMonth = $history['month'];
    $historyData = $history['data'];
    $today = \Illuminate\Support\Carbon::today()->day;
    $isCurrentMonth = $historyMonth->isSameMonth(\Illuminate\Support\Carbon::now());
@endphp
<x-card class="mb-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
        <h3 class="text-xl font-bold">Monthly History</h3>
        <x-input type="month" wire:model.live="filterMonth" class="input-sm w-48" />
    </div>

    <div class="overflow-x-auto">
        <table class="table table-xs table-bordered text-center">
            <thead>
                <tr class="bg-base-300">
                    <th class="text-left min-w-40 sticky left-0 bg-base-300 z-10">Point Check</th>
                    @for($d = 1; $d <= $daysInMonth; $d++)
                        <th class="min-w-10 {{ ($isCurrentMonth && $d === $today) ? 'bg-primary text-primary-content' : '' }}">
                            {{ $d }}
                        </th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr class="hover">
                        <td class="text-left font-medium sticky left-0 bg-base-100 z-10 border-r">
                            <div class="text-xs text-primary font-semibold">{{ $item->item_check }}</div>
                            <div class="text-[10px] opacity-60">{{ $item->standard }}</div>
                        </td>
                        @for($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $val = $historyData[$item->id][$d] ?? null;
                                $cellClass = '';
                                $bgClass   = ($isCurrentMonth && $d === $today) ? 'bg-primary/10' : '';
                                $display   = '-';

                                if ($val !== null) {
                                    $upper = strtoupper($val);
                                    if ($upper === 'OK') {
                                        $cellClass = 'bg-success/15 text-success font-bold';
                                        $display   = '✓';
                                    } elseif ($upper === 'NG') {
                                        $cellClass = 'bg-error/15 text-error font-bold';
                                        $display   = '✗';
                                    } else {
                                        // Nilai numerik: evaluasi lewat method Livewire
                                        $isOk = $this->evaluateResult((string) $val, $item->standard);
                                        if ($isOk === true) {
                                            $cellClass = 'bg-success/15 text-success font-semibold';
                                        } elseif ($isOk === false) {
                                            $cellClass = 'bg-error/15 text-error font-bold';
                                        } else {
                                            $cellClass = 'text-info font-semibold';
                                        }
                                        $display = $val;
                                    }
                                }
                            @endphp
                            <td class="{{ $cellClass }} {{ $bgClass }}">
                                {{ $display }}
                            </td>
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Legend --}}
    <div class="mt-3 flex flex-wrap gap-4 text-xs">
        <span class="flex items-center gap-1 bg-success/15 text-success font-bold px-2 py-0.5 rounded">✓ OK / Sesuai</span>
        <span class="flex items-center gap-1 bg-error/15 text-error font-bold px-2 py-0.5 rounded">✗ NG / Di luar std</span>
        <span class="flex items-center gap-1 text-info font-semibold opacity-70"># = Nilai tidak bisa dievaluasi</span>
        <span class="opacity-50">- = Belum diisi</span>
    </div>
</x-card>
@endif
