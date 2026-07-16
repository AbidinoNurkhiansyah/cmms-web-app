@php
    $safetySlides = [
        ['title' => 'Emergency Stop', 'text' => 'Tekan Tombol Emergency Stop saat kondisi darurat.', 'icon' => 'o-exclamation-triangle', 'color' => 'text-error'],
        ['title' => 'Manual Mode', 'text' => 'Pindahkan Selector Switch Ke Manual sebelum perbaikan.', 'icon' => 'o-adjustments-horizontal', 'color' => 'text-warning'],
        ['title' => 'LOTO Procedure', 'text' => 'Gantung Safety Tag Sebelum Memulai Pekerjaan.', 'icon' => 'o-tag', 'color' => 'text-info'],
        ['title' => 'Pneumatic Safety', 'text' => 'Buang Sisa Tekanan Angin hingga tuntas.', 'icon' => 'o-arrow-down-circle', 'color' => 'text-success'],
    ];
@endphp

@if(auth()->user() && auth()->user()->hasAnyRole([\App\Models\User::ROLE_TECHNICIAN, \App\Models\User::ROLE_PLANNER, \App\Models\User::ROLE_SUPERVISOR]))
    {{-- Safety Commitment Banner --}}
    <div class="mb-5 overflow-hidden rounded-2xl bg-gradient-to-r from-base-200 to-base-100 border border-base-200 shadow-sm relative"
         x-data="{
            current: 0,
            init() { setInterval(() => { this.current = (this.current + 1) % {{ count($safetySlides) }} }, 6000) }
         }">
        <div class="px-6 py-5 flex items-center justify-between">
            <div class="flex-1">
                <div class="text-[10px] font-bold uppercase tracking-widest opacity-50 mb-2">Safety Commitment</div>
                <div class="relative h-12">
                    @foreach($safetySlides as $i => $slide)
                        <div x-show="current === {{ $i }}" 
                             x-transition:enter="transition ease-out duration-700"
                             x-transition:enter-start="opacity-0 translate-y-2" 
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-300 absolute inset-0"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="flex items-center gap-4" style="display: none;">
                            <div class="p-2.5 rounded-xl bg-base-100 shadow-sm border border-base-200">
                                <x-icon name="{{ $slide['icon'] }}" class="w-6 h-6 {{ $slide['color'] }}" />
                            </div>
                            <div>
                                <div class="font-bold text-sm">{{ $slide['title'] }}</div>
                                <div class="text-xs opacity-70 mt-0.5">{{ $slide['text'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            {{-- Dots Indicator --}}
            <div class="flex gap-1.5 ml-4">
                @foreach($safetySlides as $i => $slide)
                    <button @click="current = {{ $i }}" 
                            class="h-1.5 rounded-full transition-all duration-300"
                            :class="current === {{ $i }} ? 'bg-primary w-4' : 'bg-base-content/20 hover:bg-base-content/40 w-1.5'"></button>
                @endforeach
            </div>
        </div>
    </div>
@endif
