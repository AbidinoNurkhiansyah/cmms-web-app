<?php

use App\Models\Carty;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public string $month = '';
    public string $year = '';
    public string $groupBy = 'MachineName'; // Default grouping
    
    public array $chartData = [];

    public function mount()
    {
        $this->month = date('m');
        $this->year = date('Y');
        $this->loadData();
    }

    public function updated($property)
    {
        $this->loadData();
    }

    public function loadData()
    {
        $groupColumn = $this->groupBy === 'LineName' ? 'LineName' : 'MachineName';

        $data = Carty::query()
            ->select($groupColumn . ' as category', DB::raw('COUNT(*) as total_issues'), DB::raw('SUM(DownTime) as total_downtime'))
            ->whereMonth('Date', $this->month)
            ->whereYear('Date', $this->year)
            ->whereNotNull($groupColumn)
            ->where($groupColumn, '!=', '')
            ->groupBy($groupColumn)
            ->orderByDesc('total_issues')
            ->limit(10)
            ->get();

        $this->chartData = $data->toArray();
    }
};
?>

<div>
    <x-header title="Problem Analysis Dashboard" separator progress-indicator>
        <x-slot:middle class="!justify-end gap-3 flex w-full">
            <x-select 
                wire:model.live="month" 
                :options="[
                    ['id'=>'01','name'=>'January'],['id'=>'02','name'=>'February'],['id'=>'03','name'=>'March'],
                    ['id'=>'04','name'=>'April'],['id'=>'05','name'=>'May'],['id'=>'06','name'=>'June'],
                    ['id'=>'07','name'=>'July'],['id'=>'08','name'=>'August'],['id'=>'09','name'=>'September'],
                    ['id'=>'10','name'=>'October'],['id'=>'11','name'=>'November'],['id'=>'12','name'=>'December']
                ]" 
                option-value="id" option-label="name" 
                label="Month"
            />
            
            <x-select 
                wire:model.live="year" 
                :options="[
                    ['id'=>date('Y')-1,'name'=>date('Y')-1],
                    ['id'=>date('Y'),'name'=>date('Y')],
                    ['id'=>date('Y')+1,'name'=>date('Y')+1]
                ]" 
                option-value="id" option-label="name" 
                label="Year"
            />

            <x-select 
                wire:model.live="groupBy" 
                :options="[
                    ['id'=>'MachineName','name'=>'By Machine'],
                    ['id'=>'LineName','name'=>'By Line']
                ]" 
                option-value="id" option-label="name" 
                label="Group By"
            />
        </x-slot:middle>
    </x-header>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
        <x-card title="Top 10 Issues - {{ $groupBy == 'LineName' ? 'By Line' : 'By Machine' }}">
            @if(count($chartData) > 0)
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ $groupBy == 'LineName' ? 'Line' : 'Machine' }}</th>
                                <th>Total Issues</th>
                                <th>Total Downtime (Mins)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($chartData as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="font-bold">{{ $row['category'] }}</td>
                                <td>
                                    <x-badge value="{{ $row['total_issues'] }}" class="badge-error" />
                                </td>
                                <td>{{ $row['total_downtime'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-10 text-gray-500">
                    <x-icon name="o-inbox" class="w-12 h-12 mx-auto mb-3 opacity-30" />
                    <p>No downtime data found for the selected month and year.</p>
                </div>
            @endif
        </x-card>

        <x-card title="Downtime Summary">
            @if(count($chartData) > 0)
                <div class="space-y-4">
                    @foreach($chartData as $index => $row)
                        @php
                            // Calculate percentage based on max
                            $max = collect($chartData)->max('total_issues');
                            $percentage = $max > 0 ? ($row['total_issues'] / $max) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium">{{ $row['category'] }}</span>
                                <span>{{ $row['total_issues'] }} issues</span>
                            </div>
                            <progress class="progress progress-error w-full" value="{{ $percentage }}" max="100"></progress>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-gray-500">
                    <p>Select different filters to see insights.</p>
                </div>
            @endif
        </x-card>
    </div>
</div>
