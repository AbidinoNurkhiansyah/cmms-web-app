<div class="overflow-x-auto">
    <table class="table w-full table-zebra">
        <thead>
            <tr>
                <th>No</th>
                <th>Team/Rank</th>
                <th>Job Description</th>
                <th>Units</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jobdescs as $index => $jd)
                <tr>
                    <td>{{ $jobdescs->firstItem() + $index }}</td>
                    <td>{{ $jd->team }}</td>
                    <td class="whitespace-normal min-w-48">{{ $jd->description }}</td>
                    <td>
                        <div class="flex flex-wrap gap-1">
                            @if(is_array($jd->units))
                                @foreach($jd->units as $unit)
                                    <div class="badge badge-primary badge-outline badge-sm">{{ $unit }}</div>
                                @endforeach
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="flex justify-center gap-2">
                            <x-button icon="o-pencil" class="btn-sm btn-ghost text-info" wire:click="openEditModal({{ $jd->id }})" />
                            <x-button icon="o-trash" class="btn-sm btn-ghost text-error" wire:click="confirmDelete({{ $jd->id }})" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No job descriptions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4">
        {{ $jobdescs->links() }}
    </div>
</div>
