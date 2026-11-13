{{-- resources/views/owner/produk.blade.php --}}
{{-- STATUS: BARU --}}
{{-- Halaman produk & stok — READ ONLY dari website. --}}
{{-- Data diambil via AJAX ke /owner/data/produk dan /owner/data/stok --}}

@extends('owner.layout')

@section('title', 'Produk & Stok')
@section('page-title', 'Produk & Stok')
@section('page-sub', 'Pantau stok dan daftar produk toko Anda')

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
    Untuk menambah atau mengubah produk, gunakan aplikasi <strong>Bathiku Kasir</strong> di perangkat Android Anda.</span>
</div>

{{-- ── Kartu Summary ────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card blue">
            <div class="stat-icon"><i class="bi bi-box-seam-fill"></i></div>
            <div class="stat-value" id="totalProduk">—</div>
            <div class="stat-label">Total Produk Aktif</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card amber" id="cardHampir">
            <div class="stat-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="stat-value" id="totalHampirHabis">—</div>
            <div class="stat-label">Stok Hampir Habis (≤5)</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card red" id="cardHabis">
            <div class="stat-icon"><i class="bi bi-x-circle-fill"></i></div>
            <div class="stat-value" id="totalStokHabis">—</div>
            <div class="stat-label">Stok Habis (= 0)</div>
        </div>
    </div>
</div>

{{-- ── Filter ────────────────────────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="row g-2 align-items-end">
            <div class="col-sm-4 col-lg-3">
                <label class="form-label">Kategori</label>
                <select id="filterKategori" class="form-select">
                    <option value="">Semua Kategori</option>
                </select>
            </div>
            <div class="col-sm-4 col-lg-3">
                <label class="form-label">Cari Nama Produk</label>
                <input type="text" id="filterSearch" class="form-control"
                       placeholder="Ketik nama produk...">
            </div>
            <div class="col-sm-4 col-lg-2">
                <label class="form-label">Filter Stok</label>
                <select id="filterStok" class="form-select">
                    <option value="">Semua</option>
                    <option value="hampir_habis">Hampir Habis (≤5)</option>
                    <option value="habis">Stok Habis (0)</option>
                </select>
            </div>
            <div class="col-sm-4 col-lg-2">
                <label class="form-label">Outlet</label>
                <select id="filterToko" class="form-select">
                    <option value="">Semua</option>
                </select>
            </div>
            <div class="col-sm-8 col-lg-2">
                <div class="d-flex gap-2">
                    <button class="btn btn-success flex-grow-1" onclick="loadProduk(1)">
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

{{-- ── Tabel Produk ─────────────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6><i class="bi bi-table me-2 text-primary"></i>Daftar Produk</h6>
        <span class="text-muted text-xs" id="infoTotal">-</span>
    </div>
    <div id="tabelProdukWrap">
        <div class="loading-overlay">
            <div class="spinner-bk"></div>
            <span class="text-muted text-sm">Memuat data produk...</span>
        </div>
    </div>
    <div class="card-body border-top py-2 d-flex align-items-center justify-content-between"
         id="paginasiWrap" style="display:none!important">
        <span class="text-muted text-xs" id="paginasiInfo"></span>
        <div id="paginasiBtn" class="d-flex gap-1"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const LIMIT = 50;
let currentPage = 1;
let totalData   = 0;

document.addEventListener('DOMContentLoaded', async () => {
    // Load kategori & toko untuk dropdown
    try {
        const [resProduk, resToko] = await Promise.all([
            loadData('{{ route('owner.data.produk') }}', { limit: 1, offset: 0 }),
            loadData('{{ route('owner.data.toko-list') }}'),
        ]);

        if (resProduk.success && resProduk.kategori_list) {
            const sel = document.getElementById('filterKategori');
            resProduk.kategori_list.forEach(k => {
                const opt = document.createElement('option');
                opt.value = k; opt.textContent = k;
                sel.appendChild(opt);
            });
        }

        if (resToko.success && resToko.data.length > 1) {
            const sel = document.getElementById('filterToko');
            resToko.data.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id_toko; opt.textContent = t.nama_toko;
                sel.appendChild(opt);
            });
        }
    } catch(e) {}

    // Load stok summary
    try {
        const resStok = await loadData('{{ route('owner.data.stok') }}', { limit: 1 });
        if (resStok.success) {
            document.getElementById('totalHampirHabis').textContent = resStok.stok_hampir_habis.length;
            // Count stok 0
            const resHabis = await loadData('{{ route('owner.data.produk') }}', { stok_filter: 'habis', limit: 1 });
            document.getElementById('totalStokHabis').textContent   = resHabis.success ? resHabis.total : '-';
        }
    } catch(e) {}

    await loadProduk(1);
});

function resetFilter() {
    document.getElementById('filterKategori').value = '';
    document.getElementById('filterSearch').value   = '';
    document.getElementById('filterStok').value     = '';
    document.getElementById('filterToko').value     = '';
    loadProduk(1);
}

// Enter untuk cari
document.getElementById('filterSearch')
    ?.addEventListener('keydown', e => { if (e.key === 'Enter') loadProduk(1); });

async function loadProduk(page) {
    currentPage = page;
    const wrap  = document.getElementById('tabelProdukWrap');
    wrap.innerHTML = `<div class="loading-overlay"><div class="spinner-bk"></div><span class="text-muted text-sm">Memuat...</span></div>`;
    document.getElementById('paginasiWrap').style.display = 'none';

    const params = {
        kategori   : document.getElementById('filterKategori').value,
        search     : document.getElementById('filterSearch').value,
        stok_filter: document.getElementById('filterStok').value,
        id_toko    : document.getElementById('filterToko').value,
        limit      : LIMIT,
        offset     : (page - 1) * LIMIT,
    };

    try {
        const res = await loadData('{{ route('owner.data.produk') }}', params);

        if (!res.success) {
            wrap.innerHTML = `<div class="loading-overlay text-muted"><i class="bi bi-database-x fs-2"></i><span>Data belum tersedia.</span></div>`;
            return;
        }

        totalData = res.total;
        document.getElementById('infoTotal').textContent = res.total + ' produk';
        document.getElementById('totalProduk').textContent = res.total;

        if (res.data.length === 0) {
            wrap.innerHTML = `<div class="loading-overlay text-muted"><i class="bi bi-inbox fs-2"></i><span>Tidak ada produk yang sesuai filter.</span></div>`;
            return;
        }

        let rows = '';
        res.data.forEach((p, i) => {
            const no       = (page - 1) * LIMIT + i + 1;
            const isHabis  = p.stok === 0;
            const isHampir = p.stok > 0 && p.stok <= 5;
            const rowClass = isHabis ? 'row-danger' : (isHampir ? 'row-warning' : '');

            rows += `<tr class="${rowClass}">
                <td class="text-center text-muted text-xs">${no}</td>
                <td>
                    <div class="fw-semibold text-sm">${p.nama_produk}</div>
                    <div class="text-muted text-xs">${p.kode_produk}</div>
                </td>
                <td><span class="badge bg-light text-dark border text-xs">${p.kategori || '-'}</span></td>
                <td class="text-xs text-muted">${p.sku || '-'}</td>
                <td class="fw-semibold">${formatRupiah(p.harga_jual)}</td>
                <td>${p.harga_diskon > 0 ? '<span class="text-success fw-semibold">' + formatRupiah(p.harga_diskon) + '</span>' : '<span class="text-muted">-</span>'}</td>
                <td class="text-center">
                    <span class="badge rounded-pill px-3 ${isHabis ? 'bg-danger' : (isHampir ? 'bg-warning text-dark' : 'bg-success')}">
                        ${p.stok}
                    </span>
                </td>
            </tr>`;
        });

        wrap.innerHTML = `
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:40px">#</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>SKU</th>
                            <th>Harga Jual</th>
                            <th>Harga Diskon</th>
                            <th class="text-center">Stok</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>`;

        renderPaginasi(totalData, page);

    } catch(e) {
        wrap.innerHTML = `<div class="loading-overlay text-danger"><i class="bi bi-wifi-off fs-2"></i><span>Gagal memuat data.</span></div>`;
    }
}

function renderPaginasi(total, page) {
    const totalPage = Math.ceil(total / LIMIT);
    const wrap = document.getElementById('paginasiWrap');
    if (totalPage <= 1) { wrap.style.display = 'none'; return; }

    wrap.style.display = '';
    const dari = (page-1)*LIMIT+1, hingga = Math.min(page*LIMIT, total);
    document.getElementById('paginasiInfo').textContent = `${dari}–${hingga} dari ${total}`;

    let btns = `<button class="btn btn-sm btn-outline-secondary" ${page===1?'disabled':''} onclick="loadProduk(${page-1})"><i class="bi bi-chevron-left"></i></button>`;
    const start = Math.max(1,page-2), end = Math.min(totalPage,page+2);
    if (start>1) btns += `<button class="btn btn-sm btn-outline-secondary" onclick="loadProduk(1)">1</button>`;
    if (start>2) btns += `<span class="btn btn-sm disabled">…</span>`;
    for (let p=start;p<=end;p++) btns += `<button class="btn btn-sm ${p===page?'btn-success':'btn-outline-secondary'}" onclick="loadProduk(${p})">${p}</button>`;
    if (end<totalPage-1) btns += `<span class="btn btn-sm disabled">…</span>`;
    if (end<totalPage) btns += `<button class="btn btn-sm btn-outline-secondary" onclick="loadProduk(${totalPage})">${totalPage}</button>`;
    btns += `<button class="btn btn-sm btn-outline-secondary" ${page===totalPage?'disabled':''} onclick="loadProduk(${page+1})"><i class="bi bi-chevron-right"></i></button>`;

    document.getElementById('paginasiBtn').innerHTML = btns;
}
</script>
@endpush