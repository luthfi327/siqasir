@extends('admin.layouts.app')

@section('title', 'Kelola Promo')
@section('page-title', 'Kelola Promo')

@section('content')

<div class="row g-3">

    {{-- FORM TAMBAH PROMO --}}
    <div class="col-lg-4">
        <div class="bk-card">
            <div class="bk-card-header">
                <h2><i class="bi bi-plus-circle-fill text-primary me-1"></i> Tambah Promo Baru</h2>
            </div>
            <div class="bk-card-body">
                <form action="{{ route('admin.promos.store') }}" method="POST" id="formTambahPromo">
                    @csrf

                    {{-- Cari Toko --}}
                    <div class="mb-3">
                        <label class="form-label">Cari & Pilih Toko</label>
                        <input type="hidden" name="kode_owner" id="kodeOwnerInput">
                        <div class="position-relative">
                            <input type="text"
                                   id="searchTokoInput"
                                   class="form-control"
                                   placeholder="Ketik nama toko..."
                                   autocomplete="off">
                            <button type="button" id="btnClearToko" onclick="clearPilihanToko()"
                                    style="display:none;position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#94a3b8;font-size:1rem;cursor:pointer;">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                            <div id="dropdownToko"
                                 style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;
                                        background:#fff;border:1px solid #e2e8f0;border-radius:8px;
                                        box-shadow:0 4px 16px rgba(0,0,0,.1);z-index:9999;
                                        max-height:220px;overflow-y:auto;"></div>
                        </div>
                        <div id="badgeTokoTerpilih" style="display:none;margin-top:8px;">
                            <span class="badge bg-success py-2 px-3 rounded-pill" style="font-size:.82rem">
                                <i class="bi bi-shop me-1"></i>
                                <span id="namaTokoTerpilih"></span>
                            </span>
                        </div>
                    </div>

                    {{-- Harga Promo --}}
                    <div class="mb-3">
                        <label class="form-label">Harga Promo (Rp)</label>
                        <input type="text" name="harga_promo" id="inputHargaPromo"
                               class="form-control"
                               value="{{ old('harga_promo') }}"
                               placeholder="contoh: 15.000">
                        <div class="form-text">Minimal Rp 1.000.</div>
                    </div>

                    {{-- Tipe Promo --}}
                    <div class="mb-3">
                        <label class="form-label">Tipe Promo</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio"
                                       name="tipe_promo" id="tipePromoSekali" value="sekali"
                                       {{ old('tipe_promo', 'sekali') === 'sekali' ? 'checked' : '' }}>
                                <label class="form-check-label" for="tipePromoSekali">
                                    <span class="badge bg-primary">Sekali</span>
                                    <small class="text-muted d-block">Berlaku hingga tanggal tertentu</small>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio"
                                       name="tipe_promo" id="tipePromoTahunan" value="tahunan"
                                       {{ old('tipe_promo') === 'tahunan' ? 'checked' : '' }}>
                                <label class="form-check-label" for="tipePromoTahunan">
                                    <span class="badge" style="background:#7c3aed!important">Tahunan</span>
                                    <small class="text-muted d-block">Berlaku N bulan berturut-turut</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Field Sekali --}}
                    <div id="fieldSekali" class="mb-3">
                        <label class="form-label">Berlaku Hingga</label>
                        <input type="date" name="berlaku_hingga" id="inputBerlakuHingga"
                               class="form-control"
                               min="{{ now()->addDay()->format('Y-m-d') }}"
                               value="{{ old('berlaku_hingga') }}">
                        <div class="form-text">Harus tanggal di masa depan.</div>
                    </div>

                    {{-- Field Tahunan --}}
                    <div id="fieldTahunan" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label">Durasi (bulan)</label>
                            <input type="number" name="durasi_bulan" class="form-control"
                                   min="1" max="24"
                                   value="{{ old('durasi_bulan', 12) }}"
                                   placeholder="contoh: 12">
                            <div class="form-text">Minimal 1, maksimal 24 bulan.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Berlaku Mulai</label>
                            <input type="date" name="berlaku_mulai" class="form-control"
                                   value="{{ old('berlaku_mulai', now()->format('Y-m-d')) }}">
                        </div>
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            Promo tahunan otomatis berakhir setelah <strong>N kali pembayaran</strong>.
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div class="mb-3">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="catatan" class="form-control" rows="2"
                                  placeholder="Alasan promo, dsb.">{{ old('catatan') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-bk-primary w-100" id="btnSimpanPromo" disabled>
                        <i class="bi bi-save-fill me-1"></i> Simpan Promo
                    </button>
                    <div class="text-muted text-xs text-center mt-1" id="hintPilihToko">
                        Pilih toko terlebih dahulu untuk mengaktifkan tombol simpan.
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- TABEL PROMO AKTIF --}}
    <div class="col-lg-8">
        <div class="bk-card">
            <div class="bk-card-header">
                <h2><i class="bi bi-tag-fill text-primary me-1"></i> Promo Aktif</h2>
                <span class="badge-soft badge-soft-secondary">{{ $promoTotal }} promo aktif</span>
            </div>
            <div class="bk-card-body p-0">
                @if ($promoItems->isEmpty())
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        Belum ada promo aktif saat ini.
                    </div>
                @else
                    <div class="table-responsive table-responsive-wrap">
                        <table class="table bk-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Nama Toko</th>
                                    <th>Harga Promo</th>
                                    <th>Tipe</th>
                                    <th>Berlaku Hingga / Sisa</th>
                                    <th>Catatan</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($promoItems as $promo)
                                    @php
                                        $isTahunan   = ($promo['tipe_promo'] ?? 'sekali') === 'tahunan';
                                        $infoTahunan = $promo['info_tahunan'] ?? null;
                                        $sisaHari    = $promo['sisa_hari'] ?? null;
                                        $isWarning   = !$isTahunan && $sisaHari !== null && $sisaHari <= 3;
                                    @endphp
                                    <tr class="{{ $isWarning ? 'row-danger' : '' }}">
                                        <td>
                                            <div class="fw-semibold">{{ $promo['nama_toko'] }}</div>
                                            <div class="text-muted small">{{ $promo['kota_toko'] }}</div>
                                        </td>
                                        <td class="fw-semibold text-success">
                                            Rp{{ number_format($promo['harga_promo'], 0, ',', '.') }}
                                        </td>
                                        <td>
                                            @if ($isTahunan)
                                                <span class="badge" style="background:#7c3aed">
                                                    <i class="bi bi-arrow-repeat me-1"></i>Tahunan
                                                </span>
                                            @else
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-check-circle me-1"></i>Sekali
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($isTahunan && $infoTahunan)
                                                <div class="small fw-semibold mb-1">{{ $infoTahunan['label'] }}</div>
                                                <div class="progress" style="height:6px;width:100px;">
                                                    <div class="progress-bar"
                                                         style="width:{{ $infoTahunan['persen'] }}%;background:#7c3aed">
                                                    </div>
                                                </div>
                                                <div class="text-muted small mt-1">
                                                    Sisa {{ $infoTahunan['bulan_tersisa'] }} bulan
                                                </div>
                                            @else
                                                <div class="small">
                                                    {{ $promo['berlaku_hingga']
                                                        ? \Carbon\Carbon::parse($promo['berlaku_hingga'])->translatedFormat('d M Y')
                                                        : 'Selamanya' }}
                                                </div>
                                                @if ($sisaHari !== null)
                                                    <span class="badge-soft {{ $sisaHari <= 3 ? 'badge-soft-danger' : 'badge-soft-warning' }}">
                                                        {{ $sisaHari }} hari lagi
                                                    </span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="small text-muted">{{ $promo['catatan'] ?: '-' }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('admin.promos.nonaktifkan', $promo['id']) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Nonaktifkan promo untuk {{ addslashes($promo['nama_toko']) }}?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-x-circle"></i> Nonaktifkan
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($isPaginator)
                        <div class="p-3 border-top">
                            {{ $promos->onEachSide(1)->links('pagination::bootstrap-4') }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

</div>

{{-- Script inline langsung di @section bukan @push --}}
<script>
(function() {
    // Data toko dari controller — sudah array PHP, di-encode langsung
    var daftarToko = {!! json_encode($tokoArr) !!};
    var tokoTerpilih = null;

    // ── Autocomplete toko ──────────────────────────────────────────
    function filterToko(keyword) {
        var dropdown = document.getElementById('dropdownToko');
        if (!dropdown) return;

        var q = keyword.trim().toLowerCase();

        // Jika sudah pilih toko dan keyword tidak berubah, skip
        if (tokoTerpilih && keyword === tokoTerpilih.nama_toko) {
            dropdown.style.display = 'none';
            return;
        }

        var hasil = q.length === 0
            ? daftarToko
            : daftarToko.filter(function(t) {
                return (t.nama_toko || '').toLowerCase().indexOf(q) !== -1 ||
                       (t.kota || '').toLowerCase().indexOf(q) !== -1 ||
                       (t.kode_owner || '').toLowerCase().indexOf(q) !== -1;
            });

        if (hasil.length === 0) {
            dropdown.innerHTML = '<div style="padding:12px;text-align:center;color:#94a3b8;font-size:.85rem;">Toko tidak ditemukan</div>';
            dropdown.style.display = 'block';
            return;
        }

        var html = '';
        var tampil = hasil.slice(0, 20);
        for (var i = 0; i < tampil.length; i++) {
            var t = tampil[i];
            var safeKode = (t.kode_owner || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
            var safaNama = (t.nama_toko || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
            html += '<div style="padding:10px 14px;cursor:pointer;border-bottom:1px solid #f1f5f9;"' +
                    ' onclick="window._pilihToko(\'' + safeKode + '\', \'' + safaNama + '\')"' +
                    ' onmouseenter="this.style.background=\'#f0fdf4\'"' +
                    ' onmouseleave="this.style.background=\'\'">' +
                    '<div style="font-weight:600;font-size:.85rem">' + (t.nama_toko || '') + '</div>' +
                    '<div style="font-size:.75rem;color:#94a3b8">' + (t.kota || '-') + ' &middot; ' + (t.kode_owner || '') + '</div>' +
                    '</div>';
        }
        dropdown.innerHTML = html;
        dropdown.style.display = 'block';
    }

    // Expose ke global agar bisa dipanggil dari onclick di HTML dinamis
    window._pilihToko = function(kodeOwner, namaToko) {
        tokoTerpilih = { kode_owner: kodeOwner, nama_toko: namaToko };
        document.getElementById('kodeOwnerInput').value      = kodeOwner;
        document.getElementById('searchTokoInput').value     = namaToko;
        document.getElementById('namaTokoTerpilih').textContent = namaToko;
        document.getElementById('badgeTokoTerpilih').style.display = 'block';
        document.getElementById('btnClearToko').style.display      = 'block';
        document.getElementById('dropdownToko').style.display      = 'none';
        document.getElementById('btnSimpanPromo').disabled         = false;
        document.getElementById('hintPilihToko').style.display     = 'none';
    };

    window.clearPilihanToko = function() {
        tokoTerpilih = null;
        document.getElementById('kodeOwnerInput').value            = '';
        document.getElementById('searchTokoInput').value           = '';
        document.getElementById('badgeTokoTerpilih').style.display = 'none';
        document.getElementById('btnClearToko').style.display      = 'none';
        document.getElementById('dropdownToko').style.display      = 'none';
        document.getElementById('btnSimpanPromo').disabled         = true;
        document.getElementById('hintPilihToko').style.display     = '';
        document.getElementById('searchTokoInput').focus();
    };

    // Pasang event ke input search setelah DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        var searchInput = document.getElementById('searchTokoInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() { filterToko(this.value); });
            searchInput.addEventListener('focus', function() { filterToko(this.value); });
        }

        // Tutup dropdown klik di luar
        document.addEventListener('click', function(e) {
            var wrapper = document.getElementById('searchTokoInput');
            var dropdown = document.getElementById('dropdownToko');
            if (wrapper && dropdown && !wrapper.parentElement.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });

        // Format rupiah
        var hargaInput = document.getElementById('inputHargaPromo');
        if (hargaInput) {
            hargaInput.addEventListener('input', function() {
                var val = this.value.replace(/\D/g, '');
                if (val) this.value = parseInt(val, 10).toLocaleString('id-ID');
            });
        }

        // Submit: strip format rupiah
        var form = document.getElementById('formTambahPromo');
        if (form) {
            form.addEventListener('submit', function() {
                if (hargaInput) {
                    hargaInput.value = hargaInput.value.replace(/\./g, '').replace(/,/g, '');
                }
            });
        }

        // ── Toggle Sekali / Tahunan ────────────────────────────────
        var radioSekali  = document.getElementById('tipePromoSekali');
        var radioTahunan = document.getElementById('tipePromoTahunan');
        var fieldSekali  = document.getElementById('fieldSekali');
        var fieldTahunan = document.getElementById('fieldTahunan');
        var inputBerlaku = document.getElementById('inputBerlakuHingga');

        function toggleTipe() {
            if (radioTahunan && radioTahunan.checked) {
                if (fieldSekali)  fieldSekali.style.display  = 'none';
                if (fieldTahunan) fieldTahunan.style.display = 'block';
                if (inputBerlaku) inputBerlaku.required = false;
            } else {
                if (fieldSekali)  fieldSekali.style.display  = 'block';
                if (fieldTahunan) fieldTahunan.style.display = 'none';
                if (inputBerlaku) inputBerlaku.required = true;
            }
        }

        if (radioSekali)  radioSekali.addEventListener('change', toggleTipe);
        if (radioTahunan) radioTahunan.addEventListener('change', toggleTipe);

        // Jalankan sekali saat load untuk handle old() value
        toggleTipe();
    });

})();
</script>

@endsection