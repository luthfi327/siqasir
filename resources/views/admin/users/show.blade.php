{{-- resources/views/admin/users/show.blade.php --}}
{{-- STATUS: MODIFIKASI --}}
{{-- Perubahan GAP 9: --}}
{{--   - Bagian "Promo Aktif": tampilkan info tipe_promo --}}
{{--   - Jika tipe 'sekali': tampilkan harga, berlaku hingga, sisa hari (sama seperti sebelumnya) --}}
{{--   - Jika tipe 'tahunan': tampilkan harga, durasi total, bulan dipakai, bulan tersisa, progress bar --}}
{{--   - Form tambah promo di show page: tambah radio tipe_promo + field kondisional (sama seperti /admin/promos) --}}

@extends('admin.layouts.app')

@section('title', $toko['nama_toko'])
@section('page-title', 'Detail Toko')

@section('content')

    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Toko
    </a>

    {{-- ── Info Toko ──────────────────────────────────────────────────────── --}}
    <div class="bk-card mb-3">
        <div class="bk-card-header">
            <h2 class="d-flex align-items-center gap-2">
                <i class="bi bi-shop text-primary"></i> {{ $toko['nama_toko'] }}
                @if ($toko['status_akun'] === 'premium')
                    <span class="badge-soft badge-soft-success">Premium</span>
                @elseif ($toko['status_akun'] === 'trial')
                    <span class="badge-soft badge-soft-info">Trial</span>
                @else
                    <span class="badge-soft badge-soft-danger">Expired</span>
                @endif
            </h2>
            <span class="text-muted small">Kode Owner: <code>{{ $toko['kode_owner'] }}</code></span>
        </div>
        <div class="bk-card-body">
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <div class="text-muted small">Nama Owner</div>
                    <div class="fw-semibold">{{ $toko['nama_owner'] }}</div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="text-muted small">No HP</div>
                    <div class="fw-semibold">{{ $toko['no_hp'] }}</div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="text-muted small">Email</div>
                    <div class="fw-semibold">{{ $toko['email'] }}</div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="text-muted small">Kota / Provinsi</div>
                    <div class="fw-semibold">{{ $toko['kota'] }}, {{ $toko['provinsi'] }}</div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="text-muted small">Terdaftar Sejak</div>
                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($toko['toko_created_at'])->translatedFormat('d M Y') }}</div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="text-muted small">Umur Akun</div>
                    <div class="fw-semibold">{{ $toko['umur_bulan'] }} bulan</div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="text-muted small">Tanggal Expired</div>
                    <div class="fw-semibold">
                        {{ $toko['exp_date'] ? \Carbon\Carbon::parse($toko['exp_date'])->translatedFormat('d M Y') : '-' }}
                        @if ($toko['sisa_hari'] !== null)
                            <span class="badge-soft {{ $toko['sisa_hari'] <= 7 ? 'badge-soft-danger' : ($toko['sisa_hari'] <= 30 ? 'badge-soft-warning' : 'badge-soft-secondary') }} ms-1">
                                {{ $toko['sisa_hari'] >= 0 ? $toko['sisa_hari'] . ' hari lagi' : 'Sudah lewat ' . abs($toko['sisa_hari']) . ' hari' }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="text-muted small">Total Pegawai Aktif</div>
                    <div class="fw-semibold">{{ $toko['total_pegawai'] }} orang</div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-4">
                    <div class="stat-card is-primary">
                        <div class="stat-icon"><i class="bi bi-receipt"></i></div>
                        <div class="stat-value">{{ $toko['total_pembayaran'] }}</div>
                        <div class="stat-label">Total Transaksi Sukses</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card is-success">
                        <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
                        <div class="stat-value">Rp{{ number_format($toko['total_pendapatan'], 0, ',', '.') }}</div>
                        <div class="stat-label">Total Pendapatan dari Toko Ini</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card {{ $toko['ada_promo_aktif'] ? 'is-warning' : 'is-dark' }}">
                        <div class="stat-icon"><i class="bi bi-tag-fill"></i></div>
                        <div class="stat-value">{{ $toko['ada_promo_aktif'] ? 'Ya' : 'Tidak' }}</div>
                        <div class="stat-label">Promo Khusus Aktif</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">

        {{-- ── Kiri: Riwayat Pembayaran + Pegawai ───────────────────────── --}}
        <div class="col-lg-7">

            {{-- Riwayat Pembayaran --}}
            <div class="bk-card mb-3">
                <div class="bk-card-header">
                    <h3><i class="bi bi-clock-history text-primary me-1"></i> Riwayat Pembayaran</h3>
                </div>
                <div class="bk-card-body p-0">
                    @if ($toko['riwayat_pembayaran']->isEmpty())
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i> Belum ada riwayat pembayaran.
                        </div>
                    @else
                        <div class="table-responsive table-responsive-wrap">
                            <table class="table bk-table mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Nominal</th>
                                        <th>Durasi</th>
                                        <th>Status</th>
                                        <th>Kode Transaksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($toko['riwayat_pembayaran'] as $p)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($p['created_at'])->translatedFormat('d M Y, H:i') }}</td>
                                            <td>Rp{{ number_format($p['amount'], 0, ',', '.') }}</td>
                                            <td>{{ $p['durasi_bulan'] }} bulan</td>
                                            <td>
                                                @php
                                                    $badgeMap = [
                                                        'PAID'      => 'badge-soft-success',
                                                        'PENDING'   => 'badge-soft-warning',
                                                        'CANCELLED' => 'badge-soft-danger',
                                                        'EXPIRED'   => 'badge-soft-secondary',
                                                    ];
                                                @endphp
                                                <span class="badge-soft {{ $badgeMap[$p['status']] ?? 'badge-soft-secondary' }}">
                                                    {{ $p['status'] }}
                                                </span>
                                            </td>
                                            <td><code class="small">{{ $p['external_id'] }}</code></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Daftar Pegawai --}}
            <div class="bk-card">
                <div class="bk-card-header">
                    <h3><i class="bi bi-people-fill text-primary me-1"></i> Daftar Pegawai</h3>
                </div>
                <div class="bk-card-body p-0">
                    @if ($toko['daftar_pegawai']->isEmpty())
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i> Belum ada data pegawai.
                        </div>
                    @else
                        <div class="table-responsive table-responsive-wrap">
                            <table class="table bk-table mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Username</th>
                                        <th>Hak Akses</th>
                                        <th>Posisi</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($toko['daftar_pegawai'] as $pegawai)
                                        <tr>
                                            <td class="fw-semibold">{{ $pegawai->nama_pegawai }}</td>
                                            <td>{{ $pegawai->username }}</td>
                                            <td>
                                                <span class="badge-soft {{ $pegawai->hak_akses === 'Owner' ? 'badge-soft-primary' : 'badge-soft-secondary' }}">
                                                    {{ $pegawai->hak_akses }}
                                                </span>
                                            </td>
                                            <td>{{ $pegawai->posisi ?? '-' }}</td>
                                            <td>
                                                <span class="badge-soft {{ $pegawai->status === 'Aktif' ? 'badge-soft-success' : 'badge-soft-secondary' }}">
                                                    {{ $pegawai->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Kanan: Promo Aktif + Form Tambah Promo ───────────────────── --}}
        <div class="col-lg-5">

            {{-- [GAP 9] Promo Aktif — tampilkan info tipe promo --}}
            <div class="bk-card mb-3">
                <div class="bk-card-header">
                    <h3><i class="bi bi-tag-fill text-primary me-1"></i> Promo Aktif</h3>
                </div>
                <div class="bk-card-body">
                    @if ($promoAktif)
                        @php
                            $isTahunan   = ($promoAktif->tipe_promo ?? 'sekali') === 'tahunan';
                            $infoTahunan = $promoAktif->infoTahunan();
                        @endphp

                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <div class="text-muted small">Harga Promo</div>
                                <div class="fw-bold fs-5 text-success">
                                    Rp{{ number_format($promoAktif->harga_promo, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="d-flex gap-1 flex-wrap justify-content-end">
                                <span class="badge-soft badge-soft-success">Aktif</span>
                                {{-- [GAP 9] Badge tipe promo --}}
                                @if ($isTahunan)
                                    <span class="badge" style="background:#7c3aed;color:#fff;font-size:.7rem">
                                        <i class="bi bi-arrow-repeat me-1"></i>Tahunan
                                    </span>
                                @else
                                    <span class="badge bg-primary" style="font-size:.7rem">Sekali</span>
                                @endif
                            </div>
                        </div>

                        @if ($isTahunan && $infoTahunan)
                            {{-- [GAP 9] Tampilkan progress bar untuk promo tahunan --}}
                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Progress Promo Tahunan</span>
                                    <span class="fw-semibold">{{ $infoTahunan['label'] }}</span>
                                </div>
                                <div class="progress" style="height:10px;border-radius:6px">
                                    <div class="progress-bar"
                                         style="width:{{ $infoTahunan['persen'] }}%;background:#7c3aed"
                                         role="progressbar"
                                         aria-valuenow="{{ $infoTahunan['persen'] }}"
                                         aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="row g-2 mt-2 text-center">
                                    <div class="col-4">
                                        <div class="text-muted" style="font-size:.7rem">Durasi Total</div>
                                        <div class="fw-bold">{{ $infoTahunan['total_durasi'] }} bln</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-muted" style="font-size:.7rem">Sudah Dipakai</div>
                                        <div class="fw-bold text-primary">{{ $promoAktif->total_bulan_dipakai }} bln</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-muted" style="font-size:.7rem">Sisa</div>
                                        <div class="fw-bold text-success">{{ $infoTahunan['bulan_tersisa'] }} bln</div>
                                    </div>
                                </div>
                                @if ($promoAktif->berlaku_mulai)
                                    <div class="text-muted small mt-2">
                                        Mulai: <strong>{{ \Carbon\Carbon::parse($promoAktif->berlaku_mulai)->translatedFormat('d M Y') }}</strong>
                                        &mdash; Selesai: <strong>{{ $promoAktif->berlaku_hingga ? \Carbon\Carbon::parse($promoAktif->berlaku_hingga)->translatedFormat('d M Y') : '-' }}</strong>
                                    </div>
                                @endif
                            </div>
                        @else
                            {{-- Promo sekali: tampilkan berlaku hingga + sisa hari --}}
                            <div class="mb-2">
                                <div class="text-muted small">Berlaku Hingga</div>
                                <div class="fw-semibold">
                                    {{ $promoAktif->berlaku_hingga
                                        ? \Carbon\Carbon::parse($promoAktif->berlaku_hingga)->translatedFormat('d M Y')
                                        : 'Selamanya' }}
                                </div>
                                @if ($promoAktif->berlaku_hingga)
                                    @php $sisaHariPromo = (int) now()->diffInDays($promoAktif->berlaku_hingga, false); @endphp
                                    <span class="badge-soft {{ $sisaHariPromo <= 3 ? 'badge-soft-danger' : 'badge-soft-warning' }} mt-1">
                                        {{ $sisaHariPromo }} hari lagi
                                    </span>
                                @endif
                            </div>
                        @endif

                        @if ($promoAktif->catatan)
                            <div class="text-muted small mt-2">Catatan</div>
                            <div class="mb-2">{{ $promoAktif->catatan }}</div>
                        @endif

                        <div class="text-muted small">Dibuat Oleh</div>
                        <div class="mb-3">{{ $promoAktif->dibuat_oleh }}</div>

                        {{-- Tombol nonaktifkan --}}
                        <form action="{{ route('admin.promos.nonaktifkan', $promoAktif->id) }}"
                              method="POST"
                              data-confirm="Nonaktifkan promo untuk {{ $toko['nama_toko'] }}?">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                <i class="bi bi-x-circle me-1"></i> Nonaktifkan Promo Ini
                            </button>
                        </form>

                    @else
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-tag fs-3 d-block mb-2"></i>
                            Toko ini belum memiliki promo khusus aktif.
                        </div>
                    @endif
                </div>
            </div>

            {{-- [GAP 9] Form tambah promo — support tipe sekali & tahunan --}}
            <div class="bk-card">
                <div class="bk-card-header">
                    <h3><i class="bi bi-plus-circle-fill text-primary me-1"></i> Tambah Promo untuk Toko Ini</h3>
                </div>
                <div class="bk-card-body">
                    <form action="{{ route('admin.promos.store') }}" method="POST" id="formPromoShow">
                        @csrf
                        <input type="hidden" name="kode_owner" value="{{ $toko['kode_owner'] }}">

                        <div class="mb-3">
                            <label class="form-label">Toko</label>
                            <input type="text" class="form-control" value="{{ $toko['nama_toko'] }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Harga Promo (Rp)</label>
                            <input type="text" name="harga_promo" class="form-control"
                                   data-format-rupiah placeholder="contoh: 15.000" required>
                        </div>

                        {{-- [GAP 9] Tipe promo toggle --}}
                        <div class="mb-3">
                            <label class="form-label">Tipe Promo</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio"
                                           name="tipe_promo" id="showTipeSekali" value="sekali" checked>
                                    <label class="form-check-label small" for="showTipeSekali">
                                        <span class="badge bg-primary">Sekali</span>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio"
                                           name="tipe_promo" id="showTipeTahunan" value="tahunan">
                                    <label class="form-check-label small" for="showTipeTahunan">
                                        <span class="badge" style="background:#7c3aed">Tahunan</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Field sekali --}}
                        <div id="showFieldSekali" class="mb-3">
                            <label class="form-label">Berlaku Hingga</label>
                            <input type="date" name="berlaku_hingga" class="form-control"
                                   min="{{ now()->addDay()->format('Y-m-d') }}">
                        </div>

                        {{-- Field tahunan --}}
                        <div id="showFieldTahunan" style="display:none">
                            <div class="mb-3">
                                <label class="form-label">Durasi (bulan)</label>
                                <input type="number" name="durasi_bulan" class="form-control"
                                       min="1" max="24" value="12">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Berlaku Mulai</label>
                                <input type="date" name="berlaku_mulai" class="form-control"
                                       value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan (opsional)</label>
                            <textarea name="catatan" class="form-control" rows="2"
                                      placeholder="Alasan promo, dsb."></textarea>
                        </div>

                        @if ($promoAktif)
                            <div class="alert alert-warning small py-2 px-3">
                                <i class="bi bi-info-circle-fill me-1"></i>
                                Toko ini sudah punya promo aktif. Menyimpan promo baru akan otomatis menonaktifkan promo sebelumnya.
                            </div>
                        @endif

                        <button type="submit" class="btn btn-bk-primary w-100">
                            <i class="bi bi-save-fill me-1"></i> Simpan Promo
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
<script>
// [GAP 9] Toggle field form promo di halaman show toko
(function () {
    const radioSekali  = document.getElementById('showTipeSekali');
    const radioTahunan = document.getElementById('showTipeTahunan');
    const fieldSekali  = document.getElementById('showFieldSekali');
    const fieldTahunan = document.getElementById('showFieldTahunan');
    const inputBerlaku = document.querySelector('[name="berlaku_hingga"]');

    function toggle() {
        if (radioTahunan?.checked) {
            fieldSekali.style.display  = 'none';
            fieldTahunan.style.display = '';
            if (inputBerlaku) inputBerlaku.required = false;
        } else {
            fieldSekali.style.display  = '';
            fieldTahunan.style.display = 'none';
            if (inputBerlaku) inputBerlaku.required = true;
        }
    }

    radioSekali?.addEventListener('change', toggle);
    radioTahunan?.addEventListener('change', toggle);
    toggle();
})();
</script>
@endpush