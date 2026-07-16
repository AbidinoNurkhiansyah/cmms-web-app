<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeepCleaning;
use App\Models\Asset;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DeepCleaningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ambil data asset yang valid untuk digunakan sebagai referensi Line dan Machine
        $assets = Asset::whereNotNull('line_name')
            ->whereNotNull('asset_no')
            ->whereNotNull('machine_name')
            ->get();

        if ($assets->isEmpty()) {
            $this->command->warn('Tabel assets masih kosong atau tidak memiliki data line_name/machine_name. Tidak dapat men-generate data Deep Cleaning yang valid.');
            return;
        }

        $statuses = ['Scheduled', 'In Progress', 'Done'];
        $dummyPics = [
            ['Budi', 'Andi'],
            ['Joko', 'Tono'],
            ['Siti', 'Ayu', 'Rini'],
            ['Doni'],
            ['Reza', 'Fajar']
        ];
        
        $itemChecks = [
            'Pembersihan filter udara mesin utama.',
            'Pengecekan dan pembersihan jalur oli pelumas.',
            'Penggantian seal karet dan deep cleaning area putaran rotor.',
            'Pembersihan sensor optic pada conveyor.',
            'Scrubbing area panel kontrol dan vacuum debu.'
        ];

        // Buat 100 record dummy untuk bulan Feb, Mar, Apr, May
        for ($i = 0; $i < 100; $i++) {
            $asset = $assets->random();
            $status = $faker->randomElement($statuses);

            // Generate date between Feb 1 and May 31
            $start = Carbon::create(date('Y'), 2, 1);
            $end = Carbon::create(date('Y'), 5, 31);
            $randomDate = Carbon::createFromTimestamp(rand($start->timestamp, $end->timestamp));

            $deepCleaning = DeepCleaning::create([
                'Date'          => $randomDate,
                'LineName'      => $asset->line_name,
                'MachineNo'     => $asset->asset_no,
                'MachineName'   => $asset->machine_name,
                'pics'          => $faker->randomElement($dummyPics),
                'description'   => $faker->randomElement(['TPM', 'Preventive', 'Repair']),
            ]);

            $itemcheck = $faker->randomElement($itemChecks);

            $deepCleaning->items()->create([
                'itemcheck'     => $itemcheck,
                'description'   => $faker->sentence(),
                'action'        => $status === 'Done' ? 'Selesai dilakukan deep cleaning menyeluruh.' : 'Tindakan lanjutan sedang dipersiapkan.',
                'status'        => $status,
                'before_photo'  => null,
                'after_photo'   => null,
            ]);

            if ($faker->boolean(30)) {
                $deepCleaning->spareparts()->create([
                    'sparepart_id'  => 'SP-00' . rand(1, 9),
                    'qty' => rand(1, 5),
                    'itemcheck'     => $itemcheck,
                ]);
            }
        }

        $this->command->info('20 data dummy Deep Cleaning berhasil di-generate!');
    }
}
