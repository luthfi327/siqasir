<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::connection('pgsql')->create('siqasir.users', function (Blueprint $table) {
            $table->id(); // auto increment id
            $table->integer('id_toko')->default(1); // 1 = toko utama
            $table->string('kode_pegawai', 50)->unique();
            $table->string('nama_pegawai');
            $table->string('no_hp')->nullable();
            $table->string('hak_akses')->default('Owner'); // Owner, Admin, etc
            $table->string('posisi')->nullable();
            $table->string('status')->default('Aktif');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('pin')->nullable();
            $table->string('nama_toko');
            $table->string('kode_owner');
            $table->string('kode_franchise');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('pgsql')->dropIfExists('siqasir.users');
    }
}
