<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Carty;
use Illuminate\Support\Carbon;

class CartySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample data taking references from cmms.sql legacy structure
        $data = [
            [
                'Date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'groupline' => 'MTC B',
                'LineName' => 'BRG',
                'MachineNo' => '11HSID002',
                'MachineName' => 'OR R/W SUPER FINISH MACHINE',
                'DownTime' => 15,
                'Problem' => 'Alarm coolant low level',
                'Action' => 'Kuras coolant SF 36 & ganti filter 1 micron',
                'Status' => 'Close',
                'Shift' => 1,
                'PIC' => 'Andy',
            ],
            [
                'Date' => Carbon::now()->subDays(4)->format('Y-m-d'),
                'groupline' => 'MTC A',
                'LineName' => 'BRG',
                'MachineNo' => '11FBID003',
                'MachineName' => 'HEAT TREATMENT',
                'DownTime' => 90,
                'Problem' => 'Counter tidak bisa menghitung',
                'Action' => 'Cek PLC, tray di posisi 2 dua tidak rev detect, tray di posisi 1 di ON kan',
                'Status' => 'Close',
                'Shift' => 1,
                'PIC' => 'Budi',
            ],
            [
                'Date' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'groupline' => 'MTC A',
                'LineName' => 'BRG',
                'MachineNo' => '12KCID010',
                'MachineName' => 'PART WASHING MACHINE',
                'DownTime' => 10,
                'Problem' => 'Cylinder pneumatic transfer up / down tidak mau turun',
                'Action' => 'Setting sensor pneumatik',
                'Status' => 'Open',
                'Shift' => 2,
                'PIC' => 'Candra',
            ],
            [
                'Date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'groupline' => 'MTC A',
                'LineName' => 'STC',
                'MachineNo' => '11ORID002',
                'MachineName' => 'BROACHING 2',
                'DownTime' => 210,
                'Problem' => 'Part shift left-right abnormal , hand clamp-unclamp turn abnormal',
                'Action' => 'Ganti modular cylinder part enter ADV-Ret , setting antara part shift Right-left dengan turn 1',
                'Status' => 'Close',
                'Shift' => 3,
                'PIC' => 'Deni',
            ],
            [
                'Date' => Carbon::now()->subDays(1)->format('Y-m-d'),
                'groupline' => 'MTC A',
                'LineName' => 'BRG',
                'MachineNo' => '11FBID002',
                'MachineName' => 'HEAT TREATMENT',
                'DownTime' => 15,
                'Problem' => 'Filter panel heatreatment kotor',
                'Action' => 'Penggantian filter Heat treatment panel',
                'Status' => 'Close',
                'Shift' => 1,
                'PIC' => 'Eko',
            ],
            [
                'Date' => Carbon::now()->format('Y-m-d'),
                'groupline' => 'MTC B',
                'LineName' => 'EPS',
                'MachineNo' => '11OMID003',
                'MachineName' => 'MACHINING CENTER OP20.1',
                'DownTime' => 60,
                'Problem' => 'Tool nabrak pin clamper',
                'Action' => 'Buat pin clamper baru dan kalibrasi',
                'Status' => 'Open',
                'Shift' => 2,
                'PIC' => 'Fajar',
            ],
            [
                'Date' => Carbon::now()->format('Y-m-d'),
                'groupline' => 'MTC B',
                'LineName' => 'BRG',
                'MachineNo' => '11LNID012',
                'MachineName' => 'OR LATHE MACHINE',
                'DownTime' => 5,
                'Problem' => 'Coolant pump motor circuit breaker trip',
                'Action' => 'Reset NFB (di ON kan kembali) dan cek overload',
                'Status' => 'Close',
                'Shift' => 1,
                'PIC' => 'Galih',
            ]
        ];

        // Define the start and end period (Jan 1st to June 30th of the current year)
        $start = Carbon::now()->startOfYear();
        $end = Carbon::now()->month(6)->endOfMonth();

        $equipmentList = ['Motor', 'Sensor', 'Pneumatic', 'Hydraulic', 'Conveyor', 'PLC', 'Panel'];
        $classificationList = ['Class A', 'Class B', 'Class C'];
        $problemTypes = ['Electrical', 'Mechanical', 'Other'];
        $spareparts = ['O-Ring', 'Bearing', 'Filter', 'Cylinder', 'Relay', 'Contactor', 'Switch'];

        // Seed 200 records to deeply test pagination and chart simulations
        for ($i = 0; $i < 200; $i++) {
            // Pick a random template
            $template = $data[array_rand($data)];
            
            // Randomize the date between Jan and June
            $randomTimestamp = rand($start->timestamp, $end->timestamp);
            $template['Date'] = Carbon::createFromTimestamp($randomTimestamp)->format('Y-m-d');
            
            // Slightly randomize the DownTime and Shift
            $template['DownTime'] = rand(5, 120);
            $template['Shift'] = rand(1, 3);

            // Legacy fields dummy data
            $template['equipment'] = $equipmentList[array_rand($equipmentList)];
            $template['classification'] = $classificationList[array_rand($classificationList)];
            $template['typeofproblem'] = $problemTypes[array_rand($problemTypes)];
            
            if (rand(0, 1) === 1) {
                $template['sparepartName'] = $spareparts[array_rand($spareparts)];
                $template['sparepartType'] = 'Type ' . rand(1, 10);
            }

            $template['Cause'] = 'Simulated cause for problem: ' . $template['Problem'];
            $template['stopline'] = $template['DownTime']; // generally equal or less
            $template['worktime'] = rand(30, 180);
            
            Carty::create($template);
        }
    }
}
