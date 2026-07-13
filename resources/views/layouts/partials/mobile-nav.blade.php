{{-- NAVBAR (Mobile Only) --}}
<x-nav sticky class="lg:hidden bg-base-100/90 backdrop-blur-sm border-b border-base-200">
    <x-slot:brand>
        <label for="main-drawer" class="lg:hidden mr-3">
            <x-icon name="o-bars-3" class="cursor-pointer" />
        </label>
        <a href="/dashboard" class="lg:hidden" wire:navigate>
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-6 w-auto" />
        </a>
    </x-slot:brand>
</x-nav>
