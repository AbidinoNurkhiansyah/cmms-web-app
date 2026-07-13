<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Asset;

class LegacyChecksheetItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sqlFile = base_path('cmms.sql');
        
        if (!file_exists($sqlFile)) {
            $this->command->error("File cmms.sql tidak ditemukan di root project!");
            return;
        }

        $this->command->info("Membaca cmms.sql untuk mencari data legacy checksheet items...");

        $handle = fopen($sqlFile, "r");
        
        $inInsert = false;
        $insertQuery = '';
        
        // 1. Buat tabel sementara dengan struktur lama
        DB::statement('DROP TABLE IF EXISTS cmms_cs_items_legacy');
        DB::statement("
            CREATE TABLE cmms_cs_items_legacy (
                id int(11) NOT NULL AUTO_INCREMENT,
                point_check TEXT DEFAULT NULL,
                standard TEXT DEFAULT NULL,
                method TEXT DEFAULT NULL,
                periode varchar(5) DEFAULT NULL,
                assetNo varchar(50) DEFAULT NULL,
                status char(50) DEFAULT 'Aktif',
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $this->command->info("Menyalin data ke tabel sementara...");
        
        // 2. Scan file baris per baris (ramah memori)
        while (($line = fgets($handle)) !== false) {
            // Cari baris INSERT dengan struktur kolom lama
            if (strpos($line, "INSERT INTO `cmms_cs_items` (`id`, `point_check`, `standard`") !== false) {
                $inInsert = true;
                // Ubah nama tabel target ke tabel sementara
                $line = str_replace("INSERT INTO `cmms_cs_items`", "INSERT INTO `cmms_cs_items_legacy`", $line);
            }
            
            if ($inInsert) {
                $insertQuery .= $line;
                
                // Jika baris diakhiri titik koma, maka blok INSERT selesai
                if (str_ends_with(trim($line), ';')) {
                    break;
                }
            }
        }
        
        fclose($handle);

        if (empty($insertQuery)) {
            $this->command->error("Tidak menemukan blok INSERT INTO cmms_cs_items yang sesuai di cmms.sql");
            // Bersihkan tabel sementara jika gagal
            DB::statement('DROP TABLE IF EXISTS cmms_cs_items_legacy');
            return;
        }

        // 3. Eksekusi query bulk insert ke tabel sementara
        DB::unprepared($insertQuery);
        
        $this->command->info("Data legacy berhasil masuk ke temp table. Memulai migrasi ke struktur baru...");
        
        $legacyItems = DB::table('cmms_cs_items_legacy')->get();
        
        // Update the cmms_cs_items table schema to support varchar(255) for method if it was restricted
        DB::statement('ALTER TABLE cmms_cs_items MODIFY COLUMN method VARCHAR(255) NULL');

        // Disable foreign key checks to allow truncation
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        DB::table('cmms_cs_items')->truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
        
        // Ambil master asset untuk mapping machine_name dan line_name
        $assets = Asset::all()->keyBy('asset_no');
        
        $insertData = [];
        $now = now();
        $orderCounter = []; 
        
        foreach ($legacyItems as $item) {
            $assetNo = $item->assetNo;
            $machineName = null;
            $lineName = null;
            
            // Map berdasarkan relasi asset
            if (isset($assets[$assetNo])) {
                $machineName = $assets[$assetNo]->machine_name;
                $lineName = $assets[$assetNo]->line_name;
            }
            
            // Penomoran sort_order dinamis per asset_no
            if (!isset($orderCounter[$assetNo])) {
                $orderCounter[$assetNo] = 1;
            } else {
                $orderCounter[$assetNo]++;
            }
            
            $insertData[] = [
                'asset_no'     => mb_substr($assetNo, 0, 50),
                'machine_name' => $machineName ? mb_substr($machineName, 0, 255) : null,
                'line_name'    => $lineName ? mb_substr($lineName, 0, 100) : null,
                'item_check'   => $item->point_check ? mb_substr($item->point_check, 0, 255) : null,
                'standard'     => $item->standard ? mb_substr($item->standard, 0, 255) : null,
                'method'       => $item->method ? mb_substr($item->method, 0, 255) : null,
                'periode'      => $item->periode ? mb_substr($item->periode, 0, 5) : null,
                'is_active'    => strtolower(trim($item->status ?? '')) === 'aktif' ? 1 : 0,
                'sort_order'   => $orderCounter[$assetNo],
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }
        
        // 4. Insert secara chunk untuk menghindari memory limit jika data sangat besar
        $chunks = array_chunk($insertData, 500);
        foreach ($chunks as $chunk) {
            DB::table('cmms_cs_items')->insert($chunk);
        }
        
        $this->command->info("Berhasil migrasi " . count($insertData) . " item checksheet.");
        
        // 5. Drop tabel sementara setelah selesai
        DB::statement('DROP TABLE IF EXISTS cmms_cs_items_legacy');
        
        $this->command->info("Proses seeder migrasi legacy checksheet selesai dengan sukses!");
    }
}
