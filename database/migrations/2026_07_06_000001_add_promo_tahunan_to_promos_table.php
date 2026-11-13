<?php

// database/migrations/2026_07_06_000001_add_promo_tahunan_to_promos_table.php
// STATUS: BARU
// Menambahkan kolom pendukung fitur promo tahunan ke tabel siqasir.promos.
// Jalankan: php artisan migrate --force

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddPromoTahunanToPromosTable extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan 4 kolom baru ke siqasir.promos:
     *   - tipe_promo          : 'sekali' (default) atau 'tahunan'
     *   - durasi_bulan        : jumlah bulan total promo tahunan (null jika sekali)
     *   - berlaku_mulai       : tanggal mulai promo tahunan (null jika sekali)
     *   - total_bulan_dipakai : counter bulan yang sudah dibayar (0 untuk promo baru)
     */
    public function up()
    {
        // Gunakan raw SQL karena tabel berada di schema 'siqasir'
        // (bukan schema 'public' default Laravel)
        DB::statement("
            ALTER TABLE siqasir.promos
            ADD COLUMN IF NOT EXISTS tipe_promo VARCHAR(20) NOT NULL DEFAULT 'sekali'
        ");

        DB::statement("
            ALTER TABLE siqasir.promos
            ADD COLUMN IF NOT EXISTS durasi_bulan INTEGER DEFAULT NULL
        ");

        DB::statement("
            ALTER TABLE siqasir.promos
            ADD COLUMN IF NOT EXISTS berlaku_mulai TIMESTAMP WITHOUT TIME ZONE DEFAULT NULL
        ");

        DB::statement("
            ALTER TABLE siqasir.promos
            ADD COLUMN IF NOT EXISTS total_bulan_dipakai INTEGER NOT NULL DEFAULT 0
        ");

        // Tambah constraint CHECK agar tipe_promo hanya bisa 'sekali' atau 'tahunan'
        DB::statement("
            ALTER TABLE siqasir.promos
            DROP CONSTRAINT IF EXISTS promos_tipe_promo_check
        ");

        DB::statement("
            ALTER TABLE siqasir.promos
            ADD CONSTRAINT promos_tipe_promo_check
            CHECK (tipe_promo IN ('sekali', 'tahunan'))
        ");

        // Tambah constraint CHECK agar total_bulan_dipakai tidak negatif
        DB::statement("
            ALTER TABLE siqasir.promos
            DROP CONSTRAINT IF EXISTS promos_total_bulan_dipakai_check
        ");

        DB::statement("
            ALTER TABLE siqasir.promos
            ADD CONSTRAINT promos_total_bulan_dipakai_check
            CHECK (total_bulan_dipakai >= 0)
        ");
    }

    /**
     * Reverse the migrations.
     * Hapus kolom yang ditambahkan (rollback).
     */
    public function down()
    {
        DB::statement("ALTER TABLE siqasir.promos DROP CONSTRAINT IF EXISTS promos_tipe_promo_check");
        DB::statement("ALTER TABLE siqasir.promos DROP CONSTRAINT IF EXISTS promos_total_bulan_dipakai_check");
        DB::statement("ALTER TABLE siqasir.promos DROP COLUMN IF EXISTS tipe_promo");
        DB::statement("ALTER TABLE siqasir.promos DROP COLUMN IF EXISTS durasi_bulan");
        DB::statement("ALTER TABLE siqasir.promos DROP COLUMN IF EXISTS berlaku_mulai");
        DB::statement("ALTER TABLE siqasir.promos DROP COLUMN IF EXISTS total_bulan_dipakai");
    }
}