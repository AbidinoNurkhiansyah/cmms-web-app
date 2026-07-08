@php
    $summary = $this->attendanceData['summary'];
@endphp

<div class="mb-6">
    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total -->
        <div class="stat bg-base-100 shadow-sm rounded-box border border-base-200 px-4 py-3">
            <div class="stat-figure text-base-content/50">
                <x-icon name="o-users" class="w-8 h-8" />
            </div>
            <div class="stat-title font-semibold">Total Karyawan</div>
            <div class="stat-value text-2xl">{{ $summary['total'] }}</div>
            <div class="stat-desc mt-1">
                @if($summary['empty'] > 0)
                    <span class="text-error font-semibold">Belum diisi: {{ $summary['empty'] }}</span>
                @else
                    <span class="text-success font-semibold">Semua terisi</span>
                @endif
            </div>
        </div>

        <!-- Hadir -->
        <div class="stat bg-base-100 shadow-sm rounded-box border border-base-200 px-4 py-3">
            <div class="stat-figure text-success">
                <x-icon name="o-check-badge" class="w-8 h-8" />
            </div>
            <div class="stat-title font-semibold text-success">Hadir</div>
            <div class="stat-value text-2xl text-success">{{ $summary['present'] }}</div>
            <div class="stat-desc text-success mt-1">P, S, BT</div>
        </div>

        <!-- Cuti/Izin -->
        <div class="stat bg-base-100 shadow-sm rounded-box border border-base-200 px-4 py-3">
            <div class="stat-figure text-warning">
                <x-icon name="o-envelope-open" class="w-8 h-8" />
            </div>
            <div class="stat-title font-semibold text-warning">Cuti / Izin</div>
            <div class="stat-value text-2xl text-warning">{{ $summary['leave'] }}</div>
            <div class="stat-desc text-warning mt-1">CL, AL, SL, CD</div>
        </div>

        <!-- Mangkir -->
        <div class="stat bg-base-100 shadow-sm rounded-box border border-base-200 px-4 py-3">
            <div class="stat-figure text-error">
                <x-icon name="o-x-circle" class="w-8 h-8" />
            </div>
            <div class="stat-title font-semibold text-error">Mangkir</div>
            <div class="stat-value text-2xl text-error">{{ $summary['absent'] }}</div>
            <div class="stat-desc text-error mt-1">A, UL</div>
        </div>
    </div>
</div>
