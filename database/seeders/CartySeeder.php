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
                'pics' => ['Andy'],
                // These are injected dynamically in the loop, but added here for your reference:
                'sparepartName' => 'BEARING 6205',
                'sparepartQty'  => 2,
                'filebefore1'   => 'images/cardty/start.png',
                'filebefore2'   => 'images/cardty/stop-line.png',
                'fileafter1'    => 'images/cardty/repair.png',
                'fileafter2'    => 'images/cardty/finish.png',
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
                'pics' => ['Budi'],
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
                'pics' => ['Candra'],
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
                'pics' => ['Deni'],
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
                'pics' => ['Eko'],
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
                'pics' => ['Fajar'],
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
                'pics' => ['Galih'],
            ]
        ];

        // Define the start and end period (Jan 1st to June 30th of the current year)
        $start = Carbon::now()->startOfYear();
        $end = Carbon::now()->month(6)->endOfMonth();

        $problemTypes = ['Electrical', 'Mechanical', 'Other'];
        $sparepartNames = \App\Models\SparePart::query()->whereNotNull('part_name')->pluck('part_name')->toArray();
        if (empty($sparepartNames)) {
            $sparepartNames = ['BEARING 6205', 'O-RING 12'];
        }
        
        $userNames = \App\Models\User::query()->where('role', \App\Models\User::ROLE_TECHNICIAN)->pluck('name')->toArray();
        if (empty($userNames)) {
            $userNames = ['Andy', 'Budi', 'Candra', 'Deni', 'Eko', 'Fajar', 'Galih'];
        }

        $assetIds = \App\Models\Asset::pluck('id')->toArray();
        $sparePartIds = \App\Models\SparePart::pluck('id')->toArray();

        // Seed 200 records to deeply test pagination and chart simulations
        for ($i = 0; $i < 200; $i++) {
            // Pick a random template
            $template = $data[array_rand($data)];
            
            // Randomize the date between Jan and June
            $randomTimestamp = rand($start->timestamp, $end->timestamp);
            $template['Date'] = Carbon::createFromTimestamp($randomTimestamp)->format('Y-m-d');
            
            // Time fields
            $startHour = rand(8, 16);
            $startMin = rand(0, 59);
            $downTime = rand(5, 120);
            
            $startTime = Carbon::createFromTime($startHour, $startMin);
            $finishTime = (clone $startTime)->addMinutes($downTime);

            $template['start_time'] = $startTime->format('H:i');
            $template['finish_time'] = $finishTime->format('H:i');
            $template['DownTime'] = $downTime;
            $template['worktime'] = $downTime + rand(10, 30);
            
            $template['Shift'] = rand(1, 3);
            $template['Status'] = rand(0, 1) ? 'Temporary' : 'Permanent';

            // Use actual Asset to ensure Edit form works correctly
            if (!empty($assetIds)) {
                $assetId = $assetIds[array_rand($assetIds)];
                $asset = \App\Models\Asset::find($assetId);
                if ($asset) {
                    $template['LineName'] = $asset->line_name;
                    $template['MachineNo'] = $asset->asset_no;
                    $template['MachineName'] = $asset->machine_name;
                }
            }

            // Random PICs from actual users
            $picsCount = rand(1, 3);
            $template['pics'] = (array) array_rand(array_flip($userNames), min($picsCount, count($userNames)));

            // Legacy fields dummy data
            $template['typeofproblem'] = $problemTypes[array_rand($problemTypes)];
            
            if (rand(0, 1) === 1) {
                $template['sparepartName'] = $sparepartNames[array_rand($sparepartNames)];
                $template['sparepartQty'] = rand(1, 5);
            }

            $template['Cause'] = 'Simulated cause for problem: ' . $template['Problem'];
            
            // Add images
            $template['filebefore1'] = 'images/cardty/start.png';
            $template['filebefore2'] = 'images/cardty/stop-line.png';
            $template['fileafter1'] = 'images/cardty/repair.png';
            $template['fileafter2'] = 'images/cardty/finish.png';
            
            $carty = Carty::create($template);

            // Seed Pivot Table (carty_spare_part)
            if (!empty($sparePartIds) && rand(0, 1) === 1) {
                $partsCount = rand(1, 3);
                $partsToAttach = (array) array_rand(array_flip($sparePartIds), min($partsCount, count($sparePartIds)));
                
                $syncData = [];
                foreach ($partsToAttach as $spId) {
                    $syncData[$spId] = ['qty' => rand(1, 5)];
                }
                $carty->spareParts()->sync($syncData);
            }
        }
    }
}
