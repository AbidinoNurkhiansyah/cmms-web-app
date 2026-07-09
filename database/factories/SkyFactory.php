<?php

namespace Database\Factories;

use App\Models\Sky;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkyFactory extends Factory
{
    protected $model = Sky::class;

    public function definition(): array
    {
        // Ambil user yang ada jid_no nya, jika tidak ada, defaultkan ke null atau ambil sembarang
        $user = User::whereNotNull('jid_no')->inRandomOrder()->first();

        return [
            'date' => $this->faker->dateTimeBetween('-2 months', 'now'),
            'userId' => $user ? $user->jid_no : $this->faker->numerify('JID-#####'),
            'lokasi' => $this->faker->randomElement(['Area Produksi A', 'Gudang Sparepart', 'Ruang Server', 'Workshop Maintenance', 'Area Compressor', 'Jalur Conveyor B']),
            'bahaya' => $this->faker->sentence(),
            'resiko' => $this->faker->randomElement(['Tersandung', 'Tersetrum', 'Terjepit', 'Terpeleset', 'Menghirup gas beracun', 'Kejatuhan benda berat']),
            'countermeasure' => $this->faker->sentence(),
            'img' => null, // Biarkan null untuk dummy
        ];
    }
}
