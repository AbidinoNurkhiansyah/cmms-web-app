<x-card class="bg-base-100 shadow-sm">
    <x-table :headers="$headers" :rows="$users" striped link="/master-data/skills/{id}">
        
        @scope('cell_training_skills_count', $user)
            <div class="flex justify-center whitespace-nowrap">
                <x-badge :value="$user->training_skills_count . ' Skills'" class="badge-neutral" />
            </div>
        @endscope

    </x-table>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</x-card>
