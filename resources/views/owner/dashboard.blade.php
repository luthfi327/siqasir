{{-- resources/views/owner/dashboard.blade.php --}}
{{-- STATUS: MODIFIKASI --}}
{{-- Perubahan GAP 3: --}}
{{--   - Tambah Baris 2: kartu statistik hari ini & bulan ini (dari SQLite via AJAX) --}}
{{--   - Tambah Baris 3: kartu total produk, pelanggan, stok hampir habis --}}
{{--   - Tambah Baris 4: grafik bar Chart.js 7 hari terakhir (dari $grafikHarian PHP) --}}
{{--   - Tambah Baris 5: tabel 5 transaksi terbaru (dari SQLite via AJAX) --}}
{{--   - Semua data SQLite dimuat AJAX dengan loading spinner --}}

@extends('owner.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Toko')
@section('page-sub', 'Selamat datang, ' . session('owner_toko.nama_pegawai'))

@section('content')

@php
    $expDate   = $toko->exp_date ?? null;
    $expCarbon = $expDate ? \Carbon\Carbon::parse($expDate)->setTimezone('Asia/Jakarta') : null;
@endphp

{{-- ── Banner Status Akun ──────────────────────────────────────────────── --}}
@if($statusAkun === 'expired')
    <div class="alert alert-danger d-flex align-items-center gap-3 mb-4">
        <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0"></i>
        <div>
            <strong>Akun Expired — Segera Perpanjang!</strong><br>
            <span class="text-sm">Masa aktif premium Anda telah berakhir. Buka aplikasi Android untuk melakukan perpanjangan.</span>
        </div>
    </div>
@elseif($statusAkun === 'premium' && $sisaHari !== null && $sisaHari <= 30)
    <div class="alert alert-warning d-flex align-items-center gap-3 mb-4">
        <i class="bi bi-clock-history fs-4 flex-shrink-0"></i>
        <div>
            <strong>Akun hampir expired!</strong><br>
            <span class="text-sm">Masa aktif premium akan berakhir dalam <strong>{{ $sisaHari }} hari</strong>. Segera perpanjang melalui aplikasi Android.</span>
        </div>
    </div>
@endif

