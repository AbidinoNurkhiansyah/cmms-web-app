<?php

namespace App\Services;

use App\Models\Andon;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AndonService
{
    public function getPaginated(int $perPage = 15, string $search = '', string $status = '')
    {
        return Andon::query()
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('line_name', 'like', "%{$search}%")
                  ->orWhere('machine', 'like', "%{$search}%")
                  ->orWhere('stop_info', 'like', "%{$search}%");
            }))
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('date_in')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function getOutstandingToday()
    {
        return Andon::query()
            ->whereIn('status', ['CALL', 'REPAIR'])
            ->whereDate('date_in', Carbon::today())
            ->orderBy('id', 'asc')
            ->get();
    }

    public function getDashboardStats(): array
    {
        $today = Carbon::today();
        
        // 1. Today Chart
        $todayStats = Andon::query()
            ->select('line_name', DB::raw('count(*) as total'))
            ->whereDate('date_in', $today)
            ->groupBy('line_name')
            ->orderByDesc('total')
            ->get();

        // 2. Top 10 Month
        $currentMonth = $today->format('Y-m');
        $top10MonthStats = Andon::query()
            ->select('line_name', DB::raw('count(*) as total'))
            ->whereRaw("DATE_FORMAT(date_in, '%Y-%m') = ?", [$currentMonth])
            ->groupBy('line_name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // 3. Daily Month Chart
        $daysInMonth = $today->daysInMonth;
        $dailyData = [];
        $dailyLabels = [];
        
        $dailyStats = Andon::query()
            ->select(DB::raw('DAY(date_in) as day'), DB::raw('count(*) as total'))
            ->whereRaw("DATE_FORMAT(date_in, '%Y-%m') = ?", [$currentMonth])
            ->groupBy(DB::raw('DAY(date_in)'))
            ->pluck('total', 'day')->toArray();

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dailyLabels[] = $day . ' ' . $today->format('M');
            $dailyData[] = $dailyStats[$day] ?? 0;
        }

        // 4. 6 Months Monthly Chart
        $monthlyLabels = [];
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = $today->copy()->subMonths($i);
            $monthFormat = $monthDate->format('Y-m');
            $monthlyLabels[] = $monthDate->format('M y');
            
            $count = Andon::query()
                ->whereRaw("DATE_FORMAT(date_in, '%Y-%m') = ?", [$monthFormat])
                ->count();
            $monthlyData[] = $count;
        }

        // 5. 6 Months Line Chart
        $startDate = $today->copy()->subMonths(5)->startOfMonth();
        $endDate = $today->copy()->endOfMonth();
        $lineStats = Andon::query()
            ->select('line_name', DB::raw('count(*) as total'))
            ->whereBetween('date_in', [$startDate, $endDate])
            ->groupBy('line_name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'today' => [
                'labels' => $todayStats->pluck('line_name')->toArray(),
                'data' => $todayStats->pluck('total')->toArray(),
            ],
            'top10Month' => [
                'labels' => $top10MonthStats->pluck('line_name')->toArray(),
                'data' => $top10MonthStats->pluck('total')->toArray(),
            ],
            'daily' => [
                'labels' => $dailyLabels,
                'data' => $dailyData,
            ],
            'monthly' => [
                'labels' => $monthlyLabels,
                'data' => $monthlyData,
            ],
            'line6Months' => [
                'labels' => $lineStats->pluck('line_name')->toArray(),
                'data' => $lineStats->pluck('total')->toArray(),
            ]
        ];
    }

    public function create(array $data)
    {
        $data['status'] = $data['status'] ?? 'CALL';
        return Andon::create($data);
    }

    public function update(int $id, array $data)
    {
        $record = Andon::findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id): bool
    {
        return Andon::findOrFail($id)->delete();
    }
}
