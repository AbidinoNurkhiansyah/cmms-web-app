<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sqlPath = base_path('master_asset.sql');
        if (file_exists($sqlPath)) {
            $sql = file_get_contents($sqlPath);
            preg_match_all("/\(([^)]+)\)/", $sql, $matches);
            
            $insertData = [];
            foreach ($matches[1] as $match) {
                // Fix potential issues with trailing spaces before passing to str_getcsv
                $match = trim($match);
                if (empty($match) || str_contains($match, '`')) continue; // Skip schema definition lines
                
                $row = str_getcsv($match, ',', "'");
                
                if (count($row) >= 7) {
                    $insertData[] = [
                        'asset_no' => trim($row[0]),
                        'line_name' => trim($row[1]),
                        'machine_name' => trim($row[2]),
                        'maker' => trim($row[3]) === 'NULL' ? null : trim($row[3]),
                        'manufacture_year' => trim($row[4]) === 'NULL' ? null : (int) trim($row[4]),
                        'machine_rank' => trim($row[6]) === 'NULL' ? null : trim($row[6]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            
            foreach (array_chunk($insertData, 500) as $chunk) {
                DB::table('assets')->insertOrIgnore($chunk);
            }
        }
    }
}
