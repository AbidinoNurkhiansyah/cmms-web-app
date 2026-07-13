{{-- Desktop Top Header --}}
@if($user = auth()->user())
    <div class="hidden lg:flex justify-between items-center gap-2 mb-4 pb-2 pt-4 -mt-4 sticky top-0 z-10 bg-gray-100/90 dark:bg-base-200/90 backdrop-blur-sm border-b border-base-content/10">
        
        {{-- Page Title (Left) --}}
        <div class="flex-1">
            <div class="text-2xl font-bold">{{ $title ?? 'Dashboard' }}</div>
            <div class="text-sm text-base-content/70">{{ now()->format('l, d F Y') }}</div>
        </div>

        {{-- Actions (Right) --}}
        <div class="flex items-center gap-2">
        
            <x-button class="btn-ghost normal-case flex items-center gap-2" link="/profile" wire:navigate>
                <x-icon name="o-user-circle" class="w-6 h-6" />
                <span class="font-medium">{{ $user->name }}</span>
            </x-button>
            
            <div class="divider divider-horizontal mx-0 my-2 w-0.5"></div>
            
            <x-button icon="o-power" class="btn-ghost text-error btn-circle btn-sm" tooltip-left="Logout" onclick="logout_modal.showModal()" />
        </div>
    </div>
@endif