{{-- ── Baris 1: Kartu Status Akun (dari PostgreSQL) ──────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card {{ $statusAkun === 'premium' ? 'green' : ($statusAkun === 'trial' ? 'blue' : 'amber') }}">
            <div class="stat-icon"><i class="bi bi-shield-check"></i></div>
            <div class="stat-value">
                @if($statusAkun === 'premium') Premium
                @elseif($statusAkun === 'trial') Trial
                @else Expired
                @endif
            </div>
            <div class="stat-label">Status Akun</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card {{ ($sisaHari !== null && $sisaHari <= 7) ? 'amber' : 'blue' }}">
            <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
            <div class="stat-value">{{ $sisaHari !== null ? max(0, $sisaHari) : '—' }}</div>
            <div class="stat-label">
                Hari lagi{{ $expCarbon ? ' · Exp ' . $expCarbon->translatedFormat('d M Y') : '' }}
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card purple">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-value">{{ number_format($totalPegawai) }}</div>
            <div class="stat-label">Pegawai Aktif</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card green">
            <div class="stat-icon"><i class="bi bi-shop-window"></i></div>
            <div class="stat-value">{{ number_format($totalCabang) }}</div>
            <div class="stat-label">Total Cabang / Outlet</div>
        </div>
    </div>
</div>

{{-- ── Baris 2: Statistik Hari Ini & Bulan Ini (dari SQLite via AJAX) ── --}}
<div class="row g-3 mb-4" id="statsHarian">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card green">
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-value" id="omzetHari">
                <span class="spinner-bk" style="width:20px;height:20px;border-width:2px;"></span>
            </div>
            <div class="stat-label">Omzet Hari Ini</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card blue">
            <div class="stat-icon"><i class="bi bi-receipt"></i></div>
            <div class="stat-value" id="transaksiHari">—</div>
            <div class="stat-label">Transaksi Hari Ini</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card purple">
            <div class="stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
            <div class="stat-value" id="omzetBulan">—</div>
            <div class="stat-label">Omzet Bulan Ini</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card amber">
            <div class="stat-icon"><i class="bi bi-cart-check-fill"></i></div>
            <div class="stat-value" id="transaksiBulan">—</div>
            <div class="stat-label">Transaksi Bulan Ini</div>
        </div>
    </div>
</div>

{{-- ── Baris 3: Statistik Produk & Stok (dari SQLite via AJAX) ───────── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card blue">
            <div class="stat-icon"><i class="bi bi-box-seam-fill"></i></div>
            <div class="stat-value" id="totalProduk">—</div>
            <div class="stat-label">Total Produk Aktif</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card green">
            <div class="stat-icon"><i class="bi bi-person-heart"></i></div>
            <div class="stat-value" id="totalPelanggan">—</div>
            <div class="stat-label">Total Pelanggan</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card amber" id="cardStokHampir">
            <div class="stat-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="stat-value" id="stokHampirHabis">—</div>
            <div class="stat-label">Produk Stok ≤ 5</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card {{ $promoAktif ? 'green' : 'blue' }}">
            <div class="stat-icon"><i class="bi bi-tag-fill"></i></div>
            <div class="stat-value">
                @if($promoAktif)
                    Rp {{ number_format($promoAktif->harga_promo / 1000, 0, ',', '.') }}rb
                @else
                    Normal
                @endif
            </div>
            <div class="stat-label">
                @if($promoAktif) Harga Promo Aktif
                @else Rp 49.000 / bulan
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── Baris 4 & 5: Grafik + Transaksi Terbaru ───────────────────────── --}}
<div class="row g-4 mb-4">

    {{-- Grafik 7 Hari Terakhir --}}
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Penjualan 7 Hari Terakhir</h6>
                <span class="text-xs text-muted">Omzet harian (Rp)</span>
            </div>
            <div class="card-body">
                @if(!empty($grafikHarian))
                    <canvas id="grafikPenjualan" height="220"></canvas>
                @else
                    <div class="loading-overlay text-muted">
                        <i class="bi bi-inbox fs-2"></i>
                        <span>Data grafik belum tersedia.<br>Pastikan sudah melakukan backup dari aplikasi.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Info Toko --}}
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <h6><i class="bi bi-shop me-2 text-success"></i>Informasi Toko</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-2"
                         style="width:56px;height:56px;font-size:1.6rem;">🏪</div>
                    <h6 class="fw-bold mb-1">{{ $toko->nama_toko ?? session('owner_toko.nama_toko') }}</h6>
                    <code class="text-muted text-xs">{{ session('owner_toko.kode_owner') }}</code>
                    <div class="mt-2">
                        @if($statusAkun === 'premium')
                            <span class="badge badge-premium px-3 py-2 rounded-pill">✅ Premium</span>
                        @elseif($statusAkun === 'trial')
                            <span class="badge badge-trial px-3 py-2 rounded-pill">🔵 Trial</span>
                        @else
                            <span class="badge badge-expired px-3 py-2 rounded-pill">❌ Expired</span>
                        @endif
                    </div>
                </div>
                <hr class="my-3">
                <dl class="row small mb-0">
                    <dt class="col-5 text-muted">Kota</dt>
                    <dd class="col-7">{{ $toko->kota ?? '-' }}</dd>
                    <dt class="col-5 text-muted">Exp Date</dt>
                    <dd class="col-7">
                        @if($expCarbon)
                            {{ $expCarbon->translatedFormat('d M Y') }}
                            <br>
                            @if($sisaHari !== null && $sisaHari < 0)
                                <span class="text-danger text-xs">Sudah berakhir {{ abs($sisaHari) }} hari lalu</span>
                            @elseif($sisaHari !== null && $sisaHari <= 7)
                                <span class="text-danger text-xs fw-semibold">⚠ {{ $sisaHari }} hari lagi</span>
                            @elseif($sisaHari !== null && $sisaHari <= 30)
                                <span class="text-warning text-xs fw-semibold">🔔 {{ $sisaHari }} hari lagi</span>
                            @else
                                <span class="text-success text-xs">{{ $sisaHari }} hari lagi</span>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </dd>
                    <dt class="col-5 text-muted">Max Outlet</dt>
                    <dd class="col-7">{{ $toko->max_toko ?? 1 }}</dd>
                    <dt class="col-5 text-muted">Pembayaran</dt>
                    <dd class="col-7">
                        @if($pembayaranTerakhir)
                            {{ \Carbon\Carbon::parse($pembayaranTerakhir->paid_at)->setTimezone('Asia/Jakarta')->translatedFormat('d M Y') }}
                            <span class="badge badge-paid ms-1">PAID</span>
                        @else
                            <span class="text-muted">Belum ada</span>
                        @endif
                    </dd>
                    @if($promoAktif)
                    <dt class="col-5 text-muted">Promo Aktif</dt>
                    <dd class="col-7 text-success fw-semibold">
                        Rp {{ number_format($promoAktif->harga_promo, 0, ',', '.') }}
                        @if($promoAktif->tipe_promo === 'tahunan')
                            <span class="badge text-bg-secondary ms-1 small">Tahunan</span>
                        @endif
                    </dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

