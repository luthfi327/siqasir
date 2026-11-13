{{-- resources/views/owner/kas_keluar.blade.php --}}
{{-- STATUS: BARU — Pengganti halaman Pelanggan --}}
{{-- Data diambil via AJAX ke /owner/data/kas-keluar --}}

@extends('owner.layout')

@section('title', 'Kas Keluar')
@section('page-title', 'Kas Keluar')
@section('page-sub', 'Rincian pengeluaran kas dari setiap sesi kasir')

@section('content')

@if(!$sqliteAda)
<div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
    <div><strong>Data belum tersedia.</strong> Lakukan tutup kasir dan backup dari aplikasi Android.</div>
</div>
@endif

{{-- Info read-only --}}
<div class="alert alert-info d-flex align-items-center gap-2 mb-4 py-2">
    <i class="bi bi-info-circle-fill flex-shrink-0"></i>
    <span class="text-sm">Halaman ini bersifat <strong>hanya lihat</strong>.
    Pencatatan kas keluar dilakukan melalui aplikasi <strong>Bathiku Kasir</strong>
    saat kasir sedang terbuka.</span>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card amber">
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-value text-sm" id="statTotalNominal">—</div>
            <div class="stat-label">Total Kas Keluar</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card blue">
            <div class="stat-icon"><i class="bi bi-journal-check"></i></div>
            <div class="stat-value text-sm" id="statTotalTransaksi">—</div>
            <div class="stat-label">Total Transaksi Keluar</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card red">
            <div class="stat-icon"><i class="bi bi-graph-down-arrow"></i></div>
            <div class="stat-value text-sm" id="statRataRata">—</div>
            <div class="stat-label">Rata-rata per Transaksi</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="row g-2 align-items-end">
            <div class="col-sm-4 col-lg-3">
                <label class="form-label">Tanggal Dari</label>
                <input type="date" id="filterDari" class="form-control"
                       value="{{ now()->startOfMonth()->format('Y-m-d') }}">
            </div>
            <div class="col-sm-4 col-lg-3">
                <label class="form-label">Tanggal Hingga</label>
                <input type="date" id="filterHingga" class="form-control"
                       value="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="col-sm-4 col-lg-2">
                <label class="form-label">Outlet</label>
                <select id="filterToko" class="form-select">
                    <option value="">Semua Outlet</option>
                </select>
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="d-flex gap-2">
                    <button class="btn btn-success flex-grow-1" onclick="loadKasKeluar(1)">
                        <i class="bi bi-search me-1"></i> Tampilkan
                    </button>
                    <button class="btn btn-outline-secondary" onclick="resetFilter()">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6><i class="bi bi-table me-2 text-primary"></i>Rincian Kas Keluar</h6>
        <span class="text-muted text-xs" id="infoTotal">-</span>
    </div>
    <div id="tabelKasKeluarWrap">
        <div class="loading-overlay">
            <div class="spinner-bk"></div>
            <span class="text-muted text-sm">Memuat data kas keluar...</span>
        </div>
    </div>
    <div class="card-body border-top py-2 d-flex align-items-center justify-content-between"
         id="paginasiWrap" style="display:none!important">
        <span class="text-muted text-xs" id="paginasiInfo"></span>
        <div id="paginasiBtn" class="d-flex gap-1 flex-wrap"></div>
    </div>
</div>

@endsection

<script>
const LIMIT     = 25;
let currentPage = 1;
let totalData   = 0;
let outletList  = [];

document.addEventListener('DOMContentLoaded', async () => {
    // Load daftar outlet untuk dropdown
    try {
        const res = await loadData('{{ route('owner.data.toko-list') }}');
        if (res.success && res.data.length > 0) {
            outletList = res.data;
            if (res.data.length > 1) {
                const sel = document.getElementById('filterToko');
                res.data.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.id_toko;
                    opt.textContent = t.nama_toko;
                    sel.appendChild(opt);
                });
            }
        }
    } catch(e) {}

    await loadKasKeluar(1);
});

// Enter untuk tampilkan
document.getElementById('filterDari')?.addEventListener('keydown',   e => { if (e.key === 'Enter') loadKasKeluar(1); });
document.getElementById('filterHingga')?.addEventListener('keydown', e => { if (e.key === 'Enter') loadKasKeluar(1); });

function resetFilter() {
    document.getElementById('filterDari').value    = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().slice(0,10);
    document.getElementById('filterHingga').value  = new Date().toISOString().slice(0,10);
    document.getElementById('filterToko').value    = '';
    loadKasKeluar(1);
}

function rupiah(angka) {
    return 'Rp ' + parseInt(angka || 0).toLocaleString('id-ID');
}

