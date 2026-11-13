<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel siqasir.admins.
 * Tabel ini menyimpan akun internal Pyramidsoft yang dapat mengakses panel admin backend.
 * BERBEDA dengan siqasir.users yang merupakan pegawai toko.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('siqasir.admins', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Nama lengkap admin
            $table->string('nama');

            // Username unik untuk login
            $table->string('username')->unique();

            // Password di-hash dengan bcrypt
            $table->string('password');

            // Role akses: 'superadmin' bisa segalanya, 'admin' akses terbatas
            $table->string('role')->default('admin');

            // Status aktif/nonaktif akun
            $table->boolean('aktif')->default(true);

            // Waktu login terakhir — diupdate setiap kali login berhasil
            $table->dateTime('last_login_at')->nullable();

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
        Schema::dropIfExists('siqasir.admins');
    }
};