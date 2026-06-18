@props(['title'])

<x-header {{ $attributes->merge(['class' => 'mb-4']) }} separator>
    <x-slot:title>
        <div class="flex items-center gap-2">
            <x-button icon="o-arrow-left" class="btn-circle btn-ghost btn-sm"
                link="{{ route('maintenance.cardty') }}" tooltip-left="Back to List" />
            <span class="text-xl">{{ $title }}</span>
        </div>
    </x-slot:title>
</x-header>
