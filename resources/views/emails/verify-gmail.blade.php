<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8;padding:20px 0;">
    <tr>
        <td align="center">

            <!-- CARD -->
            <table width="100%" cellpadding="0" cellspacing="0"
                   style="max-width:520px;background:#ffffff;border-radius:8px;
                          box-shadow:0 4px 12px rgba(0,0,0,0.08);overflow:hidden;">

                <!-- HEADER -->
                <tr>
                    <td style="background:#2F80ED;padding:20px;text-align:center;">
                        <h1 style="margin:0;color:#ffffff;font-size:20px;">
                            Verifikasi Email
                        </h1>
                    </td>
                </tr>

                <!-- CONTENT -->
                <tr>
                    <td style="padding:30px;color:#333333;font-size:14px;line-height:1.6;">

                        <p style="margin-top:0;">
                            Halo 👋
                        </p>

                        <p>
                            Terima kasih telah mendaftar di <strong>BathiKu</strong>.
                            Untuk melanjutkan proses pendaftaran, silakan verifikasi
                            alamat email Anda dengan menekan tombol di bawah ini.
                        </p>

                        <!-- BUTTON -->
                        <div style="text-align:center;margin:30px 0;">
                            <a href="{{ $verifyUrl }}"
                               style="
                                   display:inline-block;
                                   padding:14px 28px;
                                   background:#2F80ED;
                                   color:#ffffff;
                                   text-decoration:none;
                                   font-size:14px;
                                   font-weight:bold;
                                   border-radius:6px;
                               ">
                                Verifikasi Email
                            </a>
                        </div>

                        <p style="color:#666;">
                            Jika tombol di atas tidak berfungsi, salin dan buka
                            link berikut di browser Anda:
                        </p>

                        <p style="word-break:break-all;color:#2F80ED;">
                            {{ $verifyUrl }}
                        </p>

                        <p style="margin-top:25px;color:#888;">
                            Jika Anda tidak merasa melakukan pendaftaran,
                            silakan abaikan email ini.
                        </p>

                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="background:#f0f2f5;padding:15px;text-align:center;
                               font-size:12px;color:#999;">
                        © {{ date('Y') }} BathiKu · Sistem Kasir Digital
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
