@extends('admin.layouts.app')

@section('title', 'Monitoring Pembayaran')
@section('page-title', 'Monitoring Pembayaran')

@section('content')

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="stat-card is-success">
                <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stat-value">Rp{{ number_format($rekap['total_paid_bulan_ini'], 0, ',', '.') }}</div>
                <div class="stat-label">Total PAID Bulan Ini</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card is-warning">
                <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="stat-value">{{ number_format($rekap['total_pending_aktif']) }}</div>
                <div class="stat-label">Total PENDING Aktif</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card is-danger">
                <div class="stat-icon"><i class="bi bi-x-circle-fill"></i></div>
                <div class="stat-value">Rp{{ number_format($rekap['total_cancelled_bulan_ini'], 0, ',', '.') }}</div>
                <div class="stat-label">Total CANCELLED Bulan Ini</div>
            </div>
        </div>
    </div>

    <div class="bk-card mb-3">
        <div class="bk-card-body">
            <form action="{{ route('admin.payments.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach (['PAID', 'PENDING', 'CANCELLED', 'EXPIRED'] as $statusOpt)
                            <option value="{{ $statusOpt }}" {{ $filters['status'] === $statusOpt ? 'selected' : '' }}>{{ $statusOpt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="from" class="form-control" value="{{ $filters['from'] }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="to" class="form-control" value="{{ $filters['to'] }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-bk-primary flex-fill">
                        <i class="bi bi-funnel-fill me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.payments.export', array_filter($filters)) }}" class="btn btn-outline-success" title="Export CSV sesuai filter aktif">
                        <i class="bi bi-file-earmark-spreadsheet-fill"></i> CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="bk-card">
        <div class="bk-card-header">
            <h2><i class="bi bi-credit-card-fill text-primary me-1"></i> Riwayat Pembayaran</h2>
            <span class="badge-soft badge-soft-secondary">{{ $payments->total() }} transaksi</span>
        </div>
        <div class="bk-card-body p-0">
            @if ($payments->isEmpty())
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i> Tidak ada transaksi yang cocok dengan filter ini.
                </div>
            @else
                <div class="table-responsive table-responsive-wrap">
                    <table class="table bk-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Toko</th>
                                <th>Nominal</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th>Kode Transaksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $badgeMap = [
                                    'PAID' => 'badge-soft-success',
                                    'PENDING' => 'badge-soft-warning',
                                    'CANCELLED' => 'badge-soft-danger',
                                    'EXPIRED' => 'badge-soft-secondary',
                                ];
                            @endphp
                            @foreach ($payments as $p)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($p->created_at)->translatedFormat('d M Y, H:i') }}</td>
                                    <td class="fw-semibold">{{ $p->nama_toko ?? '-' }}</td>
                                    <td>Rp{{ number_format($p->amount, 0, ',', '.') }}</td>
                                    <td>{{ $p->durasi_bulan }} bulan</td>
                                    <td><span class="badge-soft {{ $badgeMap[$p->status] ?? 'badge-soft-secondary' }}">{{ $p->status }}</span></td>
                                    <td><code class="small">{{ $p->external_id }}</code></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top">
                    {{ $payments->onEachSide(1)->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>

@endsection