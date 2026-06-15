<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-sans antialiased bg-base-200">

    {{-- NAVBAR (Mobile Only) --}}
    <x-nav sticky class="lg:hidden bg-base-100/90 backdrop-blur-sm border-b border-base-200">
        <x-slot:brand>
            <label for="main-drawer" class="lg:hidden mr-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
            <a href="/dashboard" class="lg:hidden">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-6 w-auto" />
            </a>
        </x-slot:brand>
    </x-nav>

    {{-- MAIN --}}
    <x-main>
        {{-- SIDEBAR --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 lg:bg-inherit">

            {{-- BRAND & THEME TOGGLE --}}
            <div class="px-5 pt-4 flex justify-between items-center">
                <a href="/dashboard">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto drop-shadow-sm" />
                </a>
                <x-theme-toggle darkTheme="dim" lightTheme="corporate" class="btn btn-circle btn-ghost btn-sm lg:hidden" />
            </div>

            {{-- MENU --}}
            <x-menu activate-by-route>

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
                <x-menu-item title="Dashboard" icon="o-home" link="/dashboard" />

                {{-- Maintenance --}}
                <x-menu-sub title="Maintenance" icon="o-wrench-screwdriver">
                    <x-menu-item title="Cardty" icon="o-clipboard-document-list" link="/maintenance/cardty" />
                    <x-menu-item title="Check Sheet" icon="o-document-check" link="/checksheet" />
                    <x-menu-item title="CS Monitoring" icon="o-computer-desktop" link="/checksheet/monitoring" />
                    <x-menu-item title="Andon" icon="o-bell" link="/andon" />
                    <x-menu-item title="One Hour Over" icon="o-clock" link="/maintenance/one-hour-over" />
                </x-menu-sub>

                {{-- Deep Cleaning / TPM --}}
                <x-menu-sub title="Deep Cleaning" icon="o-clipboard-document-check">
                    <x-menu-item title="Before After" icon="o-chart-bar" link="/tpm/list" />
                    <x-menu-item title="Schedule" icon="o-calendar-days" link="/tpm/schedule" />
                    <x-menu-item title="Check Sheet" icon="o-document-check" link="/tpm/checksheet" />
                </x-menu-sub>

                {{-- Over Houl --}}
                <x-menu-sub title="Over Houl" icon="o-cog-8-tooth">
                    <x-menu-item title="Report" icon="o-document-text" link="/overhaul" />
                </x-menu-sub>

                {{-- Spare Part --}}
                <x-menu-sub title="Spare Part" icon="o-cog-6-tooth">
                    <x-menu-item title="Spare Part Check" icon="o-check-badge" link="/spare-parts" />
                    <x-menu-item title="Stock Taking" icon="o-clipboard-document" link="/spare-parts/stock-taking" />
                    <x-menu-item title="Repair" icon="o-wrench" link="/spare-parts/repair" />
                </x-menu-sub>

                {{-- Administration --}}
                <x-menu-sub title="Administration" icon="o-user-group">
                    <x-menu-item title="Attendance" icon="o-user" link="/admin/attendance" />
                    <x-menu-item title="Over Time" icon="o-clock" link="/admin/overtime" />
                    <x-menu-item title="MyInfo" icon="o-identification" link="/admin/my-info" />
                    <x-menu-item title="SS" icon="o-light-bulb" link="/admin/ss" />
                    <x-menu-item title="KYT" icon="o-shield-exclamation" link="/admin/kyt" />
                    <x-menu-item title="Rolling Break" icon="o-arrow-path" link="/admin/rolling-break" />
                </x-menu-sub>

                {{-- Master Data --}}
                <x-menu-sub title="Master Data" icon="o-circle-stack">
                    <x-menu-item title="User Management" icon="o-users" link="/users" />
                    <x-menu-item title="Assets" icon="o-cpu-chip" link="/assets" />
                    <x-menu-item title="Spare Parts" icon="o-wrench-screwdriver" link="/spare-parts" />
                    <x-menu-item title="Master Checksheet" icon="o-clipboard-document-check"
                        link="/checksheet/master" />
                </x-menu-sub>

                {{-- Standalone items --}}
                <x-menu-item title="Annalize Problem" icon="o-chart-pie" link="/problem-analysis" />
                <x-menu-item title="Work Order" icon="o-document-text" link="/work-orders" />
                <x-menu-item title="Meeting" icon="o-video-camera" link="/meeting" />

                <x-menu-separator class="lg:hidden" />

                {{-- Profile (Mobile Only) --}}
                <x-menu-item title="My Profile" icon="o-user-circle" link="/profile" class="lg:hidden" />

            </x-menu>
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content>
            {{-- Desktop Top Header --}}
            @if($user = auth()->user())
                <div class="hidden lg:flex justify-end items-center gap-4 mb-6 pb-4 pt-4 -mt-4 sticky top-0 z-10 bg-base-200/90 backdrop-blur-sm border-b border-base-300">
                    <x-theme-toggle darkTheme="dim" lightTheme="corporate" class="btn btn-circle btn-ghost btn-sm" />
                    
                    <x-dropdown right>
                        <x-slot:trigger>
                            <x-button class="btn-ghost normal-case flex items-center gap-2">
                                <x-icon name="o-user-circle" class="w-6 h-6" />
                                <span>{{ $user->name }}</span>
                                <x-icon name="o-chevron-down" class="w-3 h-3 opacity-50" />
                            </x-button>
                        </x-slot:trigger>
                        
                        <x-menu-item title="My Profile" icon="o-user" link="/profile" />
                        <x-menu-separator />
                        <x-menu-item title="Logout" icon="o-power" class="text-error" onclick="logout_modal.showModal()" />
                    </x-dropdown>
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

    {{-- TOAST area --}}
    <x-toast />
</body>

</html>