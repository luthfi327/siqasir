<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration untuk menambahkan kolom harga_kategori pada tabel siqasir.payments.
 * Kolom ini mencatat kategori harga yang digunakan saat pembayaran, berguna untuk laporan admin.
 * Nilai yang mungkin: 'trial_promo' | 'promo_bulan4' | 'normal'
 */
class AddHargaKategoriToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // PostgreSQL tidak mendukung ->after() di Blueprint, gunakan raw SQL untuk menambah kolom
        DB::statement("ALTER TABLE siqasir.payments ADD COLUMN IF NOT EXISTS harga_kategori VARCHAR(30) NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE siqasir.payments DROP COLUMN IF EXISTS harga_kategori");
    }
}
