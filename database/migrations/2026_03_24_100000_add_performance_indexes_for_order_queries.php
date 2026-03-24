<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    private function indexExists(string $table, string $index): bool
    {
        $result = DB::selectOne(
            "SELECT COUNT(1) AS total
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND INDEX_NAME = ?",
            [$table, $index]
        );

        return (int) ($result->total ?? 0) > 0;
    }

    public function up(): void
    {
        // High-impact indexes for order page queries.
        if (! $this->indexExists('tb_harga', 'idx_tb_harga_distribusi_menu')) {
            DB::statement('CREATE INDEX idx_tb_harga_distribusi_menu ON tb_harga (id_distribusi, id_menu)');
        }
        if (! $this->indexExists('tb_menu', 'idx_tb_menu_lokasi_aktif_kategori')) {
            DB::statement('CREATE INDEX idx_tb_menu_lokasi_aktif_kategori ON tb_menu (lokasi, aktif, id_kategori)');
        }
        if (! $this->indexExists('tb_order', 'idx_tb_order_tgl_aktif_meja')) {
            DB::statement('CREATE INDEX idx_tb_order_tgl_aktif_meja ON tb_order (tgl, aktif, id_meja)');
        }
    }

    public function down(): void
    {
        if ($this->indexExists('tb_harga', 'idx_tb_harga_distribusi_menu')) {
            DB::statement('DROP INDEX idx_tb_harga_distribusi_menu ON tb_harga');
        }
        if ($this->indexExists('tb_menu', 'idx_tb_menu_lokasi_aktif_kategori')) {
            DB::statement('DROP INDEX idx_tb_menu_lokasi_aktif_kategori ON tb_menu');
        }
        if ($this->indexExists('tb_order', 'idx_tb_order_tgl_aktif_meja')) {
            DB::statement('DROP INDEX idx_tb_order_tgl_aktif_meja ON tb_order');
        }
    }
};

