<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Overtime;
use Carbon\Carbon;

class OvertimeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan data lembur yang lama jika ada
        Overtime::truncate();

        // Ambil semua user yang memiliki tim MTC, PE, atau ME
        $users = User::whereIn('team', ['MTC', 'PE', 'ME'])->get();

        // Target periode: 16 bulan lalu hingga 15 bulan ini (periode aktif default aplikasi)
        $today = Carbon::today();
        if ($today->day >= 16) {
            $start = Carbon::create($today->year, $today->month, 16);
        } else {
            $start = Carbon::create($today->year, $today->month, 16)->subMonth();
        }

        // Generate data lembur acak untuk masing-masing karyawan
        foreach ($users as $user) {
            // Generate untuk 3 periode (Bulan ini, bulan lalu, 2 bulan lalu)
            for ($monthsBack = 0; $monthsBack < 3; $monthsBack++) {
                $periodStart = $start->copy()->subMonths($monthsBack);
                
                // Setiap karyawan mendapat 2 hingga 5 entri lembur dalam periode ini
                $entries = rand(2, 5);
                for ($i = 0; $i < $entries; $i++) {
                    $randomDayOffset = rand(0, 25);
                    $date = $periodStart->copy()->addDays($randomDayOffset);

                    // Jam 1 secara acak antara 2 sampai 6 jam (sebagai contoh)
                    $hours1 = rand(20, 60) / 10; 
                    
                    // Kalkulasi (Jam 2) biasanya lebih besar, misal dikali 1.5 atau 2
                    $hours2 = $hours1 * rand(15, 25) / 10; 

                    Overtime::create([
                        'user_id' => $user->id,
                        'date' => $date->format('Y-m-d'),
                        'hours_1' => round($hours1, 1),
                        'hours_2' => round($hours2, 1),
                    ]);
                }
            }
        }
    }
}
