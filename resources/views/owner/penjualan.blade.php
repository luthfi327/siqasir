{{-- resources/views/owner/penjualan.blade.php --}}
{{-- STATUS: MODIFIKASI --}}
{{-- Perubahan REVISI: --}}
{{--   - Tambah kolom Laba di tabel transaksi --}}
{{--   - Modal detail: tampilkan laba per item + total laba + info diskon lengkap --}}
{{--   - Kartu summary: tambah Total Laba --}}
{{--   - Tabel: tambah kolom Diskon dan Laba di setiap baris --}}

@extends('owner.layout')

@section('title', 'Data Penjualan')
@section('page-title', 'Data Penjualan')
@section('page-sub', 'Riwayat transaksi toko Anda')

@section('content')

@if(!$sqliteAda)
<div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
    <div><strong>Data belum tersedia.</strong> Lakukan tutup kasir dan backup dari aplikasi Android.</div>
</div>
@endif

{{-- ── Kartu Summary (5 kartu termasuk laba) ───────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg">
        <div class="stat-card green">
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-value" id="summaryOmzet">—</div>
            <div class="stat-label">Total Omzet</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="stat-card blue">
            <div class="stat-icon"><i class="bi bi-receipt"></i></div>
            <div class="stat-value" id="summaryTransaksi">—</div>
            <div class="stat-label">Total Transaksi</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="stat-card purple">
            <div class="stat-icon"><i class="bi bi-graph-up"></i></div>
            <div class="stat-value" id="summaryRataRata">—</div>
            <div class="stat-label">Rata-rata / Transaksi</div>
        </div>
    </div>
    {{-- [REVISI] Tambah kartu Total Laba --}}
    <div class="col-sm-6 col-lg" id="cardLaba">
        <div class="stat-card green">
            <div class="stat-icon"><i class="bi bi-trending-up"></i></div>
            <div class="stat-value" id="summaryLaba">—</div>
            <div class="stat-label">Total Laba</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="stat-card amber">
            <div class="stat-icon"><i class="bi bi-calendar-day"></i></div>
            <div class="stat-value" id="summaryHari">—</div>
            <div class="stat-label">Hari Dipilih</div>
        </div>
    </div>
</div>

{{-- ── Filter ────────────────────────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="row g-2 align-items-end">
            <div class="col-sm-4 col-lg-3">
                <label class="form-label">Tanggal Dari</label>
                <input type="date" id="filterDari" class="form-control"
                       value="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="col-sm-4 col-lg-3">
                <label class="form-label">Tanggal Hingga</label>
                <input type="date" id="filterHingga" class="form-control"
                       value="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="col-sm-4 col-lg-3">
                <label class="form-label">Outlet / Toko</label>
                <select id="filterToko" class="form-select">
                    <option value="">Semua Outlet</option>
                </select>
            </div>
            <div class="col-sm-12 col-lg-3">
                <div class="d-flex gap-2">
                    <button class="btn btn-success flex-grow-1" onclick="loadPenjualan(1)">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    <button class="btn btn-outline-secondary" onclick="resetFilter()">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2 mt-2 flex-wrap">
            <span class="text-muted text-xs me-1 align-self-center">Cepat:</span>
            <button class="btn btn-sm btn-outline-secondary" onclick="setRangeHariIni()">Hari Ini</button>
            <button class="btn btn-sm btn-outline-secondary" onclick="setRange7Hari()">7 Hari</button>
            <button class="btn btn-sm btn-outline-secondary" onclick="setRangeBulanIni()">Bulan Ini</button>
            <button class="btn btn-sm btn-outline-secondary" onclick="setRangeBulanLalu()">Bulan Lalu</button>
        </div>
    </div>
</div>

{{-- ── Tabel Penjualan ──────────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6><i class="bi bi-table me-2 text-primary"></i>Daftar Transaksi</h6>
        <span class="text-muted text-xs" id="infoTotal">-</span>
    </div>
    <div id="tabelPenjualanWrap">
        <div class="loading-overlay">
            <div class="spinner-bk"></div>
            <span class="text-muted text-sm">Memuat data...</span>
        </div>
    </div>
    <div class="card-body border-top py-2 d-flex align-items-center justify-content-between"
         id="paginasiWrap" style="display:none!important">
        <span class="text-muted text-xs" id="paginasiInfo"></span>
        <div id="paginasiBtn" class="d-flex gap-1"></div>
    </div>
</div>

{{-- ── Modal Detail Transaksi ──────────────────────────────────────────── --}}
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-receipt me-2 text-primary"></i>
                    Detail Transaksi — <span id="modalInvoice"></span>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <div class="loading-overlay">
                    <div class="spinner-bk"></div>
                    <span class="text-sm text-muted">Memuat detail...</span>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const LIMIT     = 25;
let currentPage = 1;
let totalData   = 0;
let modalBs     = null;

document.addEventListener('DOMContentLoaded', async () => {
    modalBs = new bootstrap.Modal(document.getElementById('modalDetail'));

    try {
        const res = await loadData('{{ route('owner.data.toko-list') }}');
        const sel = document.getElementById('filterToko');
        if (res.success && res.data.length > 1) {
            res.data.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id_toko; opt.textContent = t.nama_toko;
                sel.appendChild(opt);
            });
        }
    } catch(e) {}

    await loadPenjualan(1);
});

function fmtDate(d) { return d.toISOString().split('T')[0]; }
function setRangeHariIni() {
    const t = fmtDate(new Date());
    document.getElementById('filterDari').value = t;
    document.getElementById('filterHingga').value = t;
    loadPenjualan(1);
}
function setRange7Hari() {
    const hingga = new Date(), dari = new Date();
    dari.setDate(dari.getDate() - 6);
    document.getElementById('filterDari').value   = fmtDate(dari);
    document.getElementById('filterHingga').value = fmtDate(hingga);
    loadPenjualan(1);
}
function setRangeBulanIni() {
    const now = new Date();
    document.getElementById('filterDari').value   = fmtDate(new Date(now.getFullYear(), now.getMonth(), 1));
    document.getElementById('filterHingga').value = fmtDate(new Date(now.getFullYear(), now.getMonth()+1, 0));
    loadPenjualan(1);
}
function setRangeBulanLalu() {
    const now = new Date();
    document.getElementById('filterDari').value   = fmtDate(new Date(now.getFullYear(), now.getMonth()-1, 1));
    document.getElementById('filterHingga').value = fmtDate(new Date(now.getFullYear(), now.getMonth(), 0));
    loadPenjualan(1);
}
function resetFilter() {
    const t = fmtDate(new Date());
    document.getElementById('filterDari').value   = t;
    document.getElementById('filterHingga').value = t;
    document.getElementById('filterToko').value   = '';
    loadPenjualan(1);
}

async function loadPenjualan(page) {
    currentPage = page;
    const wrap  = document.getElementById('tabelPenjualanWrap');
    wrap.innerHTML = `<div class="loading-overlay"><div class="spinner-bk"></div><span class="text-muted text-sm">Memuat...</span></div>`;
    document.getElementById('paginasiWrap').style.display = 'none';

    const params = {
        tgl_dari  : document.getElementById('filterDari').value,
        tgl_hingga: document.getElementById('filterHingga').value,
        id_toko   : document.getElementById('filterToko').value,
        limit     : LIMIT,
        offset    : (page - 1) * LIMIT,
    };

    try {
        const res = await loadData('{{ route('owner.data.penjualan') }}', params);

        if (!res.success) {
            wrap.innerHTML = `<div class="loading-overlay text-muted"><i class="bi bi-database-x fs-2"></i><span>Data belum tersedia.</span></div>`;
            return;
        }

        totalData = res.total;
        document.getElementById('infoTotal').textContent = totalData + ' transaksi';

        // [REVISI] Hitung laba total dari detail transaksi (dari field laba_total di response)
        let totalOmzet = 0, totalLaba = 0;
        res.data.forEach(r => {
            totalOmzet += Number(r.total_jml_transaksi || 0);
            totalLaba  += Number(r.total_laba || 0);
        });
        const hariUnik = new Set(res.data.map(r => r.tanggal)).size;

        document.getElementById('summaryOmzet').textContent     = formatRupiah(totalOmzet);
        document.getElementById('summaryTransaksi').textContent  = res.total;
        document.getElementById('summaryRataRata').textContent   = res.data.length ? formatRupiah(Math.round(totalOmzet/res.data.length)) : '-';
        document.getElementById('summaryLaba').textContent       = formatRupiah(totalLaba);
        document.getElementById('summaryHari').textContent       = hariUnik + ' hari';

        // Warna kartu laba: merah jika minus, hijau jika positif
        const cardLaba = document.querySelector('#cardLaba .stat-card');
        if (cardLaba) {
            cardLaba.className = cardLaba.className.replace(/\b(green|red|blue|amber|purple)\b/, '');
            cardLaba.classList.add(totalLaba < 0 ? 'red' : 'green');
        }

        if (res.data.length === 0) {
            wrap.innerHTML = `<div class="loading-overlay text-muted"><i class="bi bi-inbox fs-2"></i><span>Tidak ada transaksi pada periode ini.</span></div>`;
            return;
        }

        let rows = '';
        res.data.forEach((t, i) => {
            const no = (page - 1) * LIMIT + i + 1;
            // [REVISI] Tampilkan laba per transaksi
            const labaVal  = Number(t.total_laba || 0);
            const labaClass = labaVal < 0 ? 'text-danger' : 'text-success';

            rows += `<tr>
                <td class="text-center text-muted text-xs">${no}</td>
                <td><code class="text-xs">${t.invoice}</code></td>
                <td>
                    <div class="fw-semibold text-sm">${t.tanggal}</div>
                    <div class="text-muted text-xs">${t.waktu || ''}</div>
                </td>
                <td class="text-sm">${t.nama_pegawai || '-'}</td>
                <td><span class="badge bg-light text-dark border text-xs">${t.jenis_kedatangan || '-'}</span></td>
                <td><span class="badge bg-light text-dark border text-xs">${t.jenis_pembayaran || '-'}</span></td>
                <td class="fw-bold text-success">${formatRupiah(t.total_jml_transaksi)}</td>
                <td class="fw-semibold ${labaClass}">${formatRupiah(labaVal)}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-primary" onclick="lihatDetail('${t.invoice}')">
                        <i class="bi bi-eye"></i>
                    </button>
                </td>
            </tr>`;
        });

        wrap.innerHTML = `
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:40px">#</th>
                            <th>Invoice</th>
                            <th>Tanggal</th>
                            <th>Kasir</th>
                            <th>Kedatangan</th>
                            <th>Pembayaran</th>
                            <th>Total</th>
                            <th>Laba</th>
                            <th class="text-center">Aksi</th>
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
    let btns = `<button class="btn btn-sm btn-outline-secondary" ${page===1?'disabled':''} onclick="loadPenjualan(${page-1})"><i class="bi bi-chevron-left"></i></button>`;
    const start = Math.max(1,page-2), end = Math.min(totalPage,page+2);
    if (start>1) btns += `<button class="btn btn-sm btn-outline-secondary" onclick="loadPenjualan(1)">1</button>`;
    if (start>2) btns += `<span class="btn btn-sm disabled">…</span>`;
    for (let p=start;p<=end;p++) btns += `<button class="btn btn-sm ${p===page?'btn-success':'btn-outline-secondary'}" onclick="loadPenjualan(${p})">${p}</button>`;
    if (end<totalPage-1) btns += `<span class="btn btn-sm disabled">…</span>`;
    if (end<totalPage) btns += `<button class="btn btn-sm btn-outline-secondary" onclick="loadPenjualan(${totalPage})">${totalPage}</button>`;
    btns += `<button class="btn btn-sm btn-outline-secondary" ${page===totalPage?'disabled':''} onclick="loadPenjualan(${page+1})"><i class="bi bi-chevron-right"></i></button>`;
    document.getElementById('paginasiBtn').innerHTML = btns;
}

async function lihatDetail(invoice) {
    document.getElementById('modalInvoice').textContent = invoice;
    document.getElementById('modalBody').innerHTML = `<div class="loading-overlay"><div class="spinner-bk"></div></div>`;
    modalBs.show();

    try {
        const url = `{{ url('/owner/data/penjualan') }}/${encodeURIComponent(invoice)}/detail`;
        const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const d   = await res.json();

        if (!d.success) {
            document.getElementById('modalBody').innerHTML = `<div class="alert alert-warning">Detail tidak ditemukan.</div>`;
            return;
        }

        // [REVISI] Hitung total laba dari semua item
        let totalLaba = 0;
        let itemRows  = '';
        (d.items || []).forEach(item => {
            const laba     = Number(item.laba || 0);
            totalLaba     += laba;
            const labaClass = laba < 0 ? 'text-danger' : 'text-success';
            itemRows += `<tr>
                <td>${item.nama_produk}</td>
                <td class="text-end">${formatRupiah(item.harga_produk)}</td>
                <td class="text-center">${item.qty}</td>
                <td class="text-end">${formatRupiah(item.total_harga)}</td>
                <td class="text-end fw-semibold ${labaClass}">${formatRupiah(laba)}</td>
            </tr>`;
        });

        const diskonInfo = (d.status_diskon === '1' || d.status_diskon === 1)
            ? `<span class="badge bg-warning text-dark">${formatRupiah(d.nominal_disc)} (${d.tipe_diskon || 'nominal'})</span>`
            : '<span class="text-muted">-</span>';

        const labaTotal      = d.total_laba !== undefined ? Number(d.total_laba) : totalLaba;
        const labaClass      = labaTotal < 0 ? 'text-danger' : 'text-success';

        document.getElementById('modalBody').innerHTML = `
            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <dl class="row small mb-0">
                        <dt class="col-5 text-muted">Invoice</dt>
                        <dd class="col-7"><code>${d.invoice}</code></dd>
                        <dt class="col-5 text-muted">Tanggal</dt>
                        <dd class="col-7">${d.tanggal} ${d.waktu || ''}</dd>
                        <dt class="col-5 text-muted">Kasir</dt>
                        <dd class="col-7">${d.nama_pegawai || '-'}</dd>
                        <dt class="col-5 text-muted">Jenis Kedatangan</dt>
                        <dd class="col-7">${d.jenis_kedatangan || '-'}</dd>
                        <dt class="col-5 text-muted">Pelanggan</dt>
                        <dd class="col-7">${d.nama_pelanggan || 'Umum'}</dd>
                    </dl>
                </div>
                <div class="col-sm-6">
                    <dl class="row small mb-0">
                        <dt class="col-5 text-muted">Metode Bayar</dt>
                        <dd class="col-7">${d.jenis_pembayaran || '-'}</dd>
                        <dt class="col-5 text-muted">Diskon</dt>
                        <dd class="col-7">${diskonInfo}</dd>
                        <dt class="col-5 text-muted">Total</dt>
                        <dd class="col-7 fw-bold text-success">${formatRupiah(d.total_jml_transaksi)}</dd>
                        <dt class="col-5 text-muted">Dibayar</dt>
                        <dd class="col-7">${formatRupiah(d.dibayar)}</dd>
                        <dt class="col-5 text-muted">Kembalian</dt>
                        <dd class="col-7">${formatRupiah(d.kembali)}</dd>
                        <dt class="col-5 text-muted">Laba</dt>
                        <dd class="col-7 fw-bold ${labaClass}">${formatRupiah(labaTotal)}</dd>
                    </dl>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-end">Harga</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">Laba</th>
                        </tr>
                    </thead>
                    <tbody>${itemRows}</tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total</td>
                            <td class="text-end fw-bold text-success">${formatRupiah(d.total_jml_transaksi)}</td>
                            <td class="text-end fw-bold ${labaClass}">${formatRupiah(labaTotal)}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>`;

    } catch(e) {
        document.getElementById('modalBody').innerHTML = `<div class="alert alert-danger">Gagal memuat detail.</div>`;
    }
}
</script>
@endpush