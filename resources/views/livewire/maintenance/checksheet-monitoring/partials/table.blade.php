<div class="overflow-x-auto w-full max-h-[75vh]" x-data x-init="$nextTick(() => { 
    let el = document.getElementById('today-col'); 
    if(el) {
        // scrollBy a bit to center or just scrollIntoView
        el.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }
})">
    <table class="table table-xs table-bordered text-center whitespace-nowrap w-full">
        <thead class="sticky top-0 bg-base-300 z-20 shadow-sm">
            <tr>
                <th class="text-left min-w-40 sticky left-0 bg-base-300 z-30 border-r">Machine Name</th>
                <th class="text-center min-w-24 bg-base-300">Asset No</th>
                <th class="text-center min-w-16 bg-base-300">Total Point</th>
                @for($d = 1; $d <= $daysInMonth; $d++)
                    @php $isToday = (date('Y-m') === $selectedYear.'-'.$selectedMonth && $d === (int)date('d')); @endphp
                    <th {!! $isToday ? 'id="today-col"' : '' !!} class="min-w-10 {{ $isToday ? 'bg-primary text-primary-content' : '' }}">
                        {{ $d }}
                    </th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse($machines as $m)
                <tr class="border-b border-base-200">
                    <td class="text-left font-medium sticky left-0 bg-base-100 z-10 border-r truncate max-w-64" style="transform: translateZ(0);">
                        {{ $m->machine_name }}
                    </td>
                    <td class="text-xs text-base-content/70">{{ $m->asset_no }}</td>
                    @php
                        $qty = $qtyMap[$m->asset_no] ?? 0;
                    @endphp
                    <td class="font-bold">{{ $qty }}</td>
                    
                    @for($d = 1; $d <= $daysInMonth; $d++)
                        @php
                            $dateStr = $baseDate->copy()->day($d)->format('Y-m-d');
                            $totalActual = $resultMap[$m->asset_no][$dateStr] ?? '';
                            $noteData = $notesMap[$m->asset_no][$dateStr] ?? null;
                            
                            $hasUnresolved = $noteData && $noteData['unresolved'] > 0;
                            $isKurang = ($totalActual !== '' && $totalActual < $qty);
                            
                            // Set cell classes based on conditions
                            $cellClass = '';
                            if ($hasUnresolved) {
                                $cellClass = 'bg-warning/30 hover:bg-warning/50 cursor-pointer transition-colors';
                            } elseif ($isKurang) {
                                $cellClass = 'bg-error/10 text-error font-bold';
                            } elseif ($totalActual !== '') {
                                $cellClass = 'bg-success/10 text-success font-bold';
                            }
                        @endphp
                        
                        <td class="{{ $cellClass }}" 
                            @if($hasUnresolved) 
                                wire:click="openNoteModal('{{ $m->asset_no }}', '{{ addslashes($m->machine_name) }}', '{{ $dateStr }}', '{{ addslashes($noteData['inspector'] ?? '') }}')"
                                title="Click to Follow Up Note"
                            @endif
                        >
                            @if($totalActual !== '')
                                @if($hasUnresolved)
                                    <div class="flex items-center justify-center gap-1 font-bold">
                                        {{ $totalActual }}
                                        <span class="text-warning text-xs">⚠</span>
                                    </div>
                                @else
                                    {{ $totalActual }}
                                @endif
                            @else
                                <span class="opacity-20">-</span>
                            @endif
                        </td>
                    @endfor
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 3 + $daysInMonth }}" class="text-center py-6 text-base-content/50">
                        <x-icon name="o-inbox" class="w-12 h-12 mx-auto mb-2 opacity-30" />
                        Tidak ada data mesin.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Legend --}}
<div class="p-4 bg-base-200/50 flex flex-wrap gap-4 text-xs">
    <span class="flex items-center gap-1 bg-success/10 text-success font-bold px-2 py-0.5 rounded">
        123 OK / Sesuai Target
    </span>
    <span class="flex items-center gap-1 bg-warning/30 text-warning-content font-bold px-2 py-0.5 rounded">
        <x-icon name="o-exclamation-triangle" class="w-3 h-3" /> NG / Unresolved Notes (Klik utk Follow Up)
    </span>
    <span class="flex items-center gap-1 bg-error/10 text-error font-bold px-2 py-0.5 rounded">
        123 Incomplete (Cek Kurang)
    </span>
</div>
