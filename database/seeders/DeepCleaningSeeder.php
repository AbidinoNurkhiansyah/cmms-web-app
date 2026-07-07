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

        // Buat 20 record dummy
        for ($i = 0; $i < 20; $i++) {
            $asset = $assets->random();
            $status = $faker->randomElement($statuses);

            $deepCleaning = DeepCleaning::create([
                'Date'          => Carbon::today()->subDays(rand(0, 30)),
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
