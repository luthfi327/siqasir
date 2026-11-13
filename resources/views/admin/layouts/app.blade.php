<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Dashboard') — Admin Bathiku Kasir</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>

    <div class="admin-shell">

        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-brand">
                <span class="logo-badge"><i class="bi bi-shop"></i></span>
                <div>
                    Bathiku Kasir
                    <small>Panel Admin Pyramidsoft</small>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-label">Menu Utama</div>

                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-shop-window"></i> Daftar Toko
                </a>
                <a href="{{ route('admin.promos.index') }}" class="nav-link {{ request()->routeIs('admin.promos.*') ? 'active' : '' }}">
                    <i class="bi bi-tag-fill"></i> Kelola Promo
                </a>
                <a href="{{ route('admin.payments.index') }}" class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card-fill"></i> Pembayaran
                </a>
            </nav>

            <div class="sidebar-footer">
                &copy; {{ now()->year }} Pyramidsoft — Bathiku Kasir
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <button class="btn btn-sm btn-light d-lg-none" id="sidebarToggle" type="button" aria-label="Buka menu">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <div class="topbar-title">@yield('page-title', 'Dashboard')</div>

                <div class="topbar-clock" id="liveClock"></div>

                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar-circle">{{ strtoupper(substr(session('admin_web_nama', 'A'), 0, 1)) }}</span>
                        <span class="d-none d-sm-inline">{{ session('admin_web_nama', 'Admin') }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><span class="dropdown-item-text small text-muted">{{ '@' . session('admin_web_username') }} &middot; {{ ucfirst(session('admin_web_role', 'admin')) }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('admin.logout') }}" method="POST" id="formLogoutAdmin">
                                @csrf
                                <button type="button" class="dropdown-item text-danger"
                                        onclick="konfirmasiLogoutAdmin()">
                                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </header>

            <main class="admin-content">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm alert-auto-dismiss" role="alert">
                        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        <strong>Periksa kembali isian Anda:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                @endif

                @yield('content')

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    @stack('scripts')


{{-- [REVISI] Modal Konfirmasi Logout Admin --}}
<div class="modal fade" id="modalLogoutAdmin" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:14px;overflow:hidden">
            <div class="modal-body text-center py-4 px-4">
                <div style="width:54px;height:54px;background:#fee2e2;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:1.5rem">🚪</div>
                <h6 class="fw-bold mb-1">Konfirmasi Logout</h6>
                <p class="text-muted small mb-4">Anda yakin ingin keluar dari panel admin?</p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm flex-grow-1" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger btn-sm flex-grow-1"
                            onclick="document.getElementById('formLogoutAdmin').submit()">Ya, Logout</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function konfirmasiLogoutAdmin() {
    new bootstrap.Modal(document.getElementById('modalLogoutAdmin')).show();
}
</script>
</body>
</html>