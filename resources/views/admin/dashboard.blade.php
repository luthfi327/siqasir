@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-2">
            <div class="stat-card is-success">
                <div class="stat-icon"><i class="bi bi-patch-check-fill"></i></div>
                <div class="stat-value">{{ number_format($summary['total_toko_aktif']) }}</div>
                <div class="stat-label">Toko Premium Aktif</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="stat-card is-danger">
                <div class="stat-icon"><i class="bi bi-x-octagon-fill"></i></div>
                <div class="stat-value">{{ number_format($summary['total_toko_expired']) }}</div>
                <div class="stat-label">Toko Expired</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="stat-card is-info">
                <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="stat-value">{{ number_format($summary['total_toko_trial']) }}</div>
                <div class="stat-label">Toko Trial</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="stat-card is-dark">
                <div class="stat-icon"><i class="bi bi-shop-window"></i></div>
                <div class="stat-value">{{ number_format($summary['total_toko_keseluruhan']) }}</div>
                <div class="stat-label">Total Toko</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="stat-card is-primary">
                <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
                <div class="stat-value">Rp{{ number_format($summary['total_pendapatan_bulan_ini'], 0, ',', '.') }}</div>
                <div class="stat-label">Pendapatan Bulan Ini</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="stat-card is-warning">
                <div class="stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="stat-value">Rp{{ number_format($summary['total_pendapatan_keseluruhan'], 0, ',', '.') }}</div>
                <div class="stat-label">Pendapatan Keseluruhan</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="bk-card h-100">
                <div class="bk-card-header">
                    <h2><i class="bi bi-alarm text-warning me-1"></i> Toko Mendekati Expired</h2>
                    <span class="badge-soft badge-soft-secondary">{{ $tokoMendekatiExpired->count() }} toko dalam 30 hari</span>
                </div>
                <div class="bk-card-body p-0">
                    @if ($tokoMendekatiExpired->isEmpty())
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-emoji-smile fs-3 d-block mb-2"></i>
                            Tidak ada toko yang akan expired dalam 30 hari ke depan.
                        </div>
                    @else
                        <div class="table-responsive table-responsive-wrap">
                            <table class="table bk-table mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>Nama Toko</th>
                                        <th>Kota</th>
                                        <th>Tanggal Expired</th>
                                        <th>Sisa Hari</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tokoMendekatiExpired as $toko)
                                        <tr
                                            class="row-clickable {{ $toko['sisa_hari'] <= 7 ? 'row-danger' : 'row-warning' }}"
                                            data-href="{{ route('admin.users.show', $toko['kode_owner']) }}"
                                        >
                                            <td class="fw-semibold">{{ $toko['nama_toko'] }}</td>
                                            <td>{{ $toko['kota'] }}</td>
                                            <td>{{ \Carbon\Carbon::parse($toko['exp_date'])->translatedFormat('d M Y') }}</td>
                                            <td>
                                                <span class="badge-soft {{ $toko['sisa_hari'] <= 7 ? 'badge-soft-danger' : 'badge-soft-warning' }}">
                                                    {{ $toko['sisa_hari'] }} hari
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

        <div class="col-lg-4">
            <div class="bk-card h-100">
                <div class="bk-card-header">
                    <h2><i class="bi bi-pie-chart-fill text-primary me-1"></i> Distribusi Status Toko</h2>
                </div>
                <div class="bk-card-body">
                    <canvas id="statusChart" height="220"></canvas>
                    <div class="d-flex justify-content-center gap-3 mt-3 small flex-wrap">
                        <span><span class="badge-soft badge-soft-success">&nbsp;</span> Premium</span>
                        <span><span class="badge-soft badge-soft-info">&nbsp;</span> Trial</span>
                        <span><span class="badge-soft badge-soft-danger">&nbsp;</span> Expired</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            var ctx = document.getElementById('statusChart');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Premium', 'Trial', 'Expired'],
                    datasets: [{
                        data: [
                            {{ $summary['total_toko_aktif'] }},
                            {{ $summary['total_toko_trial'] }},
                            {{ $summary['total_toko_expired'] }}
                        ],
                        backgroundColor: ['#16a34a', '#06b6d4', '#dc2626'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    cutout: '68%',
                    plugins: { legend: { display: false } }
                }
            });
        })();
    </script>
@endpush