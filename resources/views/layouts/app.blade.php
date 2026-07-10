<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Menyembunyikan garis waktu (progress bar) pada komponen Toast MaryUI */
        .toast progress { 
            display: none !important; 
        }
    </style>
</head>

<body class="min-h-screen font-sans antialiased bg-gray-100 dark:bg-base-200" x-data="{ sidebarCollapsed: {{ session('mary-sidebar-collapsed', 'false') }} }" @sidebar-toggled.window="sidebarCollapsed = $event.detail" :class="{ 'sidebar-collapsed': sidebarCollapsed }">

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

    {{-- MAIN --}}
    <x-main full-width>
        {{-- SIDEBAR --}}
        <x-slot:sidebar drawer="main-drawer" class="bg-base-100">

            {{-- BRAND & THEME TOGGLE --}}
            <div class="px-5 pt-4 mb-8 flex justify-between items-center">
                <a href="/dashboard" class="mary-hideable" wire:navigate>
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto drop-shadow-sm" />
                </a>
                
                <div class="flex items-center gap-1">
                    {{-- Desktop Collapse Button --}}
                    <x-button icon="o-bars-3-bottom-right" @click="toggle()" class="btn-circle btn-ghost btn-sm hidden lg:flex" />
                    
                    {{-- Mobile Theme Toggle --}}
                    <x-theme-toggle darkTheme="dim" lightTheme="corporate" class="btn btn-circle btn-ghost btn-sm lg:hidden" />
                </div>
            </div>

            {{-- MENU --}}
            <x-menu activate-by-route active-bg-color="sidebar-active-item">

                {{-- User info (Mobile Only) --}}
                @if($user = auth()->user())
                    <div class="lg:hidden">
                        <x-menu-separator />
                        <x-list-item :item="$user" value="name" sub-value="email" no-separator no-hover
                            class="-mx-2 !-my-2 rounded">
                            <x-slot:actions>
                                <x-button icon="o-power" class="btn-circle btn-ghost btn-xs text-error" tooltip-left="Logout"
                                    no-wire-navigate onclick="logout_modal.showModal()" />
                            </x-slot:actions>
                        </x-list-item>
                        <x-menu-separator />
                    </div>
                @endif

                {{-- Dashboard --}}
                <x-menu-item title="Dashboard" icon="o-home" link="/dashboard" wire:navigate />

                {{-- Maintenance --}}
                @can('wr.view')
                <x-menu-sub title="Maintenance" icon="o-wrench-screwdriver">
                    <x-menu-item title="Cardty" icon="o-clipboard-document-list" link="/maintenance/cardty" wire:navigate />
                    <x-menu-item title="Check Sheet" icon="o-document-check" link="/checksheet" wire:navigate />
                    <x-menu-item title="CS Monitoring" icon="o-computer-desktop" link="/checksheet/monitoring" wire:navigate />
                    <x-menu-item title="Andon" icon="o-bell" link="/andon" wire:navigate />
                    <x-menu-item title="One Hour Over" icon="o-clock" link="/maintenance/one-hour-over" wire:navigate />
                </x-menu-sub>
                @endcan

                {{-- Deep Cleaning / TPM --}}
                @can('pm.view')
                <x-menu-sub title="Deep Cleaning" icon="o-clipboard-document-check">
                    <x-menu-item title="Before After" icon="o-chart-bar" link="{{ route('deep-cleaning.index') }}" exact wire:navigate />
                    <x-menu-item title="Schedule" icon="o-calendar-days" link="{{ route('deep-cleaning.schedule') }}" wire:navigate />
                    <x-menu-item title="Check Sheet" icon="o-document-check" link="/tpm/checksheet" wire:navigate />
                </x-menu-sub>
                @endcan

                {{-- Over Houl --}}
                @can('pm.view')
                <x-menu-sub title="Over Houl" icon="o-cog-8-tooth">
                    <x-menu-item title="Report" icon="o-document-text" link="/overhaul/report" wire:navigate />
                    <x-menu-item title="History Machine" icon="o-clock" link="/overhaul/history-machine" wire:navigate />
                </x-menu-sub>
                @endcan

                {{-- Spare Part --}}
                @can('sparepart.view')
                <x-menu-sub title="Spare Part" icon="o-cog-6-tooth">
                    <x-menu-item title="Spare Part Check" icon="o-check-badge" link="/spare-parts" exact wire:navigate />
                    <x-menu-item title="Stock Taking" icon="o-clipboard-document" link="/spare-parts/stock-taking" wire:navigate />
                    <x-menu-item title="Repair" icon="o-wrench" link="/spare-parts/repair" wire:navigate />
                </x-menu-sub>
                @endcan

                {{-- Administration --}}
                <x-menu-sub title="Administration" icon="o-user-group">
                    <x-menu-item title="Attendance" icon="o-user" link="/administration/attendance" wire:navigate />
                    <x-menu-item title="Over Time" icon="o-clock" link="/administration/overtime" wire:navigate />
                    <x-menu-item title="My Info" icon="o-identification" link="/administration/my-info" wire:navigate />
                    <x-menu-item title="SS" icon="o-light-bulb" link="/administration/ss" wire:navigate />
                    <x-menu-item title="KYT" icon="o-shield-exclamation" link="/administration/kyt" wire:navigate />
                    <x-menu-item title="Rolling Break" icon="o-arrow-path" link="/administration/rolling-break" wire:navigate />
                </x-menu-sub>

                {{-- Master Data --}}
                <x-menu-sub title="Master Data" icon="o-circle-stack">
                    @if(auth()->user()?->is_admin)
                        <x-menu-item title="User Management" icon="o-users" link="/users" wire:navigate />
                        <x-menu-item title="Master Data Jobdesc" icon="o-document-text" link="/master-data/jobdescs" wire:navigate />
                    @endif
                    @can('asset.view')
                        <x-menu-item title="Assets" icon="o-cpu-chip" link="/assets" wire:navigate />
                    @endcan
                    @can('sparepart.view')
                        <x-menu-item title="Spare Parts" icon="o-wrench-screwdriver" link="/master/spare-parts" exact wire:navigate />
                    @endcan
                    <x-menu-item title="Master Checksheet" icon="o-clipboard-document-check" link="/checksheet/master" wire:navigate />
                </x-menu-sub>

                {{-- Standalone items --}}
                <x-menu-item title="Annalize Problem" icon="o-chart-pie" link="/problem-analysis" wire:navigate />
                @can('wo.view')
                <x-menu-item title="Work Order" icon="o-document-text" link="/work-orders" wire:navigate />
                @endcan
                <x-menu-item title="Meeting" icon="o-video-camera" link="/meeting" wire:navigate />

                <x-menu-separator class="lg:hidden" />

                {{-- Profile (Mobile Only) --}}
                <x-menu-item title="My Profile" icon="o-user-circle" link="/profile" class="lg:hidden" wire:navigate />

            </x-menu>
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content class="lg:!px-6">
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
                        <x-theme-toggle darkTheme="dim" lightTheme="corporate" class="btn btn-circle btn-ghost btn-sm mr-2" />
                    
                        <x-button class="btn-ghost normal-case flex items-center gap-2" link="/profile" wire:navigate>
                            <x-icon name="o-user-circle" class="w-6 h-6" />
                            <span class="font-medium">{{ $user->name }}</span>
                        </x-button>
                        
                        <div class="divider divider-horizontal mx-0 my-2 w-0.5"></div>
                        
                        <x-button icon="o-power" class="btn-ghost text-error btn-circle btn-sm" tooltip-left="Logout" onclick="logout_modal.showModal()" />
                    </div>
                </div>
            @endif

            {{ $slot }}
        </x-slot:content>
    </x-main>

    {{-- DaisyUI Logout Modal --}}
    <dialog id="logout_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Konfirmasi Keluar</h3>
            <p class="py-4">Apakah Anda yakin ingin mengakhiri sesi dan keluar dari sistem?</p>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-ghost">Batal</button>
                </form>
                <button class="btn btn-error text-white"
                    onclick="document.getElementById('logout-form').submit()">Ya, Keluar</button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    {{-- Hidden Logout Form --}}
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    {{-- TOAST area --}}
    <x-toast />
</body>

</html>