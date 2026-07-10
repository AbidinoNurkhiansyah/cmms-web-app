<?php

namespace App\Services;

use App\Repositories\Contracts\AssetRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AssetService
{
    public function __construct(
        private readonly AssetRepositoryInterface $assetRepository
    ) {}

    public function getPaginatedAssets(int $perPage = 10, string $search = '')
    {
        return $this->assetRepository->getAllPaginated($perPage, $search);
    }

    public function getAssetById(int $id)
    {
        return $this->assetRepository->findById($id);
    }

    public function getDistinctLines(): array
    {
        return DB::table('assets')->distinct()->orderBy('line_name')->pluck('line_name')->filter()->toArray();
    }

    public function createAsset(array $data, $photo = null): mixed
    {
        if ($photo) {
            $data['machine_photo'] = $photo->store('assets/photos', 'public');
        }
        return $this->assetRepository->create($data);
    }

    public function updateAsset(int $id, array $data, $photo = null): mixed
    {
        if ($photo) {
            $data['machine_photo'] = $photo->store('assets/photos', 'public');
        }
        return $this->assetRepository->update($id, $data);
    }

    public function deleteAsset(int $id): mixed
    {
        return $this->assetRepository->delete($id);
    }

    public function getAssetStats(string $assetNo, string $machineName): array
    {
        return [
            'tpm' => DB::table('cmms_tpm_checksheet')->where('machineNo', 'LIKE', "%{$assetNo}%")->count(),
            'problem' => DB::table('carty')->where('MachineNo', 'LIKE', "%{$assetNo}%")->count(),
            'overhaul' => DB::table('cmms_oh_web')->where('MachineNo', 'LIKE', "%{$assetNo}%")->count(),
            'work_order' => DB::table('cmms_work_order_request')->where('MachineNo', 'LIKE', "%{$assetNo}%")->count(),
            'one_hour_over' => DB::table('one_hour_over')->where('machine', $machineName)->count(),
        ];
    }

    public function getSparePartsChartData(string $assetNo): array
    {
        $stats = DB::table('machine_spare_parts')
            ->join('spare_parts', 'machine_spare_parts.spare_part_id', '=', 'spare_parts.id')
            ->selectRaw("spare_parts.`group` as Rangking, 
                         COUNT(CASE WHEN spare_parts.last_stock > 0 THEN 1 END) as tersedia,
                         COUNT(CASE WHEN spare_parts.last_stock <= 0 OR spare_parts.last_stock IS NULL THEN 1 END) as tidak_tersedia")
            ->where('machine_spare_parts.asset_no', 'LIKE', "%{$assetNo}%")
            ->groupBy('spare_parts.group')
            ->get();

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($stats as $stat) {
            $labels[] = $stat->Rangking . ' - Tersedia';
            $data[] = (int)$stat->tersedia;
            $colors[] = '#10b981'; // success

            $labels[] = $stat->Rangking . ' - Tidak Tersedia';
            $data[] = (int)$stat->tidak_tersedia;
            $colors[] = '#ef4444'; // error
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ];
    }

    public function getTrendData(string $assetNo, int $year): array
    {
        $labels = [];
        $problemData = [];
        $repairData = [];
        $stopLineData = [];
        $months = [];

        for ($i = 4; $i <= 12; $i++) {
            $months[] = ['month' => $i, 'year' => $year];
        }
        for ($i = 1; $i <= 3; $i++) {
            $months[] = ['month' => $i, 'year' => $year + 1];
        }

        foreach ($months as $m) {
            $m_num = $m['month'];
            $y_num = $m['year'];

            $monthName = date('M', mktime(0, 0, 0, $m_num, 10));
            $labels[] = $monthName . ' ' . substr($y_num, 2);

            $problemData[] = DB::table('carty')
                ->whereMonth('date', $m_num)
                ->whereYear('date', $y_num)
                ->where('MachineNo', 'LIKE', "%{$assetNo}%")
                ->count();

            $timeData = DB::table('carty')
                ->selectRaw("SUM(worktime) as sumRepair, SUM(DownTime) as sumStopLine")
                ->whereMonth('date', $m_num)
                ->whereYear('date', $y_num)
                ->where('MachineNo', 'LIKE', "%{$assetNo}%")
                ->first();

            $repairData[] = (int)($timeData->sumRepair ?? 0);
            $stopLineData[] = (int)($timeData->sumStopLine ?? 0);
        }

        return [
            'labels' => $labels,
            'problems' => $problemData,
            'repairTime' => $repairData,
            'stopLineTime' => $stopLineData,
        ];
    }

    public function getAvailableTrendYears(string $assetNo): array
    {
        $defaultYear = (int)date('n') >= 4 ? (int)date('Y') : (int)date('Y') - 1;

        $years = DB::table('carty')
            ->selectRaw("DISTINCT CASE WHEN MONTH(date) >= 4 THEN YEAR(date) ELSE YEAR(date) - 1 END AS period_year")
            ->where('MachineNo', 'LIKE', "%{$assetNo}%")
            ->whereNotNull('date')
            ->orderBy('period_year', 'DESC')
            ->pluck('period_year')
            ->map(fn($y) => (int)$y)
            ->filter(fn($y) => $y <= $defaultYear)
            ->toArray();

        if (empty($years) || !in_array($defaultYear, $years)) {
            $years[] = $defaultYear;
        }

        rsort($years);
        return array_unique($years);
    }
}
