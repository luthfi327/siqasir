@extends('owner.layout')

@section('title', 'Riwayat Pembayaran')
@section('page-title', 'Riwayat Pembayaran')
@section('page-sub', 'Semua transaksi perpanjangan akun premium')

@section('content')

{{-- ── Summary Card ──────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="stat-card green">
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-value">Rp {{ number_format($totalNominalPaid, 0, ',', '.') }}</div>
            <div class="stat-label">Total Pembayaran Berhasil (PAID)</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="stat-card blue">
            <div class="stat-icon"><i class="bi bi-receipt"></i></div>
            <div class="stat-value">{{ $payments->where('status', 'PAID')->count() }}</div>
            <div class="stat-label">Transaksi Berhasil</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="stat-card purple">
            <div class="stat-icon"><i class="bi bi-calendar3"></i></div>
            <div class="stat-value">{{ $payments->count() }}</div>
            <div class="stat-label">Total Semua Transaksi</div>
        </div>
    </div>
</div>

{{-- ── Tabel Pembayaran ────────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6><i class="bi bi-credit-card me-2"></i>Riwayat Pembayaran Premium</h6>
        <span class="text-muted text-xs">{{ $payments->count() }} transaksi</span>
    </div>
    <div class="card-body p-0">
        @if($payments->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                <div class="text-sm">Belum ada riwayat pembayaran.</div>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nominal</th>
                        <th>Durasi</th>
                        <th>Status</th>
                        <th>Aktif Sampai</th>
                        <th>Kode Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $p)
                    <tr>
                        <td>
                            <div class="fw-semibold text-sm">
                                {{ \Carbon\Carbon::parse($p->created_at)->setTimezone('Asia/Jakarta')->translatedFormat('d M Y') }}
                            </div>
                            <div class="text-muted text-xs">
                                {{ \Carbon\Carbon::parse($p->created_at)->setTimezone('Asia/Jakarta')->format('H:i') }} WIB
                            </div>
                        </td>
                        <td class="fw-bold">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                        <td class="text-sm">{{ $p->durasi_bulan }} bulan</td>
                        <td>
                            @php $st = strtolower($p->status ?? '') @endphp
                            <span class="badge badge-{{ $st === 'paid' ? 'paid' : ($st === 'pending' ? 'pending' : 'cancelled') }} px-3 py-1 rounded-pill">
                                {{ strtoupper($p->status) }}
                            </span>
                        </td>
                        <td class="text-sm">
                            @if($p->new_exp_date && $p->status === 'PAID')
                                {{ \Carbon\Carbon::parse($p->new_exp_date)->setTimezone('Asia/Jakarta')->translatedFormat('d M Y') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <code class="text-xs text-muted">{{ $p->external_id ?? '-' }}</code>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

@endsection