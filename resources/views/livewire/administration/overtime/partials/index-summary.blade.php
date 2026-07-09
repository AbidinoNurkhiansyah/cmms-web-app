<div class="mb-6 mt-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse($this->summaryData as $row)
            @php
                $isWarning = $row->selisih < 0;
                $iconColor = $isWarning ? 'text-error' : 'text-success';
                $icon = $isWarning ? 'o-exclamation-triangle' : 'o-check-circle';
            @endphp
            <x-stat 
                title="Total Selisih {{ $row->section }}" 
                value="{{ number_format($row->selisih, 1, ',', '.') }} Jam" 
                icon="{{ $icon }}"
                class="shadow-sm {{ $isWarning ? 'border-error/30' : 'border-success/30' }} border"
                color="{{ $iconColor }}"
                description="Max: {{ number_format($row->sum_target, 1, ',', '.') }} | Jam: {{ number_format($row->sjam, 1, ',', '.') }}" 
            />
        @empty
            <div class="col-span-3 text-center py-6 text-gray-500">
                Tidak ada data rekapitulasi.
            </div>
        @endforelse
    </div>
</div>
