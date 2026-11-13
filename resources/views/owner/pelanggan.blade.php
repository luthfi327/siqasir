{{-- resources/views/owner/pelanggan.blade.php --}}
{{-- STATUS: BARU --}}
{{-- Halaman daftar pelanggan — READ ONLY dari website. --}}
{{-- Data diambil via AJAX ke /owner/data/pelanggan --}}

@extends('owner.layout')

@section('title', 'Pelanggan')
@section('page-title', 'Daftar Pelanggan')
@section('page-sub', 'Data pelanggan yang terdaftar di toko Anda')

@section('content')

@if(!$sqliteAda)
<div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
    <div><strong>Data belum tersedia.</strong> Lakukan tutup kasir dan backup dari aplikasi Android.</div>
</div>
@endif

{{-- ── Info read-only ───────────────────────────────────────────────────── --}}
<div class="alert alert-info d-flex align-items-center gap-2 mb-4 py-2">
    <i class="bi bi-info-circle-fill flex-shrink-0"></i>
    <span class="text-sm">Halaman ini bersifat <strong>hanya lihat</strong>.
    Penambahan dan pengelolaan pelanggan dilakukan melalui aplikasi <strong>Bathiku Kasir</strong>.</span>
</div>

{{-- ── Kartu Summary ────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card blue">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-value" id="totalPelanggan">—</div>
            <div class="stat-label">Total Pelanggan Terdaftar</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card green">
            <div class="stat-icon"><i class="bi bi-person-check-fill"></i></div>
            <div class="stat-value" id="totalHasilFilter">—</div>
            <div class="stat-label">Hasil Pencarian</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card purple">
            <div class="stat-icon"><i class="bi bi-shop-window"></i></div>
            <div class="stat-value" id="totalOutlet">—</div>
            <div class="stat-label">Outlet Terdaftar</div>
        </div>
    </div>
</div>

{{-- ── Filter ────────────────────────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="row g-2 align-items-end">
            <div class="col-sm-5 col-lg-5">
                <label class="form-label">Cari Nama / No HP</label>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" id="filterSearch" class="form-control"
                           placeholder="Ketik nama atau nomor HP...">
                </div>
            </div>
            <div class="col-sm-3 col-lg-3">
                <label class="form-label">Outlet</label>
                <select id="filterToko" class="form-select">
                    <option value="">Semua Outlet</option>
                </select>
            </div>
            <div class="col-sm-4 col-lg-4">
                <div class="d-flex gap-2">
                    <button class="btn btn-success flex-grow-1" onclick="loadPelanggan(1)">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    <button class="btn btn-outline-secondary" onclick="resetFilter()">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Tabel Pelanggan ──────────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6><i class="bi bi-table me-2 text-primary"></i>Daftar Pelanggan</h6>
        <span class="text-muted text-xs" id="infoTotal">-</span>
    </div>
    <div id="tabelPelangganWrap">
        <div class="loading-overlay">
            <div class="spinner-bk"></div>
            <span class="text-muted text-sm">Memuat data pelanggan...</span>
        </div>
    </div>
    <div class="card-body border-top py-2 d-flex align-items-center justify-content-between"
         id="paginasiWrap" style="display:none!important">
        <span class="text-muted text-xs" id="paginasiInfo"></span>
        <div id="paginasiBtn" class="d-flex gap-1 flex-wrap"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const LIMIT     = 25;
let currentPage = 1;
let totalData   = 0;

document.addEventListener('DOMContentLoaded', async () => {
    // Load daftar outlet
    try {
        const res = await loadData('{{ route('owner.data.toko-list') }}');
        if (res.success) {
            document.getElementById('totalOutlet').textContent = res.data.length;
            if (res.data.length > 1) {
                const sel = document.getElementById('filterToko');
                res.data.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.id_toko; opt.textContent = t.nama_toko;
                    sel.appendChild(opt);
                });
            }
        }
    } catch(e) {}

    await loadPelanggan(1);
});

// Enter untuk cari
document.getElementById('filterSearch')
    ?.addEventListener('keydown', e => { if (e.key === 'Enter') loadPelanggan(1); });

function resetFilter() {
    document.getElementById('filterSearch').value = '';
    document.getElementById('filterToko').value   = '';
    loadPelanggan(1);
}

async function loadPelanggan(page) {
    currentPage = page;
    const wrap  = document.getElementById('tabelPelangganWrap');
    wrap.innerHTML = `<div class="loading-overlay"><div class="spinner-bk"></div><span class="text-muted text-sm">Memuat...</span></div>`;
    document.getElementById('paginasiWrap').style.display = 'none';

    const params = {
        search : document.getElementById('filterSearch').value,
        id_toko: document.getElementById('filterToko').value,
        limit  : LIMIT,
        offset : (page - 1) * LIMIT,
    };

    try {
        const res = await loadData('{{ route('owner.data.pelanggan') }}', params);

        if (!res.success) {
            wrap.innerHTML = `<div class="loading-overlay text-muted">
                <i class="bi bi-database-x fs-2"></i>
                <span>Data belum tersedia. Lakukan backup dari aplikasi Android.</span>
            </div>`;
            return;
        }

        totalData = res.total;
        document.getElementById('infoTotal').textContent          = res.total + ' pelanggan ditemukan';
        document.getElementById('totalHasilFilter').textContent   = res.total;

        // Jika tidak ada filter, update total keseluruhan
        if (!params.search && !params.id_toko) {
            document.getElementById('totalPelanggan').textContent = res.total;
        }

        if (res.data.length === 0) {
            wrap.innerHTML = `<div class="loading-overlay text-muted">
                <i class="bi bi-inbox fs-2"></i>
                <span>Tidak ada pelanggan yang sesuai pencarian.</span>
            </div>`;
            return;
        }

        let rows = '';
        res.data.forEach((p, i) => {
            const no = (page - 1) * LIMIT + i + 1;

            // Format gender
            const genderLabel = p.gender === 'L' ? '<span class="badge bg-primary text-white">L</span>'
                : (p.gender === 'P' ? '<span class="badge bg-pink" style="background:#ec4899!important">P</span>'
                : '<span class="text-muted">-</span>');

            rows += `<tr>
                <td class="text-center text-muted text-xs">${no}</td>
                <td>
                    <div class="fw-semibold text-sm">${p.nama_pelanggan}</div>
                    <div class="text-muted text-xs">${p.kode_pelanggan || ''}</div>
                </td>
                <td class="text-sm">${p.no_hp || '-'}</td>
                <td class="text-sm">${p.kota || '-'}</td>
                <td class="text-sm text-muted" style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                    title="${p.alamat || ''}">${p.alamat || '-'}</td>
                <td>${genderLabel}</td>
                <td class="text-sm text-muted">
                    ${p.catatan
                        ? `<span title="${p.catatan}">${p.catatan.substring(0, 30)}${p.catatan.length > 30 ? '…' : ''}</span>`
                        : '-'}
                </td>
            </tr>`;
        });

        wrap.innerHTML = `
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:40px">#</th>
                            <th>Nama Pelanggan</th>
                            <th>No HP</th>
                            <th>Kota</th>
                            <th>Alamat</th>
                            <th>Gender</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>`;

        renderPaginasi(totalData, page);

    } catch(e) {
        wrap.innerHTML = `<div class="loading-overlay text-danger">
            <i class="bi bi-wifi-off fs-2"></i>
            <span>Gagal memuat data pelanggan.</span>
        </div>`;
        console.error(e);
    }
}

function renderPaginasi(total, page) {
    const totalPage = Math.ceil(total / LIMIT);
    const wrap = document.getElementById('paginasiWrap');
    if (totalPage <= 1) { wrap.style.display = 'none'; return; }

    wrap.style.display = '';
    const dari = (page-1)*LIMIT+1, hingga = Math.min(page*LIMIT, total);
    document.getElementById('paginasiInfo').textContent = `${dari}–${hingga} dari ${total}`;

    let btns = `<button class="btn btn-sm btn-outline-secondary" ${page===1?'disabled':''} onclick="loadPelanggan(${page-1})"><i class="bi bi-chevron-left"></i></button>`;
    const start = Math.max(1,page-2), end = Math.min(totalPage,page+2);
    if (start>1) btns += `<button class="btn btn-sm btn-outline-secondary" onclick="loadPelanggan(1)">1</button>`;
    if (start>2) btns += `<span class="btn btn-sm disabled">…</span>`;
    for (let p=start; p<=end; p++) {
        btns += `<button class="btn btn-sm ${p===page?'btn-success':'btn-outline-secondary'}" onclick="loadPelanggan(${p})">${p}</button>`;
    }
    if (end<totalPage-1) btns += `<span class="btn btn-sm disabled">…</span>`;
    if (end<totalPage) btns += `<button class="btn btn-sm btn-outline-secondary" onclick="loadPelanggan(${totalPage})">${totalPage}</button>`;
    btns += `<button class="btn btn-sm btn-outline-secondary" ${page===totalPage?'disabled':''} onclick="loadPelanggan(${page+1})"><i class="bi bi-chevron-right"></i></button>`;

    document.getElementById('paginasiBtn').innerHTML = btns;
}
</script>
@endpush