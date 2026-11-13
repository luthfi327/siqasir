<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login Admin — Bathiku Kasir</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
</head>
<body>

    <div class="login-page">
        <div class="login-card">

            <div class="login-logo">
                <i class="bi bi-shop"></i>
            </div>

            <h1 class="text-center fw-bold fs-4 mb-1">Bathiku Kasir</h1>
            <p class="text-center text-muted small mb-4">Panel Admin Pyramidsoft</p>

            @if (session('error'))
                <div class="alert alert-danger small d-flex align-items-start gap-2 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger small d-flex align-items-start gap-2 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-person-fill"></i></span>
                        <input
                            type="text"
                            class="form-control @error('username') is-invalid @enderror"
                            id="username"
                            name="username"
                            value="{{ old('username') }}"
                            placeholder="Masukkan username"
                            autofocus
                            required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
                        <input
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            required>
                        <button type="button" class="btn btn-light border" id="togglePassword" tabindex="-1" aria-label="Tampilkan password">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-bk-primary w-100 py-2">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                </button>
            </form>

            <p class="text-center text-muted small mt-4 mb-0">
                Khusus tim internal Pyramidsoft. Hubungi superadmin jika lupa kredensial.
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            var input = document.getElementById('password');
            var icon = this.querySelector('i');
            var isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.className = isHidden ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill';
        });
    </script>
</body>
</html>