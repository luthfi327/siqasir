{{-- resources/views/owner/laporan.blade.php --}}
{{-- STATUS: BARU --}}
{{-- Halaman laporan dua tab: Laporan Kas dan Laporan Penjualan --}}
{{-- Data dari /owner/data/laporan-kas dan /owner/data/laporan-penjualan --}}

@extends('owner.layout')

@section('title', 'Laporan')
@section('page-title', 'Laporan')
@section('page-sub', 'Laporan kas dan penjualan toko Anda')

@section('content')

@if(!$sqliteAda)
<div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
    <div><strong>Data belum tersedia.</strong> Lakukan tutup kasir dan backup dari aplikasi Android.</div>
</div>
@endif

{{-- ── Tab Navigation ───────────────────────────────────────────────────── --}}
<ul class="nav nav-pills mb-4 gap-2" id="tabLaporan">
    <li class="nav-item">
        <button class="nav-link active px-4 py-2 fw-semibold" id="tabKasBtn"
                onclick="switchTab('kas')">
            <i class="bi bi-safe2-fill me-2"></i>Laporan Kas
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link px-4 py-2 fw-semibold" id="tabPenjualanBtn"
                onclick="switchTab('penjualan')">
            <i class="bi bi-bar-chart-fill me-2"></i>Laporan Penjualan
        </button>
    </li>
