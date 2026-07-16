{{-- Activity Summary Stats --}}
<div class="flex items-center justify-between mb-2">
    <h5 class="font-bold opacity-70 uppercase tracking-wider text-sm">Main Activity Summary</h5>
</div>
<div class="stats stats-vertical lg:stats-horizontal shadow-sm border border-base-200 w-full mb-5 bg-base-100">
    
    <div class="stat hover:bg-base-200/50 transition-colors cursor-pointer" onclick="Livewire.navigate('/deep-cleaning')">
        <div class="stat-figure text-black">
            <div class="p-3 bg-black/10 rounded-full">
                <x-icon name="o-wrench-screwdriver" class="w-7 h-7" />
            </div>
        </div>
        <div class="stat-title font-medium text-xs uppercase tracking-wide">Deep Cleaning</div>
        <div class="stat-value text-black text-3xl my-1">{{ $totalTPM }}</div>
        <div class="stat-desc">Records this year</div>
    </div>
    
    <div class="stat border-t lg:border-t-0 lg:border-l border-base-200 hover:bg-base-200/50 transition-colors cursor-pointer" onclick="Livewire.navigate('/maintenance/cardty')">
        <div class="stat-figure text-black">
            <div class="p-3 bg-black/10 rounded-full">
                <x-icon name="o-exclamation-triangle" class="w-7 h-7" />
            </div>
        </div>
        <div class="stat-title font-medium text-xs uppercase tracking-wide">Cardty</div>
        <div class="stat-value text-black text-3xl my-1">{{ $totalCardty }}</div>
        <div class="stat-desc">Issues this year</div>
    </div>
    
    <div class="stat border-t lg:border-t-0 lg:border-l border-base-200 hover:bg-base-200/50 transition-colors cursor-pointer" onclick="Livewire.navigate('/overhaul/report')">
        <div class="stat-figure text-black">
            <div class="p-3 bg-black/10 rounded-full">
                <x-icon name="o-cog-8-tooth" class="w-7 h-7" />
            </div>
        </div>
        <div class="stat-title font-medium text-xs uppercase tracking-wide">Overhaul</div>
        <div class="stat-value text-black text-3xl my-1">{{ $totalOH }}</div>
        <div class="stat-desc">OH made this year</div>
    </div>
    
    <div class="stat border-t lg:border-t-0 lg:border-l border-base-200 hover:bg-base-200/50 transition-colors cursor-pointer" onclick="Livewire.navigate('/work-orders')">
        <div class="stat-figure text-black">
            <div class="p-3 bg-black/10 rounded-full">
                <x-icon name="o-clipboard-document-check" class="w-7 h-7" />
            </div>
        </div>
        <div class="stat-title font-medium text-xs uppercase tracking-wide">Work Orders</div>
        <div class="stat-value text-black text-3xl my-1">{{ $totalWO }}</div>
        <div class="stat-desc">Requests this year</div>
    </div>
    
</div>
