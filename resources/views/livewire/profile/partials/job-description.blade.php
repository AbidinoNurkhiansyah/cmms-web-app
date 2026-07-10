<div>
    @if(isset($userJobDescriptions) && $userJobDescriptions->count() > 0)
        <ul class="space-y-3">
            @foreach($userJobDescriptions as $jd)
                <li class="flex items-start gap-3 text-sm text-gray-700 dark:text-gray-300">
                    <x-icon name="o-check-circle" class="w-5 h-5 text-success shrink-0" />
                    <span class="leading-relaxed">{{ $jd->description }}</span>
                </li>
            @endforeach
        </ul>
    @else
        <div class="flex flex-col items-center justify-center p-6 text-center opacity-60">
            <x-icon name="o-clipboard-document-list" class="w-10 h-10 mb-2" />
            <p class="text-sm italic">No job descriptions assigned for your current unit/team.</p>
        </div>
    @endif
</div>
