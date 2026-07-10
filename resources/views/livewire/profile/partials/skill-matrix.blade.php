<div id="skill-matrix-container"
     class="grid grid-cols-1 md:grid-cols-2 gap-4"
     data-office-skills='@json($officeSkills ?? [])'
     data-genba-skills='@json($genbaSkills ?? [])'>

    {{-- Office Skill --}}
    <x-card title="Matrix Skill - Office" shadow class="flex flex-col items-center bg-base-100">
        <div class="w-full max-w-[280px]">
            <canvas id="officeMatrixCanvas"></canvas>
        </div>
    </x-card>

    {{-- Genba Skill --}}
    <x-card title="Matrix Skill - Genba" shadow class="flex flex-col items-center bg-base-100">
        <div class="w-full max-w-[280px]">
            <canvas id="genbaMatrixCanvas"></canvas>
        </div>
    </x-card>
</div>