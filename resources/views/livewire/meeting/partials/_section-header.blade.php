@props(['label', 'color' => 'text-base-content/50', 'border' => 'bg-base-content/30'])

<div class="flex items-center gap-3 mt-8 mb-4 first:mt-0">
    <div class="w-1 h-6 {{ $border }} rounded-full"></div>
    <span class="text-sm font-bold uppercase tracking-widest {{ $color }}">{{ $label }}</span>
</div>
