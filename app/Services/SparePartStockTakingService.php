<?php

namespace App\Services;

use App\Models\SparePartStockTaking;
use App\Models\SparePart;
use Illuminate\Support\Facades\DB;

class SparePartStockTakingService
{
    /**
     * Get aggregated daily stock taking data.
     */
    public function getAggregatedData($perPage = 10, $search = '')
    {
        $query = SparePartStockTaking::selectRaw('
                date_stock as date,
                COUNT(spare_part_id) as total_stock,
                SUM(CASE WHEN last_stock = check_stock THEN 1 ELSE 0 END) as ok_count,
                SUM(CASE WHEN last_stock != check_stock THEN 1 ELSE 0 END) as err_count
            ')
            ->groupBy('date_stock')
            ->orderByDesc('date_stock');

        // Note: Usually date searches are hard, but we can do a simple string match
        if ($search) {
            $query->where('date_stock', 'like', "%{$search}%");
        }

        return $query->paginate($perPage);
    }

    /**
     * Get detail records for a specific date and status.
     * $status can be 'all', 'ok', 'err', 'not_found'
     */
    public function getDetailData(string $date, string $status = 'all', $perPage = 15, $search = '')
    {
        // For 'not_found' (legacy xFound), it means parts that were NOT checked today.
        // We need to fetch from SparePart where id NOT IN (select spare_part_id from stock_takings where date = X)
        if ($status === 'not_found') {
            $query = SparePart::whereNotIn('id', function($q) use ($date) {
                $q->select('spare_part_id')
                  ->from('spare_part_stock_takings')
                  ->whereDate('date_stock', $date);
            });
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('part_number', 'like', "%{$search}%")
                      ->orWhere('part_name', 'like', "%{$search}%");
                });
            }
            
            return $query->paginate($perPage);
        }

        // For 'all', 'ok', 'err'
        $query = SparePartStockTaking::with('sparePart')
            ->whereDate('date_stock', $date);

        if ($status === 'ok') {
            $query->whereColumn('last_stock', '=', 'check_stock');
        } elseif ($status === 'err') {
            $query->whereColumn('last_stock', '!=', 'check_stock');
        }

        if ($search) {
            $query->whereHas('sparePart', function($q) use ($search) {
                $q->where('part_number', 'like', "%{$search}%")
                  ->orWhere('part_name', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Store a new stock taking record.
     */
    public function store(array $data)
    {
        return SparePartStockTaking::create($data);
    }

    /**
     * Get summary data for a specific date (Total Prices, Gap Over, Gap Minus)
     */
    public function getSummaryData(string $date)
    {
        $records = SparePartStockTaking::with('sparePart')
            ->whereDate('date_stock', $date)
            ->get();

        $gapOver = 0;
        $gapMinus = 0;
        $totalPrices = 0;

        foreach ($records as $record) {
            $price = $record->sparePart->price_idr ?? 0;
            $totalPrices += $record->last_stock * $price;

            $diff = $record->check_stock - $record->last_stock;
            if ($diff > 0) {
                $gapOver += ($diff * $price);
            } elseif ($diff < 0) {
                $gapMinus += abs($diff * $price);
            }
        }

        $gapTotal = $gapOver + $gapMinus;
        $prosen = $totalPrices > 0 ? ($gapTotal / $totalPrices) * 100 : 0;

        return [
            'gap_over' => $gapOver,
            'gap_minus' => $gapMinus,
            'gap_total' => $gapTotal,
            'total_prices' => $totalPrices,
            'prosen' => $prosen,
        ];
    }
}
