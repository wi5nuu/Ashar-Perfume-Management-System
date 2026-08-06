@extends('layouts.app')

@section('title', 'Manajemen Karyawan')

@section('content')
{{-- All styles loaded globally from resources/sass/_apms.scss --}}

<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-users mr-2"></i>Manajemen Karyawan</h1>
                    <p class="mb-0 mt-1" style="color:rgba(255,255,255,.65);font-size:.82rem;">
                        Kelola data karyawan, akses sistem, dan informasi kepegawaian
                    </p>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active">Karyawan</li>
                    </ol>
                </div>
                @if(auth()->user()->isOwner())
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('employees.create') }}" class="btn btn-primary-apms">
                        <i class="fas fa-plus mr-1"></i> Tambah Karyawan
                    </a>
                    <a href="{{ route('admin.rbac.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-shield-alt mr-1"></i> Hak Akses
                    </a>
                    <button class="btn btn-outline-light btn-sm" onclick="window.print()">
                        <i class="fas fa-download mr-1"></i> Export
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="container-fluid pt-2 pb-4">
    <x-alert />

    {{-- KPI Cards --}}
    <div class="row mb-4" style="row-gap:1rem;">
        <div class="col-6 col-md-3">
            <div class="kpi-card kpi-orange">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="kpi-value">{{ $employees->total() ?? $employees->count() }}</div>
                        <div class="kpi-label">Total Karyawan</div>
                        <div class="kpi-trend text-muted"><i class="fas fa-users"></i> Semua tipe</div>
                    </div>
                    <div class="kpi-icon" style="background:rgba(255,107,53,.12);color:var(--primary);">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card kpi-green">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="kpi-value" style="color:#1a7a45;">
                            {{ $employees->filter(fn($e) => !$e->is_store_employee)->count() }}
                        </div>
                        <div class="kpi-label">Akses Login</div>
                        <div class="kpi-trend" style="color:#1a7a45;"><i class="fas fa-arrow-up mr-1"></i>Aktif</div>
                    </div>
                    <div class="kpi-icon" style="background:#e8f9f0;color:#1a7a45;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card kpi-blue">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="kpi-value" style="color:#3b5bdb;">
                            {{ $employees->filter(fn($e) => $e->is_store_employee)->count() }}
                        </div>
                        <div class="kpi-label">Karyawan Toko</div>
                        <div class="kpi-trend" style="color:#3b5bdb;"><i class="fas fa-store mr-1"></i>Absensi</div>
                    </div>
                    <div class="kpi-icon" style="background:#e8f0fe;color:#3b5bdb;">
                        <i class="fas fa-user-clock"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card kpi-purple">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="kpi-value" style="color:#6f42c1;">
                            {{ $employees->filter(fn($e) => $e->created_at && $e->created_at->isCurrentMonth())->count() }}
                        </div>
                        <div class="kpi-label">Baru Bulan Ini</div>
                        <div class="kpi-trend" style="color:#6f42c1;"><i class="fas fa-calendar-plus mr-1"></i>{{ now()->format('M Y') }}</div>
                    </div>
                    <div class="kpi-icon" style="background:#f3e8ff;color:#6f42c1;">
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar d-flex flex-wrap align-items-center justify-content-between" style="gap:.75rem;">
        <div class="d-flex flex-wrap align-items-center" style="gap:.5rem;">
            {{-- Type tabs --}}
            <a href="{{ route('employees.index', ['filter' => 'all']) }}"
               class="btn filter-tab-btn {{ $filter === 'all' ? 'active' : '' }}">
                <i class="fas fa-users mr-1"></i> Semua
            </a>
            <a href="{{ route('employees.index', ['filter' => 'login']) }}"
               class="btn filter-tab-btn {{ $filter === 'login' ? 'active' : '' }}">
                <i class="fas fa-user-shield mr-1"></i> Akses Login
            </a>
            <a href="{{ route('employees.index', ['filter' => 'store']) }}"
               class="btn filter-tab-btn {{ $filter === 'store' ? 'active' : '' }}">
                <i class="fas fa-user-clock mr-1"></i> Karyawan Toko
            </a>
        </div>
        <form action="{{ route('employees.index') }}" method="GET" class="d-flex flex-wrap" style="gap:.5rem;">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <div class="input-group input-group-sm" style="width:220px;">
                <div class="input-group-prepend">
                    <span class="input-group-text border-0 bg-light"><i class="fas fa-search text-muted"></i></span>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control border-0 bg-light"
                    placeholder="Cari nama, NIK, email...">
            </div>
            <select name="role" class="form-control form-control-sm border-0 bg-light" style="width:130px;">
                <option value="">Semua Posisi</option>
                <option value="owner"      {{ request('role')=='owner'?'selected':'' }}>Owner</option>
                <option value="admin"      {{ request('role')=='admin'?'selected':'' }}>Admin</option>
                <option value="manager"    {{ request('role')=='manager'?'selected':'' }}>Manager</option>
                <option value="supervisor" {{ request('role')=='supervisor'?'selected':'' }}>Supervisor</option>
                <option value="cashier"    {{ request('role')=='cashier'?'selected':'' }}>Kasir</option>
                <option value="warehouse"  {{ request('role')=='warehouse'?'selected':'' }}>Gudang</option>
            </select>
            <button type="submit" class="btn btn-primary-apms btn-sm">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search','role']))
            <a href="{{ route('employees.index', ['filter'=>$filter]) }}" class="btn btn-sm" style="border:1.5px solid #e0e3ef;border-radius:8px;color:#6b7280;">
                <i class="fas fa-times"></i>
            </a>
            @endif
        </form>
    </div>

    {{-- Table Card --}}
    <div class="card card-apms">
        <div class="card-body p-0">
            @if($employees->count())
            <div class="table-responsive">
                <table class="table-modern w-100">
                    <thead>
                        <tr>
                            <th style="padding-left:1.5rem;">Karyawan</th>
                            <th>Kontak</th>
                            <th>Cabang</th>
                            <th>Tipe</th>
                            <th>Posisi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                        @php
                            $colors = ['#FF6B35','#4e73df','#1cc88a','#6f42c1','#e83e8c','#fd7e14'];
                            $color  = $colors[crc32($employee->name) % count($colors)];
                            $initials = strtoupper(mb_substr($employee->nickname ?? $employee->name, 0, 1))
                                      . strtoupper(mb_substr(explode(' ', $employee->name)[1] ?? '', 0, 1));
                        @endphp
                        <tr>
                            <td style="padding-left:1.5rem;">
                                <div class="d-flex align-items-center" style="gap:.75rem;">
                                    <div class="employee-avatar" style="background:{{ $color }};">{{ $initials }}</div>
                                    <div>
                                        <div class="font-weight-bold" style="color:#2d3047;font-size:.9rem;">
                                            {{ $employee->nickname ?? $employee->name }}
                                        </div>
                                        <div style="font-size:.75rem;color:#8a8fa8;">
                                            {{ $employee->full_name ?? $employee->name }}
                                            @if($employee->employee_id)
                                            &nbsp;·&nbsp;<span style="color:var(--primary);">{{ $employee->employee_id }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($employee->email)
                                <div style="font-size:.82rem;color:#3d4268;">
                                    <i class="fas fa-envelope mr-1 text-muted" style="font-size:.7rem;"></i>{{ $employee->email }}
                                </div>
                                @endif
                                @if($employee->phone)
                                <div style="font-size:.82rem;color:#8a8fa8;">
                                    <i class="fas fa-phone mr-1 text-muted" style="font-size:.7rem;"></i>{{ $employee->phone }}
                                </div>
                                @endif
                            </td>
                            <td>
                                <span style="font-size:.85rem;color:#3d4268;">
                                    <i class="fas fa-map-marker-alt mr-1" style="color:var(--primary);font-size:.75rem;"></i>
                                    {{ $employee->branch->name ?? 'Pusat' }}
                                </span>
                            </td>
                            <td>
                                @if($employee->is_store_employee)
                                    <span class="badge-modern badge-store"><i class="fas fa-store" style="font-size:.65rem;"></i> Toko</span>
                                @else
                                    <span class="badge-modern badge-login"><i class="fas fa-user-shield" style="font-size:.65rem;"></i> Login</span>
                                @endif
                            </td>
                            <td>
                                @if($employee->role)
                                <span class="badge-modern" style="background:#f3e8ff;color:#6f42c1;">
                                    {{ ucfirst($employee->role) }}
                                </span>
                                @else
                                <span style="color:#c0c0c0;font-size:.8rem;">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center" style="gap:.35rem;">
                                    <a href="{{ route('employees.show', $employee) }}" class="action-btn view" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->isOwner())
                                    <a href="{{ route('employees.edit', $employee) }}" class="action-btn edit" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('employees.destroy', $employee) }}" method="POST"
                                          id="deleteForm{{ $employee->id }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button" class="action-btn delete btn-delete"
                                                data-form-id="deleteForm{{ $employee->id }}"
                                                data-name="{{ $employee->nickname ?? $employee->name }}"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-users"></i></div>
                <h5 style="color:#3d4268;font-weight:600;">Belum ada karyawan</h5>
                <p class="text-muted" style="font-size:.88rem;">
                    @if(request('search'))
                        Tidak ada karyawan yang cocok dengan pencarian "<strong>{{ request('search') }}</strong>".
                    @else
                        Mulai dengan menambahkan karyawan pertama Anda.
                    @endif
                </p>
                @if(auth()->user()->isOwner())
                <a href="{{ route('employees.create') }}" class="btn btn-primary-apms mt-2">
                    <i class="fas fa-plus mr-1"></i> Tambah Karyawan
                </a>
                @endif
            </div>
            @endif
        </div>
        @if($employees->count())
        <div class="card-footer bg-white border-0 d-flex align-items-center justify-content-between py-3 px-4" style="border-top:1px solid #f2f3f8!important;">
            <span style="font-size:.82rem;color:#8a8fa8;">
                Menampilkan {{ $employees->firstItem() ?? 1 }}–{{ $employees->lastItem() ?? $employees->count() }}
                dari {{ $employees->total() ?? $employees->count() }} karyawan
            </span>
            <div>{{ $employees->appends(request()->query())->links() }}</div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.btn-delete').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var name = this.dataset.name || 'karyawan ini';
        var formId = this.dataset.formId;
        Swal.fire({
            title: 'Hapus Karyawan?',
            html: 'Data <strong>' + name + '</strong> akan dihapus permanen.<br><small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#E55A2B',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true,
        }).then(function(result) {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    });
});
</script>
@endpush
