<div class="card bg-base-100 shadow-sm border border-base-200">
    <div class="card-body p-0 overflow-x-auto">
        <table class="table table-zebra table-sm">
            <thead class="bg-base-200 text-base-content/80 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3 text-center">Total Stock</th>
                    <th class="px-4 py-3 text-center">OK</th>
                    <th class="px-4 py-3 text-center">Err</th>
                    <th class="px-4 py-3 text-center">Err %</th>
                    <th class="px-4 py-3 text-center">Not Count</th>
                    <th class="px-4 py-3 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->records as $record)
                    @php
                        $totalTransactions = $record->ok_count + $record->err_count;
                        $errPercent = $totalTransactions > 0 ? round(($record->err_count / $totalTransactions) * 100, 2) : 0;
                        
                        // Calculating Not Count: In legacy, it's total parts - ok - err.
                        // Assuming total parts in master data vs total stock checked.
                        // To keep it simple based on legacy UI output, totalNg = totalStock - ok - ng
                        // where 'ng' is err_count. But we'll just display it cleanly.
                        $totalStock = $record->total_stock;
                        $notCount = $totalStock - $record->ok_count - $record->err_count;
                        
                        $dateStr = \Carbon\Carbon::parse($record->date)->format('Y-m-d');
                    @endphp
                    <tr wire:key="stc-{{ $dateStr }}" class="hover:bg-base-200/50 transition-colors">
                        <td class="px-4 py-3 text-sm font-medium">
                            {{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            {{ $totalStock }}
                        </td>
                        <td class="px-4 py-3 text-sm text-center text-success font-medium">
                            {{ $record->ok_count }}
                        </td>
                        <td class="px-4 py-3 text-sm text-center text-error font-medium">
                            {{ $record->err_count }}
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            {{ $errPercent }}%
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            {{ $notCount }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('spare-parts.stock-taking.detail', ['date' => $dateStr, 'status' => 'ok']) }}" 
                                   class="btn btn-outline btn-success btn-xs font-medium"
                                   wire:navigate>
                                    Check
                                </a>
                                <a href="{{ route('spare-parts.stock-taking.detail', ['date' => $dateStr, 'status' => 'err']) }}" 
                                   class="btn btn-outline btn-error btn-xs font-medium"
                                   wire:navigate>
                                    Error
                                </a>
                                <a href="{{ route('spare-parts.stock-taking.detail', ['date' => $dateStr, 'status' => 'not_found']) }}" 
                                   class="btn btn-outline btn-secondary btn-xs font-medium"
                                   wire:navigate>
                                    Not Found
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-base-content/60">
                            <x-icon name="o-inbox" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                            <p>Belum ada data Stock Taking.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($this->records->hasPages())
    <div class="card-footer border-t border-base-200 p-4">
        {{ $this->records->links() }}
    </div>
    @endif
</div>
