{{-- Stats Summary Cards — selalu 1 baris --}}
<div class="flex gap-3 mb-5 overflow-x-auto pb-1">

    <div class="rounded-xl bg-base-100 border border-base-300 px-4 py-3 flex items-center gap-3 flex-1 min-w-[140px]">
        <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
            <x-icon name="o-list-bullet" class="w-5 h-5 text-primary" />
        </div>
        <div>
            <div class="text-xl font-bold leading-tight">{{ $stats['total'] }}</div>
            <div class="text-xs text-base-content/60">Total Item</div>
        </div>
    </div>

    <div class="rounded-xl bg-base-100 border border-base-300 px-4 py-3 flex items-center gap-3 flex-1 min-w-[140px]">
        <div class="w-9 h-9 rounded-lg bg-success/10 flex items-center justify-center shrink-0">
            <x-icon name="o-check-circle" class="w-5 h-5 text-success" />
        </div>
        <div>
            <div class="text-xl font-bold leading-tight text-success">{{ $stats['active'] }}</div>
            <div class="text-xs text-base-content/60">Aktif</div>
        </div>
    </div>

    <div class="rounded-xl bg-base-100 border border-base-300 px-4 py-3 flex items-center gap-3 flex-1 min-w-[140px]">
        <div class="w-9 h-9 rounded-lg bg-info/10 flex items-center justify-center shrink-0">
            <x-icon name="o-sun" class="w-5 h-5 text-info" />
        </div>
        <div>
            <div class="text-xl font-bold leading-tight text-info">{{ $stats['daily'] }}</div>
            <div class="text-xs text-base-content/60">Harian (D)</div>
        </div>
    </div>

    <div class="rounded-xl bg-base-100 border border-base-300 px-4 py-3 flex items-center gap-3 flex-1 min-w-[140px]">
        <div class="w-9 h-9 rounded-lg bg-warning/10 flex items-center justify-center shrink-0">
            <x-icon name="o-calendar" class="w-5 h-5 text-warning" />
        </div>
        <div>
            <div class="text-xl font-bold leading-tight text-warning">{{ $stats['weekly'] }}</div>
            <div class="text-xs text-base-content/60">Mingguan (W)</div>
        </div>
    </div>

    <div class="rounded-xl bg-base-100 border border-base-300 px-4 py-3 flex items-center gap-3 flex-1 min-w-[140px]">
        <div class="w-9 h-9 rounded-lg bg-secondary/10 flex items-center justify-center shrink-0">
            <x-icon name="o-calendar-days" class="w-5 h-5 text-secondary" />
        </div>
        <div>
            <div class="text-xl font-bold leading-tight text-secondary">{{ $stats['monthly'] }}</div>
            <div class="text-xs text-base-content/60">Bulanan (M)</div>
        </div>
    </div>

</div>
