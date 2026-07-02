<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TpmChecksheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mesin BT30 (Limit Clamp Arbor = 2kN)
        $bt30Machine = '24OMID022';
        // Mesin BT40 (Limit Clamp Arbor = 5kN)
        $bt40Machine = '11OMID001';
        
        $year = 2026;
        $types = ['GATA-GATA', 'CLAMP ARBOR', 'RUN OUT'];
        $machines = [$bt30Machine, $bt40Machine];

        foreach ($machines as $machine) {
            foreach ($types as $type) {
                // Buat 12 bulan
                for ($month = 1; $month <= 12; $month++) {
                    $date = \Carbon\Carbon::create($year, $month, 1)->format('Y-m-d');
                    
                    // Kita buat seolah-olah data bulan 1 sampai 6 sudah diisi dengan tren memburuk,
                    // bulan 7 sampai 12 kosong (belum terjadi).
                    $isFilled = $month <= 6;
                    
                    $gataMm = null;
                    $clampKn = null;
                    $runOutKelurusan = null;
                    $runOutPutaran = null;
                    $remark = null;
                    
                    if ($isFilled) {
                        if ($type === 'GATA-GATA') {
                            // Gata-gata trennya naik dari 0.5 menuju 2.5 (Standard 2)
                            $gataMm = 0.5 + ($month * 0.3); // bln 6 = 2.3 (over limit)
                            if ($gataMm > 2) $remark = "Over limit, perlu overhaul spindle.";
                        } elseif ($type === 'CLAMP ARBOR') {
                            // Clamp Arbor trennya turun
                            if ($machine === $bt30Machine) {
                                // Limit 2kN
                                $clampKn = 4.0 - ($month * 0.4); // bln 6 = 1.6 (under limit)
                                if ($clampKn < 2) $remark = "Daya cengkram sangat lemah.";
                            } else {
                                // Limit 5kN
                                $clampKn = 7.5 - ($month * 0.5); // bln 6 = 4.5 (under limit)
                                if ($clampKn < 5) $remark = "Perlu pengecekan hidrolik arbor.";
                            }
                        } elseif ($type === 'RUN OUT') {
                            // Kelurusan limit 10, Putaran limit 5. Trennya memburuk (naik)
                            $runOutKelurusan = 3.0 + ($month * 1.5); // bln 6 = 12.0 (over limit)
                            $runOutPutaran = 1.0 + ($month * 0.8);   // bln 6 = 5.8 (over limit)
                            
                            if ($runOutKelurusan > 10 || $runOutPutaran > 5) {
                                $remark = "Penyimpangan melebihi batas.";
                            }
                        }
                        
                        if (!$remark) $remark = "Kondisi normal.";
                    }

                    \App\Models\TpmChecksheet::updateOrCreate(
                        [
                            'type' => $type,
                            'machineNo' => $machine,
                            'checked_date' => $date,
                        ],
                        [
                            'pic' => $isFilled ? 'Dummy PIC' : null,
                            'remark' => $remark,
                            'gata_mm' => $gataMm,
                            'clamp_kN' => $clampKn,
                            'run_out_kelurusan' => $runOutKelurusan,
                            'run_out_putaran' => $runOutPutaran,
                        ]
                    );
                }
            }
        }
    }
}
