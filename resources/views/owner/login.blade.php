<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Owner — Bathiku Kasir</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0d1b2a;
            overflow: hidden;
        }

        /* ─── Left Panel ─── */
        .left-panel {
            flex: 1;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 40px; position: relative;
            background: linear-gradient(135deg, #0d1b2a 0%, #064e3b 60%, #065f46 100%);
        }
        .orb {
            position: absolute; border-radius: 50%;
            filter: blur(60px); opacity: .3;
            animation: float 8s ease-in-out infinite;
        }
        .orb-1 { width: 280px; height: 280px; background: #10b981; top: -80px; left: -80px; animation-delay: 0s; }
        .orb-2 { width: 200px; height: 200px; background: #059669; bottom: -50px; right: -50px; animation-delay: -3s; }
        .orb-3 { width: 150px; height: 150px; background: #34d399; top: 50%; left: 55%; animation-delay: -6s; }
        @keyframes float {
            0%,100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-20px) scale(1.05); }
        }

        .hero { position: relative; text-align: center; color: #fff; max-width: 400px; }
        .logo-wrap {
            width: 70px; height: 70px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.9rem; color: #fff; margin: 0 auto 22px;
            box-shadow: 0 0 0 12px rgba(16,185,129,.15), 0 0 0 24px rgba(16,185,129,.06);
        }
        .hero h1 { font-size: 1.85rem; font-weight: 700; margin-bottom: 10px; }
        .hero h1 span { color: #6ee7b7; }
        .hero p { color: #a7f3d0; font-size: .88rem; line-height: 1.6; }

        .features { margin-top: 36px; display: flex; flex-direction: column; gap: 12px; position: relative; }
        .feat-item {
            display: flex; align-items: center; gap: 12px;
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 10px; padding: 12px 14px; text-align: left;
        }
        .feat-icon {
            width: 36px; height: 36px; border-radius: 9px;
            background: rgba(16,185,129,.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; color: #6ee7b7; flex-shrink: 0;
        }
        .feat-item strong { display: block; color: #e2e8f0; font-size: .83rem; }
        .feat-item small  { color: #64748b; font-size: .76rem; }

        /* ─── Right Panel (Form) ─── */
        .right-panel {
            width: 430px; background: #fff;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 46px 38px;
        }
        .form-wrap { width: 100%; max-width: 355px; }

        .form-header { margin-bottom: 30px; }
        .form-header h2 { font-size: 1.45rem; font-weight: 700; color: #0f172a; margin-bottom: 5px; }
        .form-header p  { color: #64748b; font-size: .84rem; }

        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: .79rem; font-weight: 600; color: #374151; margin-bottom: 5px; }
        .input-wrap { position: relative; }
        .input-wrap i.ico { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: .95rem; pointer-events: none; }
        .input-wrap input {
            width: 100%; padding: 10px 12px 10px 34px;
            border: 1.5px solid #e2e8f0; border-radius: 10px;
            font-size: .87rem; font-family: inherit; color: #0f172a;
            background: #f8fafc; transition: border-color .2s, box-shadow .2s; outline: none;
        }
        .input-wrap input:focus { border-color: #10b981; background: #fff; box-shadow: 0 0 0 3px rgba(16,185,129,.1); }
        .toggle-pw { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; font-size: .95rem; padding: 4px; }
        .toggle-pw:hover { color: #10b981; }
        .err-msg { color: #ef4444; font-size: .74rem; margin-top: 4px; display: flex; align-items: center; gap: 4px; }

        .flash { border-radius: 10px; padding: 11px 14px; font-size: .83rem; display: flex; align-items: center; gap: 10px; margin-bottom: 18px; }
        .flash.error   { background: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444; }
        .flash.success { background: #f0fdf4; color: #166534; border-left: 4px solid #22c55e; }

        .btn-login {
            width: 100%; padding: 11px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff; border: none; border-radius: 10px;
            font-size: .89rem; font-weight: 600; font-family: inherit;
            cursor: pointer; transition: opacity .2s, transform .1s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-login:hover { opacity: .92; }
        .btn-login:active { transform: scale(.98); }

        .form-footer { text-align: center; margin-top: 26px; color: #94a3b8; font-size: .77rem; }

        @media (max-width: 767.98px) {
            body { flex-direction: column; overflow: auto; }
            .left-panel { padding: 30px 20px; min-height: 200px; }
            .features { display: none; }
            .right-panel { width: 100%; padding: 30px 22px; }
        }
    </style>
</head>
<body>

<!-- Left Panel -->
<div class="left-panel">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="hero">
        <div class="logo-wrap"><i class="bi bi-shop-window"></i></div>
        <h1>Portal <span>Owner</span></h1>
        <p>Kelola toko, pegawai, dan pantau pembayaran premium Anda dari satu tempat melalui browser.</p>
        <div class="features">
            <div class="feat-item">
                <div class="feat-icon"><i class="bi bi-speedometer2"></i></div>
                <div><strong>Dashboard Toko</strong><small>Status akun, exp date, statistik pegawai</small></div>
            </div>
            <div class="feat-item">
                <div class="feat-icon"><i class="bi bi-people-fill"></i></div>
                <div><strong>Kelola Pegawai</strong><small>Aktifkan atau nonaktifkan akun staf</small></div>
            </div>
            <div class="feat-item">
                <div class="feat-icon"><i class="bi bi-credit-card-2-back"></i></div>
                <div><strong>Riwayat Pembayaran</strong><small>Semua transaksi premium tercatat</small></div>
            </div>
        </div>
    </div>
</div>

<!-- Right Panel (Form) -->
<div class="right-panel">
    <div class="form-wrap">
        <div class="form-header">
            <h2>Masuk ke Portal Owner 👋</h2>
            <p>Gunakan username dan password yang sama dengan aplikasi Android Anda.</p>
        </div>

        @if(session('error'))
        <div class="flash error"><i class="bi bi-exclamation-triangle-fill"></i>{{ session('error') }}</div>
        @endif
        @if(session('success'))
        <div class="flash success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>
        @endif

        <form action="{{ route('owner.login.post') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Username</label>
                <div class="input-wrap">
                    <i class="bi bi-person ico"></i>
                    <input type="text" name="username" placeholder="Username Owner"
                           value="{{ old('username') }}" autofocus autocomplete="username">
                </div>
                @error('username')<div class="err-msg"><i class="bi bi-exclamation-circle"></i>{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <i class="bi bi-lock ico"></i>
                    <input type="password" id="pw" name="password" placeholder="Password" autocomplete="current-password">
                    <button type="button" class="toggle-pw" id="togglePw"><i class="bi bi-eye" id="eyeIco"></i></button>
                </div>
                @error('password')<div class="err-msg"><i class="bi bi-exclamation-circle"></i>{{ $message }}</div>@enderror
            </div>

            <div style="margin-top:22px">
                <button type="submit" class="btn-login" id="loginBtn">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span id="btnTxt">Masuk ke Portal</span>
                </button>
            </div>
        </form>

        <div class="form-footer">
            &copy; {{ date('Y') }} Pyramidsoft · Bathiku Kasir<br>
            Akun Owner didaftarkan melalui aplikasi Android.
        </div>
    </div>
</div>

<script>
    document.getElementById('togglePw')?.addEventListener('click', function () {
        const pw  = document.getElementById('pw');
        const ico = document.getElementById('eyeIco');
        const hide = pw.type === 'password';
        pw.type = hide ? 'text' : 'password';
        ico.className = hide ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
    document.querySelector('form').addEventListener('submit', function () {
        const btn = document.getElementById('loginBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    });
</script>
</body>
</html>