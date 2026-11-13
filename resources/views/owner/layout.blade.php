{{-- resources/views/owner/layout.blade.php --}}
{{-- STATUS: MODIFIKASI --}}
{{-- Perubahan GAP 3: --}}
{{--   - Tambah menu navigasi baru di sidebar: Penjualan, Produk & Stok, Laporan, Pelanggan --}}
{{--   - Semua link di-check dengan request()->routeIs() agar active state benar --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Owner') — Bathiku Kasir</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        :root {
            --sidebar-w: 250px;
            --sidebar-bg: #0d1b2a;
            --sidebar-border: rgba(255,255,255,.06);
            --sidebar-text: #94a3b8;
            --sidebar-active-bg: rgba(16,185,129,.18);
            --sidebar-active-text: #6ee7b7;
            --brand-color: #10b981;
            --brand-dark: #059669;
            --topbar-h: 62px;
            --body-bg: #f1f5f9;
            --card-shadow: 0 1px 3px rgba(0,0,0,.08);
            --radius: 12px;
            --tr: .2s ease;
        }

        * { box-sizing: border-box; }
        body { font-family: 'Inter', system-ui, sans-serif; font-size: .875rem; background: var(--body-bg); color: #334155; margin: 0; }

        /* ─── Sidebar ─── */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w); height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            display: flex; flex-direction: column;
            z-index: 1040;
            transition: transform var(--tr);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,.1) transparent;
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 4px; }

        .sidebar-brand {
            padding: 20px 18px 16px;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex; align-items: center; gap: 12px;
            text-decoration: none; flex-shrink: 0;
        }
        .brand-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; color: #fff; flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(16,185,129,.35);
        }
        .brand-text strong { display: block; color: #f1f5f9; font-size: .93rem; font-weight: 700; }
        .brand-text small  { color: #64748b; font-size: .7rem; }

        .sidebar-section {
            padding: 14px 14px 4px;
            font-size: .67rem; font-weight: 600;
            letter-spacing: .08em; text-transform: uppercase; color: #475569;
        }

        .sidebar-nav { padding: 0 8px; flex: 1; }

        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 8px;
            color: var(--sidebar-text);
            text-decoration: none; font-size: .84rem; font-weight: 500;
            transition: background var(--tr), color var(--tr);
            margin-bottom: 2px; position: relative;
        }
        .sidebar-link i { font-size: .95rem; width: 18px; text-align: center; flex-shrink: 0; }
        .sidebar-link:hover { background: rgba(255,255,255,.06); color: #e2e8f0; }
        .sidebar-link.active { background: var(--sidebar-active-bg); color: var(--sidebar-active-text); }
        .sidebar-link.active::before {
            content: '';
            position: absolute; left: 0; top: 22%; bottom: 22%;
            width: 3px; background: var(--brand-color); border-radius: 0 3px 3px 0;
        }

        .sidebar-footer {
            padding: 12px 10px; border-top: 1px solid var(--sidebar-border); flex-shrink: 0;
        }
        .sidebar-footer .user-card {
            display: flex; align-items: center; gap: 10px;
            padding: 10px; background: rgba(255,255,255,.04); border-radius: 8px;
        }
        .sidebar-footer .avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem; color: #fff; font-weight: 700; flex-shrink: 0;
        }
        .sidebar-footer .user-info strong { display: block; color: #e2e8f0; font-size: .82rem; font-weight: 600; }
        .sidebar-footer .user-info small  { color: #64748b; font-size: .7rem; }

        /* ─── Topbar ─── */
        .topbar {
            position: fixed; top: 0;
            left: var(--sidebar-w); right: 0;
            height: var(--topbar-h);
            background: #fff; border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center; padding: 0 22px;
            z-index: 1030; gap: 14px;
        }
        .topbar-toggle {
            display: none; border: none; background: transparent;
            font-size: 1.4rem; color: #64748b; cursor: pointer; padding: 4px;
        }
        .topbar-breadcrumb { flex: 1; }
        .topbar-breadcrumb .page-title { font-size: .98rem; font-weight: 700; color: #0f172a; margin: 0; }
        .topbar-breadcrumb .page-sub   { font-size: .73rem; color: #94a3b8; margin: 0; }

        .topbar-actions { display: flex; align-items: center; gap: 8px; }
        .topbar-user {
            display: flex; align-items: center; gap: 8px;
            padding: 5px 10px; background: #f0fdf4; border-radius: 8px;
            font-size: .79rem; font-weight: 500; color: #065f46;
        }
        .topbar-user .av-sm {
            width: 26px; height: 26px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: .72rem; color: #fff; font-weight: 700;
        }
        .topbar-btn {
            width: 36px; height: 36px; border-radius: 8px;
            border: 1px solid #e2e8f0; background: #fff; color: #64748b;
            display: flex; align-items: center; justify-content: center;
            font-size: .95rem; cursor: pointer; text-decoration: none;
            transition: all var(--tr);
        }
        .topbar-btn:hover { background: #f8fafc; color: #334155; }

        /* ─── Main ─── */
        .main-content { margin-left: var(--sidebar-w); padding-top: var(--topbar-h); min-height: 100vh; }
        .content-wrapper { padding: 26px 22px; }

        /* ─── Cards ─── */
        .card { border: 1px solid #e2e8f0; border-radius: var(--radius); box-shadow: var(--card-shadow); background: #fff; }
        .card-header { background: #fff; border-bottom: 1px solid #f1f5f9; padding: 14px 18px; border-radius: var(--radius) var(--radius) 0 0 !important; }
        .card-header h6 { margin: 0; font-weight: 600; font-size: .88rem; color: #1e293b; }
        .card-body { padding: 18px; }

        /* ─── Stat cards ─── */
        .stat-card {
            border-radius: var(--radius); padding: 18px; border: none;
            box-shadow: var(--card-shadow); color: #fff;
            transition: transform var(--tr);
        }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-card.green  { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-card.blue   { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .stat-card.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .stat-card.amber  { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-card.red    { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .stat-icon {
            width: 42px; height: 42px; background: rgba(255,255,255,.2);
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; margin-bottom: 12px;
        }
        .stat-value { font-size: 1.7rem; font-weight: 700; line-height: 1; margin-bottom: 3px; }
        .stat-label { font-size: .76rem; color: rgba(255,255,255,.75); font-weight: 500; }
        .stat-sub   { font-size: .72rem; color: rgba(255,255,255,.6); margin-top: 4px; }

        /* ─── Badges ─── */
        .badge { font-weight: 500; }
        .badge-premium  { background: #dcfce7; color: #166534; }
        .badge-trial    { background: #dbeafe; color: #1e40af; }
        .badge-expired  { background: #fee2e2; color: #991b1b; }
        .badge-paid     { background: #dcfce7; color: #166534; }
        .badge-pending  { background: #fef9c3; color: #854d0e; }
        .badge-cancelled{ background: #fee2e2; color: #991b1b; }
        .badge-aktif    { background: #dcfce7; color: #166534; }
        .badge-nonaktif { background: #fee2e2; color: #991b1b; }

        /* ─── Alerts ─── */
        .alert { border-radius: 10px; border: none; font-size: .85rem; }
        .alert-success { background: #f0fdf4; color: #166534; border-left: 4px solid #22c55e; }
        .alert-danger  { background: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444; }
        .alert-warning { background: #fffbeb; color: #854d0e; border-left: 4px solid #f59e0b; }
        .alert-info    { background: #eff6ff; color: #1e40af; border-left: 4px solid #3b82f6; }

        /* ─── Table ─── */
        .table { font-size: .84rem; }
        .table thead th {
            font-weight: 600; font-size: .76rem; text-transform: uppercase;
            letter-spacing: .05em; color: #64748b; background: #f8fafc;
            border-bottom: 1px solid #e2e8f0; padding: 9px 13px; white-space: nowrap;
        }
        .table tbody td { padding: 11px 13px; vertical-align: middle; border-color: #f1f5f9; }
        .table tbody tr:hover { background: #f8fafc; }
        .row-warning td { background: #fffbeb !important; }
        .row-danger  td { background: #fef2f2 !important; }

        /* ─── Forms ─── */
        .form-control, .form-select { font-size: .84rem; border-color: #e2e8f0; border-radius: 8px; }
        .form-control:focus, .form-select:focus { border-color: var(--brand-color); box-shadow: 0 0 0 3px rgba(16,185,129,.12); }
        .form-label { font-weight: 600; font-size: .79rem; color: #374151; margin-bottom: 5px; }
        .btn { font-size: .83rem; font-weight: 500; border-radius: 8px; padding: 7px 15px; transition: all var(--tr); }
        .btn-success { background: var(--brand-color); border-color: var(--brand-color); }
        .btn-success:hover { background: var(--brand-dark); border-color: var(--brand-dark); }
        .btn-sm { padding: 4px 11px; font-size: .79rem; }

        /* ─── Loading spinner ─── */
        .loading-overlay {
            display: flex; align-items: center; justify-content: center;
            padding: 40px; color: #94a3b8; flex-direction: column; gap: 12px;
        }
        .spinner-bk {
            width: 32px; height: 32px;
            border: 3px solid #e2e8f0;
            border-top-color: var(--brand-color);
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ─── Overlay mobile ─── */
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 1039; }

        /* ─── Responsive ─── */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(calc(-1 * var(--sidebar-w))); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.open { display: block; }
            .topbar { left: 0; }
            .topbar-toggle { display: flex; align-items: center; justify-content: center; }
            .main-content { margin-left: 0; }
        }
        @media (max-width: 575.98px) {
            .content-wrapper { padding: 14px; }
            .topbar { padding: 0 14px; }
        }

        /* ─── Misc ─── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .text-xs { font-size: .75rem; }
        .text-sm { font-size: .84rem; }
    </style>
    @stack('styles')
</head>
<body>

<!-- Overlay mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ─── SIDEBAR ─── -->
<aside class="sidebar" id="sidebar">
    <a href="{{ route('owner.dashboard') }}" class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-shop-window"></i></div>
        <div class="brand-text">
            <strong>{{ session('owner_toko.nama_toko', 'Toko Saya') }}</strong>
            <small>Portal Owner</small>
        </div>
    </a>

    <nav class="sidebar-nav mt-2">

        {{-- ── Utama ── --}}
        <div class="sidebar-section">Utama</div>

        <a href="{{ route('owner.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i><span>Dashboard</span>
        </a>

        {{-- ── Data Toko (dari SQLite) ── --}}
        <div class="sidebar-section">Data Toko</div>

        <a href="{{ route('owner.penjualan') }}"
           class="sidebar-link {{ request()->routeIs('owner.penjualan') ? 'active' : '' }}">
            <i class="bi bi-bag-check-fill"></i><span>Penjualan</span>
        </a>

        <a href="{{ route('owner.produk') }}"
           class="sidebar-link {{ request()->routeIs('owner.produk') ? 'active' : '' }}">
            <i class="bi bi-box-seam-fill"></i><span>Produk &amp; Stok</span>
        </a>

        <a href="{{ route('owner.laporan') }}"
           class="sidebar-link {{ request()->routeIs('owner.laporan') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-fill"></i><span>Laporan</span>
        </a>

        <a href="{{ route('owner.kas-keluar') }}"
           class="sidebar-link {{ request()->routeIs('owner.kas-keluar') ? 'active' : '' }}">
            <i class="bi bi-cash-stack"></i><span>Kas Keluar</span>
        </a>

        {{-- ── Akun ── --}}
        <div class="sidebar-section">Akun</div>

        <a href="{{ route('owner.pegawai') }}"
           class="sidebar-link {{ request()->routeIs('owner.pegawai') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i><span>Kelola Pegawai</span>
        </a>

        <a href="{{ route('owner.promo') }}"
           class="sidebar-link {{ request()->routeIs('owner.promo') ? 'active' : '' }}">
            <i class="bi bi-tag-fill"></i><span>Promo Aktif</span>
        </a>

        <a href="{{ route('owner.payments') }}"
           class="sidebar-link {{ request()->routeIs('owner.payments') ? 'active' : '' }}">
            <i class="bi bi-credit-card-2-back-fill"></i><span>Riwayat Pembayaran</span>
        </a>

    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="avatar">{{ strtoupper(substr(session('owner_toko.nama_pegawai', 'O'), 0, 1)) }}</div>
            <div class="user-info">
                <strong>{{ session('owner_toko.nama_pegawai', 'Owner') }}</strong>
                <small>{{ session('owner_toko.kode_owner', '') }}</small>
            </div>
        </div>
    </div>
</aside>

<!-- ─── TOPBAR ─── -->
<header class="topbar">
    <button class="topbar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
    <div class="topbar-breadcrumb">
        <p class="page-title">@yield('page-title', 'Dashboard')</p>
        <p class="page-sub">@yield('page-sub', 'Portal Owner — Bathiku Kasir')</p>
    </div>
    <div class="topbar-actions">
        <div class="topbar-user d-none d-sm-flex">
            <div class="av-sm">{{ strtoupper(substr(session('owner_toko.nama_pegawai', 'O'), 0, 1)) }}</div>
            <span>{{ session('owner_toko.nama_toko') }}</span>
        </div>
        <form action="{{ route('owner.logout') }}" method="POST" class="d-inline" id="formLogoutOwner">
            @csrf
            <button type="button" class="topbar-btn" title="Logout"
                    onclick="konfirmasiLogout()">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</header>

<!-- ─── MAIN ─── -->
<main class="main-content">
    <div class="content-wrapper">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeInDown mb-4">
            <i class="bi bi-check-circle-fill me-2"></i><strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeInDown mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggle  = document.getElementById('sidebarToggle');

    toggle?.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('open');
    });
    overlay?.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
    });

    document.querySelectorAll('[data-bs-toggle="tooltip"]')
        .forEach(el => new bootstrap.Tooltip(el));

    setTimeout(() => {
        document.querySelectorAll('.alert-dismissible').forEach(el => {
            bootstrap.Alert.getOrCreateInstance(el)?.close();
        });
    }, 5000);

    // Helper format rupiah global
    window.formatRupiah = (n) =>
        'Rp ' + Number(n).toLocaleString('id-ID');

    // Helper AJAX
    window.loadData = async function(url, params = {}) {
        const query = new URLSearchParams(params).toString();
        const res   = await fetch(`${url}?${query}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!res.ok) throw new Error('Server error ' + res.status);
        return res.json();
    };
</script>
@stack('scripts')

{{-- [REVISI] Modal Konfirmasi Logout --}}
<div class="modal fade" id="modalLogout" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:14px;overflow:hidden">
            <div class="modal-body text-center py-4 px-4">
                <div style="width:54px;height:54px;background:#fee2e2;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:1.5rem">
                    🚪
                </div>
                <h6 class="fw-bold mb-1" style="color:#0f172a">Konfirmasi Logout</h6>
                <p class="text-muted small mb-4">Anda yakin ingin keluar dari portal owner?</p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm flex-grow-1"
                            data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger btn-sm flex-grow-1"
                            onclick="document.getElementById('formLogoutOwner').submit()">
                        Ya, Logout
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function konfirmasiLogout() {
    new bootstrap.Modal(document.getElementById('modalLogout')).show();
}
</script>
</body>
</html>