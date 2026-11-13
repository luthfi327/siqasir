@extends('owner.layout')

@section('title', 'Promo Aktif')
@section('page-title', 'Promo Aktif')
@section('page-sub', 'Informasi harga promo khusus dari Pyramidsoft')

@section('content')

@if($promoAktif)

    {{-- ── Ada Promo Aktif ─────────────────────────────────────────────── --}}

    {{-- Banner promo --}}
    <div class="alert alert-success d-flex align-items-center gap-3 mb-4 animate__animated animate__fadeIn"
         style="border-left: 4px solid #10b981; background: #f0fdf4; color: #065f46;">
        <i class="bi bi-gift-fill fs-4 flex-shrink-0 text-success"></i>
        <div>
            <strong>Selamat! Anda mendapatkan harga promo khusus.</strong><br>
            <span class="text-sm">Promo ini diberikan khusus oleh tim Pyramidsoft untuk toko Anda.</span>
        </div>
    </div>

    <div class="row g-4">

        {{-- Kolom Kiri: Detail Promo --}}
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header">
                    <h6><i class="bi bi-tag-fill me-2 text-success"></i>Detail Promo Aktif</h6>
                </div>
                <div class="card-body text-center py-4">
                    <div class="mb-3" style="font-size:3rem;">🎉</div>
                    <div class="text-muted text-sm mb-1">Harga Promo Khusus Anda</div>
                    <div class="fw-bold text-success mb-1" style="font-size:2.4rem;">
                        Rp {{ number_format($promoAktif->harga_promo, 0, ',', '.') }}
                    </div>
                    <div class="text-muted text-xs mb-3">per bulan</div>

                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill"
                         style="background:#dcfce7; color:#166534;">
                        <i class="bi bi-check-circle-fill"></i>
                        <span class="text-sm fw-semibold">Promo Sedang Aktif</span>
                    </div>
                </div>
                <hr class="mx-3 my-0">
                <div class="card-body">
                    <dl class="row small mb-0">
                        @if($promoAktif->berlaku_hingga)
                        @php
                            $expPromo  = \Carbon\Carbon::parse($promoAktif->berlaku_hingga)->setTimezone('Asia/Jakarta');
                            $sisaPromo = (int) now()->diffInDays($promoAktif->berlaku_hingga, false);
                        @endphp
                        <dt class="col-5 text-muted">Berlaku Hingga</dt>
                        <dd class="col-7">
                            <strong>{{ $expPromo->translatedFormat('d M Y') }}</strong>
                            <br>
                            @if($sisaPromo <= 3)
                                <span class="badge badge-expired text-xs">⚠ {{ $sisaPromo }} hari lagi</span>
                            @elseif($sisaPromo <= 7)
                                <span class="badge badge-pending text-xs">{{ $sisaPromo }} hari lagi</span>
                            @else
                                <span class="badge badge-paid text-xs">{{ $sisaPromo }} hari lagi</span>
                            @endif
                        </dd>
                        @else
                        <dt class="col-5 text-muted">Berlaku Hingga</dt>
                        <dd class="col-7"><span class="badge badge-paid">Permanen</span></dd>
                        @endif

                        @if($promoAktif->catatan)
                        <dt class="col-5 text-muted">Catatan</dt>
                        <dd class="col-7 fst-italic">{{ $promoAktif->catatan }}</dd>
                        @endif

                        <dt class="col-5 text-muted">Diberikan oleh</dt>
                        <dd class="col-7">Tim Pyramidsoft</dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Perbandingan Harga --}}
        <div class="col-lg-7 d-flex flex-column gap-4">

            {{-- Perbandingan --}}
            <div class="card">
                <div class="card-header">
                    <h6><i class="bi bi-bar-chart me-2 text-primary"></i>Perbandingan Harga</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 rounded-3" style="background:#f1f5f9; border: 2px dashed #cbd5e1;">
                                <div class="text-muted text-xs mb-1">Harga Normal</div>
                                <div class="fw-bold" style="font-size:1.4rem; color:#6b7280; text-decoration:line-through;">
                                    Rp {{ number_format($hargaNormal, 0, ',', '.') }}
                                </div>
                                <div class="text-muted text-xs">per bulan</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 rounded-3" style="background:#f0fdf4; border: 2px solid #10b981;">
                                <div class="text-success text-xs fw-semibold mb-1">Harga Promo Anda</div>
                                <div class="fw-bold text-success" style="font-size:1.4rem;">
                                    Rp {{ number_format($promoAktif->harga_promo, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-success">per bulan</div>
                            </div>
                        </div>
                    </div>

                    @php $hemat = $hargaNormal - $promoAktif->harga_promo; @endphp
                    @if($hemat > 0)
                    <div class="text-center mt-3 p-2 rounded-3" style="background:#fffbeb;">
                        <span class="text-warning fw-semibold text-sm">
                            🎁 Anda hemat Rp {{ number_format($hemat, 0, ',', '.') }} per bulan!
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Info cara perpanjang --}}
            <div class="card">
                <div class="card-header">
                    <h6><i class="bi bi-info-circle me-2 text-primary"></i>Cara Menggunakan Promo</h6>
                </div>
                <div class="card-body">
                    <ol class="small mb-0" style="padding-left: 1.2rem; line-height: 2;">
                        <li>Buka aplikasi <strong>Bathiku Kasir</strong> di Android Anda</li>
                        <li>Login sebagai <strong>Owner</strong></li>
                        <li>Masuk ke menu <strong>Perpanjang Akun / Langganan</strong></li>
                        <li>Harga promo <strong>Rp {{ number_format($promoAktif->harga_promo, 0, ',', '.') }}</strong> akan otomatis diterapkan</li>
                        <li>Bayar via <strong>QRIS</strong> yang muncul di layar</li>
                    </ol>
                    <div class="alert alert-warning mt-3 mb-0 py-2 px-3 text-sm">
                        <i class="bi bi-clock me-1"></i>
                        Promo ini hanya berlaku sampai
                        <strong>
                            @if($promoAktif->berlaku_hingga)
                                {{ \Carbon\Carbon::parse($promoAktif->berlaku_hingga)->setTimezone('Asia/Jakarta')->translatedFormat('d M Y') }}
                            @else
                                promo dicabut
                            @endif
                        </strong>.
                        Segera perpanjang sebelum promo berakhir!
                    </div>
                </div>
            </div>

        </div>
    </div>

@else

    {{-- ── Tidak Ada Promo Aktif ─────────────────────────────────────── --}}
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div style="font-size:3.5rem;" class="mb-3">💳</div>
                    <h5 class="fw-bold mb-2">Tidak Ada Promo Aktif</h5>
                    <p class="text-muted text-sm mb-4">
                        Saat ini tidak ada promo khusus untuk toko Anda.<br>
                        Harga perpanjangan akun premium adalah:
                    </p>
                    <div class="fw-bold mb-3" style="font-size:2rem; color:#374151;">
                        Rp {{ number_format($hargaNormal, 0, ',', '.') }}
                    </div>
                    <div class="text-muted text-xs mb-4">per bulan / per cabang</div>

                    <hr class="my-3">

                    <div class="text-start">
                        <div class="fw-semibold text-sm mb-2">Cara menghubungi admin untuk promo:</div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height:2;">
                            <li>Hubungi tim Pyramidsoft via WhatsApp</li>
                            <li>Kirim email ke <strong>support@pyramidsoft.id</strong></li>
                            <li>Sebutkan <strong>Kode Owner</strong> Anda: <code>{{ session('owner_toko.kode_owner') }}</code></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h6><i class="bi bi-question-circle me-2 text-primary"></i>Tentang Promo Bathiku Kasir</h6>
                </div>
                <div class="card-body small">
                    <p class="text-muted mb-3">
                        Promo khusus dapat diberikan oleh tim <strong>Pyramidsoft</strong> untuk toko tertentu berdasarkan:
                    </p>
                    <ul class="text-muted ps-3 mb-3" style="line-height:2;">
                        <li>Program loyalitas pelanggan lama</li>
                        <li>Promo referral / ajak teman</li>
                        <li>Event khusus atau hari raya</li>
                        <li>Negosiasi langsung dengan tim sales</li>
                    </ul>
                    <div class="alert alert-warning mb-0 py-2 px-3 text-sm">
                        <i class="bi bi-lock me-1"></i>
                        Promo <strong>hanya dapat dibuat oleh admin Pyramidsoft</strong>.
                        Owner tidak dapat membuat promo sendiri.
                    </div>
                </div>
            </div>
        </div>
    </div>

@endif

@endsection