<div x-data="{ activeTab: 'dashboard' }">
    <div class="tabs tabs-bordered mb-4 w-full flex">
        <a href="#" class="tab tab-lg gap-2 flex-1 transition-colors duration-200"
            :class="{ 'tab-active bg-black text-white': activeTab === 'dashboard' }"
            @click.prevent="activeTab = 'dashboard'">
            <x-icon name="o-chart-bar" class="w-5 h-5" />
            Dashboard
        </a>
        <a href="#" class="tab tab-lg gap-2 flex-1 transition-colors duration-200"
            :class="{ 'tab-active bg-black text-white': activeTab === 'list' }" @click.prevent="activeTab = 'list'">
            <x-icon name="o-list-bullet" class="w-5 h-5" />
            Call List
        </a>
    </div>

    <div x-show="activeTab === 'dashboard'" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        <div wire:ignore.self>
            @include('livewire.maintenance.andon.partials.dashboard-tab')
        </div>
    </div>

    <div x-show="activeTab === 'list'" style="display: none;" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        <div wire:ignore.self>
            @include('livewire.maintenance.andon.partials.list-tab')
        </div>
    </div>
</div>