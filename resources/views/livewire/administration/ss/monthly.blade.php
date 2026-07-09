<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use App\Models\SuggestionSystem;
use Illuminate\Support\Facades\DB;

new #[Title('Monthly Suggestion System')] class extends Component {
    public int $tahun;

    public function mount()
    {
        $this->tahun = request()->query('tahun', date('Y'));
    }

    #[Computed]
    public function yearOptions()
    {
        $currentYear = date('Y');
        $startYear = $currentYear - 7;
        $endYear = $currentYear + 7;

        $options = [];
        for ($y = $startYear; $y <= $endYear; $y++) {
            $options[] = ['id' => $y, 'name' => $y];
        }
        return $options;
    }

    #[Computed]
    public function monthlyData()
    {
        $records = SuggestionSystem::with('user')
            ->whereYear('tgl', $this->tahun)
            ->get();

        $monthNames = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nop',
            12 => 'Des'
        ];

        return $records->groupBy('user_id')->map(function ($items) use ($monthNames) {
            $months = array_fill(1, 12, 0);
            foreach ($items as $item) {
                $month = (int) date('n', strtotime($item->tgl));
                $months[$month]++;
            }

            $result = [
                'name' => $items->first()->user ? $items->first()->user->name : 'Unknown',
                'ttl' => $items->count(),
            ];

            foreach ($monthNames as $idx => $name) {
                $result[$name] = $months[$idx];
            }

            return $result;
        })->sortByDesc('ttl')->values()->toArray();
    }
};
?>

<div>
    @include('livewire.administration.ss.partials.monthly-header')
    @include('livewire.administration.ss.partials.monthly-table')
</div>