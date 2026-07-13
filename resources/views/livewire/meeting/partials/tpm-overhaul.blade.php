<div x-show="tab === 'tpm-overhaul'" class="space-y-2 pb-10">

    {{-- Deep Cleaning --}}
    <div class="flex items-center gap-3 mb-4">
        <div class="w-1 h-6 bg-indigo-500 rounded-full"></div>
        <span class="text-sm font-bold uppercase tracking-widest text-indigo-500">Deep Cleaning (TPM)</span>
    </div>
    <x-card class="shadow-sm rounded-2xl border-l-4 border-l-indigo-500">
        <div class="overflow-x-auto">
            <table class="table table-sm w-full">
                <thead>
                    <tr class="bg-base-200/50"><th>Date</th><th>Line</th><th>Machine</th></tr>
                </thead>
                <tbody>
                    @forelse($tpmRecords as $tpm)
                    <tr class="hover:bg-base-200/30 transition-colors">
                        <td class="text-gray-500 whitespace-nowrap">{{ $tpm->Date ? $tpm->Date->format('d M') : '-' }}</td>
                        <td class="font-medium">{{ $tpm->LineName }}</td>
                        <td>{{ $tpm->MachineName }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-gray-400 py-8 italic">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- Overhaul --}}
    <div class="flex items-center gap-3 mt-8 mb-4">
        <div class="w-1 h-6 bg-violet-500 rounded-full"></div>
        <span class="text-sm font-bold uppercase tracking-widest text-violet-500">Overhaul</span>
    </div>
    <x-card class="shadow-sm rounded-2xl border-l-4 border-l-violet-500">
        <div class="overflow-x-auto">
            <table class="table table-sm w-full">
                <thead>
                    <tr class="bg-base-200/50"><th>Date</th><th>Line</th><th>Machine</th></tr>
                </thead>
                <tbody>
                    @forelse($overhaulRecords as $oh)
                    <tr class="hover:bg-base-200/30 transition-colors">
                        <td class="text-gray-500 whitespace-nowrap">{{ $oh->date ? $oh->date->format('d M') : '-' }}</td>
                        <td class="font-medium">{{ $oh->LineName }}</td>
                        <td>{{ $oh->MachineName }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-gray-400 py-8 italic">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

</div>