</div>

{{-- ── Baris 5: Transaksi Terbaru ─────────────────────────────────────── --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6><i class="bi bi-clock-history me-2 text-primary"></i>5 Transaksi Terbaru</h6>
        <a href="{{ route('owner.penjualan') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-list-ul me-1"></i>Lihat Semua
        </a>
    </div>
    <div id="tabelTransaksiWrap">
        <div class="loading-overlay">
            <div class="spinner-bk"></div>
            <span class="text-muted text-sm">Memuat transaksi terbaru...</span>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(async function () {
    // ── 1. Load statistik hari ini & bulan ini ──────────────────────────
    try {
        const stats = await loadData('{{ route('owner.data.dashboard-stats') }}');
        if (stats.success) {
            document.getElementById('omzetHari').textContent      = formatRupiah(stats.omzet_hari_ini);
            document.getElementById('transaksiHari').textContent   = stats.transaksi_hari_ini;
            document.getElementById('omzetBulan').textContent      = formatRupiah(stats.omzet_bulan_ini);
            document.getElementById('transaksiBulan').textContent  = stats.transaksi_bulan_ini;
            document.getElementById('totalProduk').textContent     = stats.total_produk;
            document.getElementById('totalPelanggan').textContent  = stats.total_pelanggan;
            document.getElementById('stokHampirHabis').textContent = stats.stok_hampir_habis;

            // Warnai card stok jika ada yang hampir habis
            if (stats.stok_hampir_habis > 0) {
                document.getElementById('cardStokHampir').classList.replace('amber', 'red');
            } else {
                document.getElementById('cardStokHampir').classList.replace('amber', 'green');
            }
        } else {
            ['omzetHari','transaksiHari','omzetBulan','transaksiBulan',
             'totalProduk','totalPelanggan','stokHampirHabis'].forEach(id => {
                document.getElementById(id).textContent = '-';
            });
        }
    } catch (e) {
        console.warn('Stats AJAX error:', e);
    }

    // ── 2. Grafik 7 hari dari data PHP (sudah dikirim dari controller) ──
    @if(!empty($grafikHarian))
    (function() {
        const grafikData = @json($grafikHarian);
        const labels  = grafikData.map(d => d.label);
        const omzets  = grafikData.map(d => d.omzet);
        const maxOmzet = Math.max(...omzets, 1);

        new Chart(document.getElementById('grafikPenjualan'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Omzet',
                    data: omzets,
                    backgroundColor: omzets.map(v =>
                        v === Math.max(...omzets) ? 'rgba(16,185,129,0.9)' : 'rgba(16,185,129,0.4)'
                    ),
                    borderColor: 'rgba(16,185,129,1)',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => formatRupiah(ctx.raw)
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => 'Rp ' + (v/1000).toLocaleString('id-ID') + 'rb',
                            font: { size: 10 }
                        },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    })();
    @endif

    // ── 3. Transaksi terbaru (5 data) ───────────────────────────────────
    try {
        const hari = new Date();
        const fmt  = d => String(d).padStart(2,'0');
        const tgl  = `${hari.getFullYear()}-${fmt(hari.getMonth()+1)}-${fmt(hari.getDate())}`;

        const hasil = await loadData('{{ route('owner.data.penjualan') }}', {
            tgl_dari: '2000-01-01', tgl_hingga: tgl, limit: 5, offset: 0
        });

        const wrap = document.getElementById('tabelTransaksiWrap');

        if (!hasil.success || hasil.data.length === 0) {
            wrap.innerHTML = `<div class="loading-overlay text-muted">
                <i class="bi bi-inbox fs-2"></i>
                <span>${hasil.success ? 'Belum ada transaksi.' : 'Data belum tersedia — lakukan backup dari aplikasi Android.'}</span>
            </div>`;
            return;
        }

        let rows = '';
        hasil.data.forEach(t => {
            rows += `<tr>
                <td class="text-xs text-muted">${t.invoice}</td>
                <td>${t.tanggal} ${t.waktu || ''}</td>
                <td>${t.nama_pegawai || '-'}</td>
                <td>${t.jenis_pembayaran || '-'}</td>
                <td class="fw-semibold text-success">${formatRupiah(t.total_jml_transaksi)}</td>
            </tr>`;
        });

        wrap.innerHTML = `
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Tanggal</th>
                            <th>Kasir</th>
                            <th>Metode</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>`;
    } catch (e) {
        document.getElementById('tabelTransaksiWrap').innerHTML =
            `<div class="loading-overlay text-muted"><i class="bi bi-wifi-off fs-2"></i><span>Gagal memuat data.</span></div>`;
    }
})();
</script>
@endpush