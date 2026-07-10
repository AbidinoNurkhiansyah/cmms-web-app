<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Carty;
use App\Models\WorkOrder;
use App\Models\DeepCleaning;
use App\Models\Overhaul;
use App\Models\Sky;
use App\Models\SuggestionSystem;
use Illuminate\Database\Eloquent\Builder;

class UserActivityService
{
    public function getUserActivityStats($jidNo, $userId)
    {
        // 1. Calculate April to April Date Range
        $now = Carbon::now();
        $currentYear = $now->year;
        $currentMonth = $now->month;

        if ($currentMonth < 4) {
            $startDate = Carbon::create($currentYear - 1, 4, 1)->startOfDay();
            $endDate   = Carbon::create($currentYear, 4, 1)->startOfDay();
        } else {
            $startDate = Carbon::create($currentYear, 4, 1)->startOfDay();
            $endDate   = Carbon::create($currentYear + 1, 4, 1)->startOfDay();
        }

        // Search patterns (similar to legacy using jid_no)
        $namaPendek = trim($jidNo);

        // Carty: pics array
        $cartyCount = Carty::whereBetween('Date', [$startDate, $endDate])
            ->whereJsonContains('pics', $namaPendek)
            ->count();

        // Work Order: pic column
        $woCount = WorkOrder::whereBetween('date', [$startDate, $endDate])
            ->where('pic', 'like', "%$namaPendek%")
            ->count();

        // Deep Cleaning: pics array (assuming JSON array of names/jids)
        $tpmCount = DeepCleaning::whereBetween('Date', [$startDate, $endDate])
            ->whereJsonContains('pics', $namaPendek)
            ->count();

        // Overhaul: PIC
        $ohCount = Overhaul::whereBetween('date', [$startDate, $endDate])
            ->where('PIC', 'like', "%$namaPendek%")
            ->count();

        // Sky: userId matches jid_no
        $skyCount = Sky::whereBetween('date', [$startDate, $endDate])
            ->where('userId', $namaPendek)
            ->count();

        // Suggestion System: user_id foreign key
        $ssCount = SuggestionSystem::whereBetween('tgl', [$startDate, $endDate])
            ->where('user_id', $userId)
            ->count();

        return [
            'labels' => ['Carty', 'Work Order', 'Deep Cleaning', 'OH', 'KYT', 'SS'],
            'data'   => [$cartyCount, $woCount, $tpmCount, $ohCount, $skyCount, $ssCount],
            'period' => $startDate->format('M Y') . ' - ' . $endDate->format('M Y')
        ];
    }
}
