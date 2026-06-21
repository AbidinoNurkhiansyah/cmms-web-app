<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\OneHourOver;

class OneHourOverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OneHourOver::truncate();

        // Copy files to storage
        $sourceDir = public_path('assets/one-hour-over');
        $destDir = storage_path('app/public/one_hour_over');

        if (!File::exists($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        $rsaFile = 'RSA_1772694402_RSA.pdf';
        $rcaFile = 'RCA_1772694402_RCA.pdf';

        if (File::exists("$sourceDir/$rsaFile")) {
            File::copy("$sourceDir/$rsaFile", "$destDir/$rsaFile");
        }
        
        if (File::exists("$sourceDir/$rcaFile")) {
            File::copy("$sourceDir/$rcaFile", "$destDir/$rcaFile");
        }

        $data = [
            ['2025-12-01', 'B', 'BRG DAC', 'OR SUPER FINISH MACHINE 1', 'X AXIS OVERLOAD'],
            ['2025-12-18', 'A', '1ST GRD', 'COOLANT SYSTEM', 'M/C NOT HOME POST'],
            ['2025-12-19', 'B', 'EPS 1', 'MACHINING CENTER OP20.1', 'MOLD ATAS NYANGKUT'],
            ['2025-12-19', 'A', 'EPS 1', 'MACHINING CENTER OP20.1', 'M/C TIDAK BISA MOLD SET'],
            ['2025-12-22', 'A', 'HT', 'HEAT TREATMENT', 'PRINT LABEL ALARM OUT OF PAPER'],
            ['2026-01-13', 'B', 'STC 2', 'BROACHING 2', 'PART TRANSFER ALARM'],
            ['2026-01-13', 'B', '1ST GRD', 'COOLANT SYSTEM', 'PEMBACAAN UNIPULSE ERROR'],
            ['2026-01-14', 'B', 'BRG DAC', 'OR SUPER FINISH MACHINE 1', 'DISPENSER GREASE TIDAK KELUAR'],
            ['2026-01-15', 'A', 'EPS 1', 'MACHINING CENTER OP20.1', 'ALARM X AXIS ERROR'],
            ['2026-01-19', 'A', 'EPS 1', 'MACHINING CENTER OP20.1', 'BATTERY CNC LOW, MC TIDAK BISA MASTER ON'],
        ];

        foreach ($data as $item) {
            OneHourOver::create([
                'date' => $item[0],
                'group_name' => $item[1],
                'line' => $item[2],
                'machine' => $item[3],
                'problem' => $item[4],
                'file_rsa' => "one_hour_over/$rsaFile",
                'file_rca' => "one_hour_over/$rcaFile",
            ]);
        }
    }
}
