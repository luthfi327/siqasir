<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel siqasir.promos.
 * Tabel ini menyimpan harga promo khusus per kode_owner yang dikelola oleh admin Pyramidsoft.
 */
class CreatePromosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('siqasir.promos', function (Blueprint $table) {
            $table->bigIncrements('id');

            // FK konseptual ke siqasir.toko.kode_owner
            $table->string('kode_owner')->index();

            // Nominal harga promo dalam rupiah (misal: 15000)
            $table->integer('harga_promo');

            // Jika null, promo berlaku selamanya sampai di-nonaktifkan secara manual
            $table->dateTime('berlaku_hingga')->nullable();

            // Catatan alasan promo dari admin
            $table->text('catatan')->nullable();

            // Username admin yang membuat promo ini
            $table->string('dibuat_oleh')->nullable();

            // Status aktif/nonaktif promo
            $table->boolean('aktif')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('siqasir.promos');
    }
}
