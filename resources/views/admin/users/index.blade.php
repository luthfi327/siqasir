@extends('admin.layouts.app')

@section('title', 'Daftar Toko')
@section('page-title', 'Daftar Toko')

@section('content')

    <div class="bk-card mb-3">
        <div class="bk-card-body">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Status Akun</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="trial"   {{ $filterStatus === 'trial'   ? 'selected' : '' }}>Trial</option>
                        <option value="premium" {{ $filterStatus === 'premium' ? 'selected' : '' }}>Premium</option>
                        <option value="expired" {{ $filterStatus === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cari Toko</label>
                    <input type="text" name="search" class="form-control" placeholder="Nama toko, kota, atau nama owner..." value="{{ $search }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-bk-primary flex-fill">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    @if ($filterStatus || $search)
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" title="Reset filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="bk-card">
        <div class="bk-card-header">
            <h2><i class="bi bi-shop-window text-primary me-1"></i> Semua Toko</h2>
            <span class="badge-soft badge-soft-secondary">{{ $paginator->total() }} toko ditemukan</span>
        </div>

        <div class="bk-card-body p-0">
            @if ($items->isEmpty())
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                    Tidak ada toko yang cocok dengan filter saat ini.
                </div>
            @else
                <div class="table-responsive table-responsive-wrap">
                    <table class="table bk-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nama Toko</th>
                                <th>Nama Owner</th>
                                <th>No HP</th>
                                <th>Kota</th>
                                <th>Status</th>
                                <th>Exp Date</th>
                                <th>Umur Akun</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $toko)
                                <tr class="row-clickable" data-href="{{ route('admin.users.show', $toko['kode_owner']) }}">
                                    <td>
                                        @if ($toko['mendekati_expired'])
                                            <span title="Akan expired dalam {{ $toko['sisa_hari'] }} hari">🔔</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">
                                        {{ $toko['nama_toko'] }}
                                        @if ($toko['ada_promo_aktif'])
                                            <span class="badge-soft badge-soft-primary ms-1"><i class="bi bi-tag-fill"></i> Promo</span>
                                        @endif
                                    </td>
                                    <td>{{ $toko['nama_owner'] }}</td>
                                    <td>{{ $toko['no_hp'] }}</td>
                                    <td>{{ $toko['kota'] }}</td>
                                    <td>
                                        @if ($toko['status_akun'] === 'premium')
                                            <span class="badge-soft badge-soft-success">Premium</span>
                                        @elseif ($toko['status_akun'] === 'trial')
                                            <span class="badge-soft badge-soft-info">Trial</span>
                                        @else
                                            <span class="badge-soft badge-soft-danger">Expired</span>
                                        @endif
                                    </td>
                                    <td>{{ $toko['exp_date'] ? \Carbon\Carbon::parse($toko['exp_date'])->translatedFormat('d M Y') : '-' }}</td>
                                    <td>{{ $toko['umur_bulan'] }} bulan</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.users.show', $toko['kode_owner']) }}" class="btn btn-sm btn-outline-primary">
                                            Detail <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top">
                    {{ $paginator->onEachSide(1)->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>

@endsection