<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'superadmin@digimon.com'],
            [
                'name'     => 'Super Administrator',
                'username' => 'superadmin',
                'password' => bcrypt('password123'), // Password default
                'jid_no'   => 'JID99999',
                'position' => 'Manager',
                'team'     => 'MTC',
                'is_admin' => true,
                'status'   => 'Active',
            ]
        );

        // Dummy Asset
        \App\Models\Asset::updateOrCreate(
            ['asset_no' => '13XQID014'],
            [
                'line_name' => '1ST GRD',
                'machine_name' => 'COOLANT SYSTEM',
                'maker' => 'CNK.CO.LTD',
                'manufacture_year' => 2013,
                'classification' => 'TANK',
                'machine_rank' => 'D',
            ]
        );

        \App\Models\Asset::updateOrCreate(
            ['asset_no' => '11GEID001'],
            [
                'line_name' => '1ST GRD',
                'machine_name' => 'WIDTH GRINDING SBB',
                'maker' => 'KOYO',
                'manufacture_year' => 2011,
                'classification' => 'END FACE GRINDING M/C',
                'machine_rank' => 'B',
            ]
        );

        // Dummy Spare Part
        \App\Models\SparePart::updateOrCreate(
            ['part_number' => 'BRG-001'],
            [
                'group' => 'BEARING',
                'group_id' => 1,
                'use_qty' => 5,
                'price_idr' => 250000,
            ]
        );
    }
}
