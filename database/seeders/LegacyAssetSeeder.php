<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegacyAssetSeeder extends Seeder
{
    /**
     * Membaca data legacy dari cmms_master_asset di cmms.sql
     * dan menambahkan asset yang belum ada ke tabel `assets`.
     * Data yang sudah ada TIDAK akan ditimpa.
     */
    public function run(): void
    {
        $sqlFile = base_path('cmms.sql');

        if (!file_exists($sqlFile)) {
            $this->command->error("File cmms.sql tidak ditemukan di root project!");
            return;
        }

        $this->command->info("Membaca cmms.sql untuk data legacy cmms_master_asset...");

        // ── 1. Buat tabel sementara dengan struktur legacy ───────────────────
        DB::statement('DROP TABLE IF EXISTS cmms_master_asset_legacy');
        DB::statement("
            CREATE TABLE cmms_master_asset_legacy (
                assetNo    varchar(30)  DEFAULT NULL,
                lineName   varchar(100) DEFAULT NULL,
                machineName varchar(255) DEFAULT NULL,
                maker      varchar(100) DEFAULT NULL,
                mYear      double       DEFAULT NULL,
                classify   varchar(255) DEFAULT NULL,
                rank_machine varchar(50) DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // ── 2. Scan file baris per baris, cari blok INSERT cmms_master_asset ─
        $this->command->info("Mencari blok INSERT cmms_master_asset...");
        $handle     = fopen($sqlFile, 'r');
        $inInsert   = false;
        $insertQuery = '';

        while (($line = fgets($handle)) !== false) {
            if (
                !$inInsert &&
                strpos($line, "INSERT INTO `cmms_master_asset`") !== false &&
                strpos($line, '`assetNo`') !== false
            ) {
                $inInsert = true;
                $line = str_replace(
                    "INSERT INTO `cmms_master_asset`",
                    "INSERT INTO `cmms_master_asset_legacy`",
                    $line
                );
            }

            if ($inInsert) {
                $insertQuery .= $line;

                if (str_ends_with(trim($line), ';')) {
                    break;
                }
            }
        }

        fclose($handle);

        if (empty($insertQuery)) {
            $this->command->error("Blok INSERT cmms_master_asset tidak ditemukan di cmms.sql!");
            DB::statement('DROP TABLE IF EXISTS cmms_master_asset_legacy');
            return;
        }

        // ── 3. Insert ke tabel sementara ─────────────────────────────────────
        DB::unprepared($insertQuery);
        $total = DB::table('cmms_master_asset_legacy')->count();
        $this->command->info("Ditemukan {$total} data aset legacy.");

        // ── 4. Ambil asset_no yang sudah ada di tabel assets ─────────────────
        $existing = DB::table('assets')->pluck('asset_no')->map(fn($v) => strtoupper(trim($v)))->flip();

        // ── 5. Siapkan data untuk insert: hanya yang BELUM ada ───────────────
        $legacyAssets = DB::table('cmms_master_asset_legacy')->get();

        $toInsert = [];
        $now      = now();

        foreach ($legacyAssets as $row) {
            $assetNo = trim($row->assetNo ?? '');

            if (empty($assetNo)) {
                continue;
            }

            // Skip jika sudah ada
            if ($existing->has(strtoupper($assetNo))) {
                continue;
            }

            $toInsert[] = [
                'asset_no'         => mb_substr($assetNo, 0, 50),
                'line_name'        => $row->lineName  ? mb_substr($row->lineName, 0, 100) : null,
                'machine_name'     => $row->machineName ? mb_substr($row->machineName, 0, 255) : null,
                'maker'            => $row->maker     ? mb_substr($row->maker, 0, 100) : null,
                'manufacture_year' => $row->mYear     ? (int) $row->mYear : null,
                'machine_rank'     => $row->rank_machine ? mb_substr($row->rank_machine, 0, 50) : null,
                'machine_photo'    => null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
        }

        if (empty($toInsert)) {
            $this->command->info("Tidak ada aset baru untuk ditambahkan — semua sudah ada di tabel assets.");
            DB::statement('DROP TABLE IF EXISTS cmms_master_asset_legacy');
            return;
        }

        // ── 6. Insert secara chunk ────────────────────────────────────────────
        foreach (array_chunk($toInsert, 200) as $chunk) {
            DB::table('assets')->insert($chunk);
        }

        $this->command->info("Berhasil menambahkan " . count($toInsert) . " aset baru ke tabel assets.");

        // ── 7. Update cmms_cs_items: isi machine_name & line_name yang masih null ──
        $this->command->info("Memperbarui mapping machine_name & line_name di cmms_cs_items...");

        $assets = DB::table('assets')->select('asset_no', 'machine_name', 'line_name')->get()->keyBy('asset_no');

        $unmapped = DB::table('cmms_cs_items')
            ->whereNull('machine_name')
            ->orWhereNull('line_name')
            ->select('id', 'asset_no')
            ->get();

        $updated = 0;
        foreach ($unmapped as $item) {
            if (isset($assets[$item->asset_no])) {
                $a = $assets[$item->asset_no];
                DB::table('cmms_cs_items')->where('id', $item->id)->update([
                    'machine_name' => $a->machine_name,
                    'line_name'    => $a->line_name,
                ]);
                $updated++;
            }
        }

        $this->command->info("Berhasil update {$updated} baris mapping di cmms_cs_items.");

        // ── 8. Bersihkan tabel sementara ─────────────────────────────────────
        DB::statement('DROP TABLE IF EXISTS cmms_master_asset_legacy');

        $this->command->info("✅ LegacyAssetSeeder selesai!");
    }
}