</ul>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- TAB 1 — LAPORAN KAS                                                   --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<div id="tabKas">

    {{-- Filter Kas --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="row g-2 align-items-end">
                <div class="col-sm-4 col-lg-3">
                    <label class="form-label">Tanggal Dari</label>
                    <input type="date" id="kasFilterDari" class="form-control"
                           value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                </div>
                <div class="col-sm-4 col-lg-3">
                    <label class="form-label">Tanggal Hingga</label>
                    <input type="date" id="kasFilterHingga" class="form-control"
                           value="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="col-sm-4 col-lg-2">
                    <label class="form-label">Outlet</label>
                    <select id="kasFilterToko" class="form-select">
                        <option value="">Semua</option>
                    </select>
                </div>
                <div class="col-sm-12 col-lg-4">
                    <div class="d-flex gap-2">
                        <button class="btn btn-success flex-grow-1" onclick="loadLaporanKas()">
                            <i class="bi bi-search me-1"></i> Tampilkan
                        </button>
                        <button class="btn btn-outline-secondary" onclick="kasResetFilter()">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Kas --}}
    <div class="row g-3 mb-4" id="kasSummary">
        @foreach([
            ['id'=>'kasModalAwal','icon'=>'bi-safe2','label'=>'Total Modal Awal','color'=>'blue'],
            ['id'=>'kasPenjualan','icon'=>'bi-graph-up-arrow','label'=>'Total Penjualan','color'=>'green'],
            ['id'=>'kasKeluar','icon'=>'bi-arrow-up-right-circle','label'=>'Total Kas Keluar','color'=>'amber'],
            ['id'=>'kasPenerimaan','icon'=>'bi-cash-coin','label'=>'Penerimaan Sistem','color'=>'purple'],
            ['id'=>'kasSelisih','icon'=>'bi-calculator','label'=>'Total Selisih','color'=>'red'],
        ] as $k)
        <div class="col-sm-6 col-xl">
            <div class="stat-card {{ $k['color'] }}">
                <div class="stat-icon"><i class="bi {{ $k['icon'] }}"></i></div>
                <div class="stat-value text-sm" id="{{ $k['id'] }}">—</div>
                <div class="stat-label">{{ $k['label'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Tabel Kas --}}
    <div class="card">
        <div class="card-header">
            <h6><i class="bi bi-table me-2 text-primary"></i>Riwayat Buka-Tutup Kasir</h6>
        </div>
        <div id="tabelKasWrap">
            <div class="loading-overlay">
                <div class="spinner-bk"></div>
                <span class="text-muted text-sm">Memuat laporan kas...</span>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- TAB 2 — LAPORAN PENJUALAN                                             --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<div id="tabPenjualan" style="display:none">

    {{-- Filter Penjualan --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="row g-2 align-items-end">
                <div class="col-sm-3 col-lg-2">
                    <label class="form-label">Bulan</label>
                    <select id="pjFilterBulan" class="form-select">
                        @foreach([
                            '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
                            '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
                            '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember',
                        ] as $val => $label)
                        <option value="{{ $val }}" {{ now()->format('m') === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-3 col-lg-2">
                    <label class="form-label">Tahun</label>
                    <select id="pjFilterTahun" class="form-select">
                        @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ now()->year === $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-sm-3 col-lg-2">
                    <label class="form-label">Outlet</label>
                    <select id="pjFilterToko" class="form-select">
                        <option value="">Semua</option>
                    </select>
                </div>
                <div class="col-sm-3 col-lg-3">
                    <button class="btn btn-success w-100" onclick="loadLaporanPenjualan()">
                        <i class="bi bi-search me-1"></i> Tampilkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Penjualan --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="stat-card green">
                <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
                <div class="stat-value text-sm" id="pjOmzet">—</div>
                <div class="stat-label">Total Omzet Bulan Ini</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card blue">
                <div class="stat-icon"><i class="bi bi-receipt"></i></div>
                <div class="stat-value" id="pjTransaksi">—</div>
                <div class="stat-label">Total Transaksi</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card purple">
                <div class="stat-icon"><i class="bi bi-calculator"></i></div>
                <div class="stat-value text-sm" id="pjRataRata">—</div>
                <div class="stat-label">Rata-rata per Hari</div>
            </div>
        </div>
    </div>

    {{-- Grafik Omzet per Hari --}}
    <div class="card mb-4">
        <div class="card-header">
            <h6><i class="bi bi-bar-chart me-2 text-primary"></i>Omzet per Hari</h6>
        </div>
        <div class="card-body" id="grafikWrap">
            <div class="loading-overlay">
                <div class="spinner-bk"></div>
                <span class="text-muted text-sm">Memuat grafik...</span>
            </div>
        </div>
    </div>

    {{-- Tabel Terlaris & Per Kasir --}}
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6><i class="bi bi-trophy-fill text-warning me-2"></i>10 Produk Terlaris</h6>
                </div>
                <div id="tabelTerlaris">
                    <div class="loading-overlay"><div class="spinner-bk"></div></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6><i class="bi bi-person-check-fill text-success me-2"></i>Penjualan per Kasir</h6>
                </div>
                <div id="tabelKasir">
                    <div class="loading-overlay"><div class="spinner-bk"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
let grafikChart = null;

// ── Tab switcher ──────────────────────────────────────────────────────────
function switchTab(tab) {
    document.getElementById('tabKas').style.display       = tab === 'kas'       ? '' : 'none';
    document.getElementById('tabPenjualan').style.display = tab === 'penjualan' ? '' : 'none';
    document.getElementById('tabKasBtn').classList.toggle('active',       tab === 'kas');
    document.getElementById('tabPenjualanBtn').classList.toggle('active', tab === 'penjualan');
}

// ── Init ──────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
    // Load daftar toko untuk semua dropdown
    try {
        const res = await loadData('{{ route('owner.data.toko-list') }}');
        if (res.success && res.data.length > 1) {
            ['kasFilterToko','pjFilterToko'].forEach(id => {
                const sel = document.getElementById(id);
                res.data.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.id_toko; opt.textContent = t.nama_toko;
                    sel.appendChild(opt);
                });
            });
        }
    } catch(e) {}

    await loadLaporanKas();
});

// ── Laporan Kas ───────────────────────────────────────────────────────────
function kasResetFilter() {
    document.getElementById('kasFilterDari').value   = '{{ now()->startOfMonth()->format('Y-m-d') }}';
    document.getElementById('kasFilterHingga').value = '{{ now()->format('Y-m-d') }}';
    document.getElementById('kasFilterToko').value   = '';
    loadLaporanKas();
}

async function loadLaporanKas() {
    const wrap = document.getElementById('tabelKasWrap');
    wrap.innerHTML = `<div class="loading-overlay"><div class="spinner-bk"></div><span class="text-muted text-sm">Memuat...</span></div>`;

    ['kasModalAwal','kasPenjualan','kasKeluar','kasPenerimaan','kasSelisih']
        .forEach(id => { document.getElementById(id).textContent = '—'; });

    const params = {
        tgl_dari  : document.getElementById('kasFilterDari').value,
        tgl_hingga: document.getElementById('kasFilterHingga').value,
        id_toko   : document.getElementById('kasFilterToko').value,
    };

    try {
        const res = await loadData('{{ route('owner.data.laporan-kas') }}', params);
        if (!res.success) {
            wrap.innerHTML = `<div class="loading-overlay text-muted"><i class="bi bi-database-x fs-2"></i><span>Data belum tersedia.</span></div>`;
            return;
        }

        // Update summary
        document.getElementById('kasModalAwal').textContent   = formatRupiah(res.total_modal_awal);
        document.getElementById('kasPenjualan').textContent   = formatRupiah(res.total_penjualan);
        document.getElementById('kasKeluar').textContent      = formatRupiah(res.total_kas_keluar);
        document.getElementById('kasPenerimaan').textContent  = formatRupiah(res.total_penerimaan_sistem);
        document.getElementById('kasSelisih').textContent     = formatRupiah(res.total_selisih);

        if (!res.riwayat || res.riwayat.length === 0) {
            wrap.innerHTML = `<div class="loading-overlay text-muted"><i class="bi bi-inbox fs-2"></i><span>Tidak ada data untuk periode ini.</span></div>`;
            return;
        }

        let rows = '';
        res.riwayat.forEach(r => {
            const selisihClass = r.selisih < 0 ? 'text-danger fw-semibold' : '';
            rows += `<tr>
                <td>
                    <div class="fw-semibold text-sm">${r.tgl_buka || '-'}</div>
                    <div class="text-muted text-xs">${r.waktu_buka || ''}</div>
                </td>
                <td>
                    <div class="text-sm">${r.tgl_tutup || '-'}</div>
                    <div class="text-muted text-xs">${r.waktu_tutup || ''}</div>
                </td>
                <td>${formatRupiah(r.total_modalawal)}</td>
                <td class="text-success fw-semibold">${formatRupiah(r.total_penjualan)}</td>
                <td>${formatRupiah(r.total_kaskeluar)}</td>
                <td>${formatRupiah(r.penerimaan_sistem)}</td>
                <td class="${selisihClass}">${formatRupiah(r.selisih)}</td>
                <td><span class="badge ${r.status === 'tutup' ? 'bg-success' : 'bg-warning text-dark'}">${r.status || '-'}</span></td>
            </tr>`;
        });

        wrap.innerHTML = `
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tgl Buka</th><th>Tgl Tutup</th>
                            <th>Modal Awal</th><th>Total Penjualan</th>
                            <th>Kas Keluar</th><th>Penerimaan</th>
                            <th>Selisih</th><th>Status</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>`;
    } catch(e) {
        wrap.innerHTML = `<div class="loading-overlay text-danger"><i class="bi bi-wifi-off fs-2"></i><span>Gagal memuat data.</span></div>`;
    }
}

// ── Laporan Penjualan ─────────────────────────────────────────────────────
async function loadLaporanPenjualan() {
    const grafikWrap   = document.getElementById('grafikWrap');
    const tabelTerlaris = document.getElementById('tabelTerlaris');
    const tabelKasir   = document.getElementById('tabelKasir');
    const spinner      = `<div class="loading-overlay"><div class="spinner-bk"></div></div>`;

    grafikWrap.innerHTML   = spinner;
    tabelTerlaris.innerHTML = spinner;
    tabelKasir.innerHTML   = spinner;

    ['pjOmzet','pjTransaksi','pjRataRata'].forEach(id => {
        document.getElementById(id).textContent = '—';
    });

    const params = {
        bulan  : document.getElementById('pjFilterBulan').value,
        tahun  : document.getElementById('pjFilterTahun').value,
        id_toko: document.getElementById('pjFilterToko').value,
    };

    try {
        const res = await loadData('{{ route('owner.data.laporan-penjualan') }}', params);

        if (!res.success) {
            [grafikWrap, tabelTerlaris, tabelKasir].forEach(el => {
                el.innerHTML = `<div class="loading-overlay text-muted"><i class="bi bi-database-x fs-2"></i><span>Data belum tersedia.</span></div>`;
            });
            return;
        }

        // Summary
        const hariAktif  = res.per_hari.filter(h => h.total_omzet > 0).length;
        const rataPerHari = hariAktif > 0 ? Math.round(res.total_omzet / hariAktif) : 0;
        document.getElementById('pjOmzet').textContent     = formatRupiah(res.total_omzet);
        document.getElementById('pjTransaksi').textContent  = res.total_transaksi;
        document.getElementById('pjRataRata').textContent   = formatRupiah(rataPerHari);

        // Grafik
        if (res.per_hari.length > 0) {
            grafikWrap.innerHTML = '<canvas id="grafikHarian" height="200"></canvas>';
            if (grafikChart) grafikChart.destroy();
            grafikChart = new Chart(document.getElementById('grafikHarian'), {
                type: 'bar',
                data: {
                    labels  : res.per_hari.map(h => h.tanggal),
                    datasets: [{
                        label: 'Omzet',
                        data : res.per_hari.map(h => h.total_omzet),
                        backgroundColor: 'rgba(16,185,129,.55)',
                        borderColor    : 'rgba(16,185,129,1)',
                        borderWidth: 1, borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: ctx => formatRupiah(ctx.raw) } }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: v => 'Rp ' + (v/1000).toLocaleString('id-ID') + 'rb', font: { size: 10 } },
                            grid: { color: '#f1f5f9' }
                        },
                        x: { grid: { display: false }, ticks: { font: { size: 10 }, maxRotation: 45 } }
                    }
                }
            });
        } else {
            grafikWrap.innerHTML = `<div class="loading-overlay text-muted"><i class="bi bi-inbox fs-2"></i><span>Tidak ada data.</span></div>`;
        }

        // Produk terlaris
        if (res.per_produk_terlaris.length > 0) {
            let rows = '';
            res.per_produk_terlaris.forEach((p, i) => {
                rows += `<tr>
                    <td class="text-center text-muted text-xs">${i+1}</td>
                    <td class="fw-semibold text-sm">${p.nama_produk}</td>
                    <td class="text-center"><span class="badge bg-primary">${p.total_qty}</span></td>
                    <td class="fw-semibold text-success text-sm">${formatRupiah(p.total_omzet)}</td>
                </tr>`;
            });
            tabelTerlaris.innerHTML = `<div class="table-responsive"><table class="table align-middle mb-0">
                <thead><tr><th>#</th><th>Produk</th><th class="text-center">Qty</th><th>Omzet</th></tr></thead>
                <tbody>${rows}</tbody></table></div>`;
        } else {
            tabelTerlaris.innerHTML = `<div class="loading-overlay text-muted"><i class="bi bi-inbox fs-2"></i><span>Tidak ada data.</span></div>`;
        }

        // Per kasir
        if (res.per_pegawai.length > 0) {
            let rows = '';
            res.per_pegawai.forEach((p, i) => {
                rows += `<tr>
                    <td class="text-center text-muted text-xs">${i+1}</td>
                    <td class="fw-semibold text-sm">${p.nama_pegawai}</td>
                    <td class="text-center">${p.jumlah_transaksi}</td>
                    <td class="fw-semibold text-success text-sm">${formatRupiah(p.total_omzet)}</td>
                </tr>`;
            });
            tabelKasir.innerHTML = `<div class="table-responsive"><table class="table align-middle mb-0">
                <thead><tr><th>#</th><th>Kasir</th><th class="text-center">Transaksi</th><th>Omzet</th></tr></thead>
                <tbody>${rows}</tbody></table></div>`;
        } else {
            tabelKasir.innerHTML = `<div class="loading-overlay text-muted"><i class="bi bi-inbox fs-2"></i><span>Tidak ada data.</span></div>`;
        }

    } catch(e) {
        [grafikWrap, tabelTerlaris, tabelKasir].forEach(el => {
            el.innerHTML = `<div class="loading-overlay text-danger"><i class="bi bi-wifi-off fs-2"></i><span>Gagal memuat.</span></div>`;
        });
    }
}
</script>
@endpush