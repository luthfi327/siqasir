@extends('owner.layout')

@section('title', 'Kelola Pegawai')
@section('page-title', 'Kelola Pegawai')
@section('page-sub', 'Manajemen akun staf di semua cabang')

@section('content')

@php
    $idSelf    = session('owner_toko.id');
    $aktif     = $pegawaiList->where('status', 'Aktif')->count();
    $nonaktif  = $pegawaiList->where('status', 'Nonaktif')->count();
@endphp

{{-- ── Kartu Ringkasan ──────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card green">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-value">{{ $aktif }}</div>
            <div class="stat-label">Pegawai Aktif</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card amber">
            <div class="stat-icon"><i class="bi bi-person-slash"></i></div>
            <div class="stat-value">{{ $nonaktif }}</div>
            <div class="stat-label">Pegawai Nonaktif</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card blue">
            <div class="stat-icon"><i class="bi bi-person-badge"></i></div>
            <div class="stat-value">{{ $pegawaiList->count() }}</div>
            <div class="stat-label">Total Pegawai</div>
        </div>
    </div>
</div>

{{-- ── Tabel Pegawai ─────────────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="mb-0"><i class="bi bi-people me-2"></i>Daftar Semua Pegawai</h6>

        {{-- Filter tab sederhana via JS --}}
        <div class="btn-group btn-group-sm" role="group" id="filterGroup">
            <button type="button" class="btn btn-outline-secondary active" onclick="filterTable('semua', this)">
                Semua <span class="badge bg-secondary ms-1">{{ $pegawaiList->count() }}</span>
            </button>
            <button type="button" class="btn btn-outline-success" onclick="filterTable('Aktif', this)">
                Aktif <span class="badge bg-success ms-1">{{ $aktif }}</span>
            </button>
            <button type="button" class="btn btn-outline-danger" onclick="filterTable('Nonaktif', this)">
                Nonaktif <span class="badge bg-danger ms-1">{{ $nonaktif }}</span>
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        @if($pegawaiList->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people fs-2 d-block mb-2"></i>
                <div class="text-sm">Belum ada data pegawai.</div>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tablePegawai">
                <thead>
                    <tr>
                        <th>Nama Pegawai</th>
                        <th>Username</th>
                        <th>Hak Akses</th>
                        <th>Posisi</th>
                        <th>Cabang</th>
                        <th>Bergabung</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pegawaiList as $p)
                    @php $isSelf = ($p->id == $idSelf); @endphp
                    <tr data-status="{{ $p->status }}">
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                                     style="width:34px;height:34px;background:{{ $p->hak_akses === 'Owner' ? '#dcfce7' : '#eff6ff' }};font-size:.85rem;font-weight:700;color:{{ $p->hak_akses === 'Owner' ? '#166534' : '#1e40af' }}">
                                    {{ strtoupper(substr($p->nama_pegawai, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-sm">{{ $p->nama_pegawai }}</div>
                                    @if($isSelf)
                                        <span class="badge bg-secondary text-xs">Anda</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-muted text-sm">{{ $p->username }}</td>
                        <td>
                            @if($p->hak_akses === 'Owner')
                                <span class="badge bg-success px-2 py-1">Owner</span>
                            @elseif($p->hak_akses === 'Admin')
                                <span class="badge bg-primary px-2 py-1">Admin</span>
                            @else
                                <span class="badge bg-secondary px-2 py-1">{{ $p->hak_akses }}</span>
                            @endif
                        </td>
                        <td class="text-sm">{{ $p->posisi ?? '-' }}</td>
                        <td class="text-sm">
                            {{ $p->nama_toko ?? '-' }}
                            <span class="text-muted text-xs">#{{ $p->id_toko }}</span>
                        </td>
                        <td class="text-muted text-xs">
                            {{ \Carbon\Carbon::parse($p->created_at)->setTimezone('Asia/Jakarta')->translatedFormat('d M Y') }}
                        </td>
                        <td>
                            @if($p->status === 'Aktif')
                                <span class="badge badge-aktif px-3 py-1 rounded-pill">Aktif</span>
                            @else
                                <span class="badge badge-nonaktif px-3 py-1 rounded-pill">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($isSelf)
                                {{-- Tidak bisa toggle diri sendiri --}}
                                <button class="btn btn-sm btn-outline-secondary" disabled
                                        title="Tidak dapat mengubah akun Anda sendiri"
                                        data-bs-toggle="tooltip">
                                    <i class="bi bi-lock"></i>
                                </button>
                            @else
                                <form action="{{ route('owner.pegawai.toggle', $p->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('{{ $p->status === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' }} pegawai {{ $p->nama_pegawai }}?')">
                                    @csrf
                                    @if($p->status === 'Aktif')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="Nonaktifkan" data-bs-toggle="tooltip">
                                            <i class="bi bi-person-x me-1"></i>Nonaktifkan
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-outline-success"
                                                title="Aktifkan" data-bs-toggle="tooltip">
                                            <i class="bi bi-person-check me-1"></i>Aktifkan
                                        </button>
                                    @endif
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- Info soft-delete --}}
<p class="text-muted text-xs mt-3 mb-0">
    <i class="bi bi-info-circle me-1"></i>
    Data pegawai tidak dihapus permanen. Pegawai yang dinonaktifkan tidak dapat login ke aplikasi Android
    hingga diaktifkan kembali.
</p>

@endsection

@push('scripts')
<script>
function filterTable(status, btn) {
    // Update tombol aktif
    document.querySelectorAll('#filterGroup button').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // Filter baris tabel
    document.querySelectorAll('#tablePegawai tbody tr').forEach(row => {
        if (status === 'semua') {
            row.style.display = '';
        } else {
            row.style.display = row.dataset.status === status ? '' : 'none';
        }
    });
}
</script>
@endpush