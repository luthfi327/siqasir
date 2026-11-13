<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * AdminSeeder
 *
 * Membuat akun superadmin default untuk Pyramidsoft.
 * Jalankan SEKALI saat setup pertama kali:
 *
 *   php artisan db:seed --class=AdminSeeder
 *
 * PENTING: Segera ganti password setelah pertama kali login ke production!
 *
 * Kredensial default:
 *   Username : pyramidsoft_admin
 *   Password : Admin@Pyramidsoft2024
 *   Role     : superadmin
 */
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Gunakan updateOrInsert agar aman dijalankan berkali-kali
        // (tidak membuat duplikat jika seeder dijalankan ulang)
        DB::table('siqasir.admins')->updateOrInsert(
            // Kondisi: cari berdasarkan username
            ['username' => 'pyramidsoft_admin'],
            // Data yang di-insert atau di-update
            [
                'nama'          => 'Superadmin Pyramidsoft',
                'username'      => 'pyramidsoft_admin',
                'password'      => Hash::make('Admin@Pyramidsoft2024'),
                'role'          => 'superadmin',
                'aktif'         => true,
                'last_login_at' => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]
        );

        $this->command->info('✅ AdminSeeder: Akun superadmin berhasil dibuat.');
        $this->command->warn('⚠️  PENTING: Ganti password setelah pertama kali login di production!');
        $this->command->line('   Username : pyramidsoft_admin');
        $this->command->line('   Password : Admin@Pyramidsoft2024');
    }
}
