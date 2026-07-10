<div class="space-y-6">
    {{-- Basic Electrical --}}
    @if(count($elecSkills) > 0)
    <div>
        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Basic Electrical</h4>
        <div class="space-y-3">
            @foreach($elecSkills as $skill)
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $skill->skill_name }}</span>
                    <div class="flex gap-1">
                        @for($i = 1; $i <= 4; $i++)
                            <div class="w-4 h-4 rounded {{ $i <= $skill->actual_level ? 'bg-success' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Basic Mechanical --}}
    @if(count($mechSkills) > 0)
    <div>
        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Basic Mechanical</h4>
        <div class="space-y-3">
            @foreach($mechSkills as $skill)
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $skill->skill_name }}</span>
                    <div class="flex gap-1">
                        @for($i = 1; $i <= 4; $i++)
                            <div class="w-4 h-4 rounded {{ $i <= $skill->actual_level ? 'bg-success' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Advance Electrical --}}
    @if(count($advElecSkills) > 0)
    <div>
        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Advance Electrical</h4>
        <div class="space-y-3">
            @foreach($advElecSkills as $skill)
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $skill->skill_name }}</span>
                    <div class="flex gap-1">
                        @for($i = 1; $i <= 4; $i++)
                            <div class="w-4 h-4 rounded {{ $i <= $skill->actual_level ? 'bg-success' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(count($elecSkills) == 0 && count($mechSkills) == 0 && count($advElecSkills) == 0)
        <div class="flex flex-col items-center justify-center p-6 text-center opacity-60">
            <x-icon name="o-academic-cap" class="w-10 h-10 mb-2" />
            <p class="text-sm italic">No training skills recorded.</p>
        </div>
    @endif
</div>
