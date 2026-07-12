<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\CsItem;
use App\Models\CsDocno;

class ChecksheetMasterService
{
    public function getLines(): \Illuminate\Support\Collection
    {
        return Asset::select('line_name')
            ->distinct()
            ->whereNotNull('line_name')
            ->orderBy('line_name')
            ->get();
    }

    public function getMachines(string $lineName): \Illuminate\Support\Collection
    {
        return Asset::select('asset_no', 'machine_name', 'machine_photo', 'machine_rank')
            ->where('line_name', $lineName)
            ->orderBy('machine_name')
            ->get();
    }

    public function getSelectedAsset(string $assetNo): ?Asset
    {
        return Asset::where('asset_no', $assetNo)
            ->select('asset_no', 'machine_name', 'line_name', 'machine_rank', 'machine_photo', 'maker', 'manufacture_year')
            ->first();
    }

    public function getItems(string $assetNo): \Illuminate\Support\Collection
    {
        return CsItem::where('asset_no', $assetNo)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function getStats(\Illuminate\Support\Collection $items): array
    {
        return [
            'total'   => $items->count(),
            'active'  => $items->where('is_active', true)->count(),
            'daily'   => $items->where('periode', 'D')->count(),
            'weekly'  => $items->where('periode', 'W')->count(),
            'monthly' => $items->where('periode', 'M')->count(),
        ];
    }

    public function getCurrentDoc(string $assetNo): ?CsDocno
    {
        return CsDocno::where('asset_no', $assetNo)
            ->orderBy('tanggal_revisi', 'desc')
            ->orderBy('id', 'desc')
            ->first();
    }

    public function getHistoryDocs(string $assetNo): \Illuminate\Support\Collection
    {
        return CsDocno::where('asset_no', $assetNo)
            ->orderBy('tanggal_revisi', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function periodeOptions(): array
    {
        return [
            ['id' => 'D', 'name' => 'D — Daily (Harian)'],
            ['id' => 'W', 'name' => 'W — Weekly (Mingguan)'],
            ['id' => 'M', 'name' => 'M — Monthly (Bulanan)'],
        ];
    }
}