async function loadKasKeluar(page) {
    currentPage = page;
    const wrap  = document.getElementById('tabelKasKeluarWrap');
    wrap.innerHTML = `<div class="loading-overlay"><div class="spinner-bk"></div>
        <span class="text-muted text-sm">Memuat...</span></div>`;
    document.getElementById('paginasiWrap').style.display = 'none';

    // Reset stat sementara loading
    ['statTotalNominal','statTotalTransaksi','statRataRata'].forEach(id => {
        document.getElementById(id).textContent = '—';
    });

    const params = {
        tgl_dari  : document.getElementById('filterDari').value,
        tgl_hingga: document.getElementById('filterHingga').value,
        id_toko   : document.getElementById('filterToko').value,
        limit     : LIMIT,
        offset    : (page - 1) * LIMIT,
    };

    try {
        const res = await loadData('{{ route('owner.data.kas-keluar') }}', params);

        if (!res.success) {
            wrap.innerHTML = `<div class="loading-overlay text-muted">
                <i class="bi bi-database-x fs-2"></i>
                <span>Data belum tersedia. Lakukan backup dari aplikasi Android.</span>
            </div>`;
            return;
        }

        // Update stat cards
        document.getElementById('statTotalNominal').textContent    = rupiah(res.total_nominal);
        document.getElementById('statTotalTransaksi').textContent  = (res.total || 0) + ' transaksi';
        document.getElementById('statRataRata').textContent        = rupiah(res.rata_rata);

        totalData = res.total;
        document.getElementById('infoTotal').textContent = (res.total || 0) + ' transaksi ditemukan';

        if (!res.data || res.data.length === 0) {
            wrap.innerHTML = `<div class="loading-overlay text-muted">
                <i class="bi bi-inbox fs-2"></i>
                <span>Tidak ada data kas keluar pada periode ini.</span>
            </div>`;
            return;
        }

        // Cek apakah multi-outlet (tampilkan kolom outlet atau tidak)
        const isMultiOutlet = outletList.length > 1;

        let rows = '';
        res.data.forEach((k, i) => {
            const no     = (page - 1) * LIMIT + i + 1;
            const tgl    = k.tanggal   ? formatTanggal(k.tanggal) : '-';
            const waktu  = k.waktu     || '';
            const kasir  = k.nama_pegawai || k.kode_pegawai || '-';
            const keperluan = k.keperluan || '-';
            const nominal   = rupiah(k.nominal);
            const sesi   = k.kode_rekapkas
                ? `<span class="badge bg-light text-secondary fw-normal" style="font-size:.72rem;letter-spacing:.3px">${k.kode_rekapkas}</span>`
                : '-';

            rows += `<tr>
                <td class="text-center text-muted text-xs">${no}</td>
                <td>
                    <div class="fw-semibold text-sm">${tgl}</div>
                    <div class="text-muted text-xs">${waktu}</div>
                </td>
                <td>${sesi}</td>
                <td class="text-sm">${kasir}</td>
                <td class="text-sm">${keperluan}</td>
                <td class="fw-semibold text-sm" style="color:#d97706">${nominal}</td>
                ${isMultiOutlet ? `<td class="text-sm text-muted">${namaOutlet(k.id_toko)}</td>` : ''}
            </tr>`;
        });

        wrap.innerHTML = `
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:40px">#</th>
                            <th>Tanggal</th>
                            <th>Sesi Kasir</th>
                            <th>Kasir</th>
                            <th>Keperluan</th>
                            <th>Nominal</th>
                            ${isMultiOutlet ? '<th>Outlet</th>' : ''}
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>`;

        renderPaginasi(totalData, page);

    } catch(e) {
        wrap.innerHTML = `<div class="loading-overlay text-danger">
            <i class="bi bi-wifi-off fs-2"></i>
            <span>Gagal memuat data kas keluar.</span>
        </div>`;
        console.error(e);
    }
}

function namaOutlet(idToko) {
    const found = outletList.find(t => t.id_toko == idToko);
    return found ? found.nama_toko : ('Outlet ' + (idToko || '-'));
}

function formatTanggal(str) {
    // Format dari SQLite: dd-MM-yyyy atau dd-MM-yy
    if (!str) return '-';
    const parts = str.split('-');
    if (parts.length !== 3) return str;
    const bulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
    const d = parseInt(parts[0]);
    const m = parseInt(parts[1]) - 1;
    const y = parts[2].length === 2 ? '20' + parts[2] : parts[2];
    return d + ' ' + (bulan[m] || parts[1]) + ' ' + y;
}

function renderPaginasi(total, page) {
    const totalPage = Math.ceil(total / LIMIT);
    const wrap      = document.getElementById('paginasiWrap');
    if (totalPage <= 1) { wrap.style.display = 'none'; return; }

    wrap.style.display = '';
    const dari    = (page - 1) * LIMIT + 1;
    const hingga  = Math.min(page * LIMIT, total);
    document.getElementById('paginasiInfo').textContent = `${dari}–${hingga} dari ${total}`;

    let btns = `<button class="btn btn-sm btn-outline-secondary" ${page===1?'disabled':''} onclick="loadKasKeluar(${page-1})">
        <i class="bi bi-chevron-left"></i></button>`;
    const start = Math.max(1, page - 2);
    const end   = Math.min(totalPage, page + 2);
    if (start > 1) btns += `<button class="btn btn-sm btn-outline-secondary" onclick="loadKasKeluar(1)">1</button>`;
    if (start > 2) btns += `<span class="btn btn-sm disabled">…</span>`;
    for (let p = start; p <= end; p++) {
        btns += `<button class="btn btn-sm ${p===page?'btn-success':'btn-outline-secondary'}" onclick="loadKasKeluar(${p})">${p}</button>`;
    }
    if (end < totalPage - 1) btns += `<span class="btn btn-sm disabled">…</span>`;
    if (end < totalPage)     btns += `<button class="btn btn-sm btn-outline-secondary" onclick="loadKasKeluar(${totalPage})">${totalPage}</button>`;
    btns += `<button class="btn btn-sm btn-outline-secondary" ${page===totalPage?'disabled':''} onclick="loadKasKeluar(${page+1})">
        <i class="bi bi-chevron-right"></i></button>`;

    document.getElementById('paginasiBtn').innerHTML = btns;
}
</script>