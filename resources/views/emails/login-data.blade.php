{{-- resources/views/emails/login-data.blade.php --}}
{{-- STATUS: MODIFIKASI --}}
{{-- Perubahan FIX: --}}
{{--   - HAPUS baris <strong>Password:</strong> {{ $user->password }} --}}
{{--     Alasan: password di DB adalah bcrypt hash ($2y$10$...) bukan password asli --}}
{{--     Menampilkan hash ke email: (1) tidak berguna karena tidak bisa dipakai login --}}
{{--                                (2) risiko keamanan karena hash terekspos --}}
{{--   - Ganti dengan catatan informatif: password tidak ditampilkan demi keamanan --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Data Login Anda</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f6f9; font-family:Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding:20px;">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:8px; padding:30px; box-shadow:0 4px 12px rgba(0,0,0,0.05);">

                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding-bottom:20px;">
                            <h2 style="margin:0; color:#2c3e50;">
                                🔐 Informasi Login Akun Anda
                            </h2>
                        </td>
                    </tr>

                    <!-- Toko Info -->
                    <tr>
                        <td style="padding:15px 0; font-size:14px; color:#555;">
                            <strong>Nama Toko:</strong> {{ $nama_toko }} <br>
                            <strong>Kode Owner:</strong> {{ $kode_owner }}
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td>
                            <hr style="border:none; border-top:1px solid #eee;">
                        </td>
                    </tr>

                    <!-- Users -->
                    @foreach($users as $user)
                        <tr>
                            <td style="padding:15px 0;">
                                <table width="100%" cellpadding="10" cellspacing="0"
                                    style="background:#f9fafc; border-radius:6px; border:1px solid #eaecef;">
                                    <tr>
                                        <td style="font-size:14px; color:#333;">
                                            <strong>Hak Akses:</strong> {{ $user->hak_akses }} <br>
                                            <strong>Username:</strong> {{ $user->username }} <br>

                                            {{--
                                                FIX: Password TIDAK ditampilkan di email.
                                                Alasan keamanan:
                                                  1. Password di database disimpan sebagai bcrypt hash
                                                     ($2y$10$...) — bukan password asli yang bisa dipakai login.
                                                  2. Menampilkan hash ke email tidak berguna bagi user
                                                     dan berisiko jika email tersadap.
                                                Solusi: user menggunakan password yang mereka daftarkan
                                                sendiri saat registrasi di aplikasi Android.
                                            --}}
                                            <strong>PIN:</strong> {{ $user->pin }} <br>

                                            <span style="font-size:12px; color:#e67e22; background:#fef9e7;
                                                         padding:4px 8px; border-radius:4px; display:inline-block; margin-top:6px;">
                                                🔑 Password tidak ditampilkan demi keamanan akun Anda.
                                                Gunakan password yang Anda daftarkan saat registrasi di aplikasi.
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endforeach

                    <!-- Warning -->
                    <tr>
                        <td style="padding-top:20px; font-size:12px; color:#888;">
                            ⚠️ Demi keamanan, jangan bagikan data ini ke sembarang orang.
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding-top:30px; font-size:12px; color:#aaa;">
                            Email ini dikirim otomatis oleh sistem.<br>
                            Jangan membalas email ini.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>