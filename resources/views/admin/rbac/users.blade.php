@extends('layouts.app')
@section('title', 'Pengguna Role: ' . $role->name . ' - APMS')

@push('styles')
<style>
:root {
    --primary: #FF6B35;
    --primary-dark: #E55A2B;
    --secondary: #2D3047;
}
.page-header-apms {
    background: linear-gradient(135deg, var(--secondary) 0%, #3d4266 100%);
    border-radius: 16px;
    padding: 24px 28px;
    color: #fff;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
}
.page-header-apms::before {
    content: '';
    position: absolute;
    top: -30px; right: -30px;
    width: 130px; height: 130px;
    background: rgba(255,107,53,0.15);
    border-radius: 50%;
}
.breadcrumb-apms { background: transparent; padding: 0; margin: 0; }
.breadcrumb-apms .breadcrumb-item a { color: rgba(255,255,255,0.65); text-decoration: none; font-size: 0.82rem; }
.breadcrumb-apms .breadcrumb-item.active { color: rgba(255,255,255,0.9); font-size: 0.82rem; }
.breadcrumb-apms .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.4); }

.card-modern {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 14px rgba(0,0,0,0.07);
    border: 1px solid #f0f0f0;
    overflow: hidden;
}
.card-header-modern {
    padding: 16px 20px;
    border-bottom: 1px solid #f0f0f0;
    background: #fafafa;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.table-modern { width: 100%; border-collapse: collapse; }
.table-modern thead th {
    background: #f8f9fc;
    padding: 11px 16px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    border-bottom: 2px solid #eef0f4;
    white-space: nowrap;
}
.table-modern tbody td {
    padding: 12px 16px;
    font-size: 0.84rem;
    color: #333;
    border-bottom: 1px solid #f5f5f5;
    vertical-align: middle;
}
.table-modern tbody tr:hover { background: #fafbff; }
.table-modern tbody tr:last-child td { border-bottom: none; }

.user-avatar {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 700; font-size: 0.8rem;
    flex-shrink: 0;
}
.badge-role {
    display: inline-flex; align-items: center;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 600;
}
.badge-role-info { background: rgba(0,123,255,0.1); color: #007bff; border: 1px solid rgba(0,123,255,0.2); }
.badge-role-success { background: rgba(40,167,69,0.1); color: #28a745; border: 1px solid rgba(40,167,69,0.2); }
.badge-role-warning { background: rgba(255,193,7,0.1); color: #856404; border: 1px solid rgba(255,193,7,0.25); }

.btn-primary-apms {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border: none; color: #fff; border-radius: 8px;
    padding: 8px 18px; font-weight: 600; font-size: 0.84rem;
    transition: all .18s; display: inline-flex; align-items: center;
}
.btn-primary-apms:hover { background: linear-gradient(135deg, var(--primary-dark), #c94d22); color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(255,107,53,0.35); }

.filter-bar {
    background: #f8f9fc;
    border-radius: 10px;
    padding: 14px 16px;
    margin-bottom: 16px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: flex-end;
}
.filter-bar .form-control, .filter-bar .form-control-sm {
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    font-size: 0.83rem;
    background: #fff;
}
.filter-bar .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,0.12); }

.add-user-card {
    background: linear-gradient(135deg, #f8f9fc, #f0f3ff);
    border-radius: 12px;
    border: 1px dashed #c5cae9;
    padding: 16px 20px;
    margin-bottom: 20px;
}
.select2-container .select2-selection--single {
    border-radius: 8px !important;
    border: 1px solid #e0e0e0 !important;
    height: 38px !important;
}
</style>

@endpush

@section('content')
<div class="container-fluid pt-3">

    {{-- Page Header --}}
    <div class="page-header-apms">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-apms">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.rbac.index') }}">Roles & Permissions</a></li>
                <li class="breadcrumb-item active">Pengguna</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center justify-content-between mt-2">
            <div>
                <h4 class="mb-1 font-weight-bold" style="font-size:1.25rem;">
                    <i class="fas fa-users mr-2" style="color:var(--primary);"></i>Pengguna Role: <span style="color:var(--primary);">{{ $role->name }}</span>
                    @if(request('branch') === 'null')
                        <small class="font-weight-normal" style="color:rgba(255,255,255,0.65); font-size:0.82rem;">(Pusat)</small>
                    @elseif(request('branch') === 'notnull')
                        <small class="font-weight-normal" style="color:rgba(255,255,255,0.65); font-size:0.82rem;">(Cabang)</small>
                    @endif
                </h4>
                <p class="mb-0" style="color:rgba(255,255,255,0.65); font-size:0.84rem;">
                    {{ $users->total() }} pengguna terdaftar dalam role ini
                </p>
            </div>
            <div class="d-none d-md-flex" style="gap:8px;">
                <a href="{{ route('admin.rbac.show', $role) }}"
                   class="btn btn-sm"
                   style="background:rgba(255,255,255,0.15); color:#fff; border-radius:8px; border:1px solid rgba(255,255,255,0.25); font-size:0.83rem; font-weight:600;">
                    <i class="fas fa-key mr-1"></i> Edit Permissions
                </a>
                <a href="{{ route('admin.rbac.index') }}"
                   class="btn btn-sm"
                   style="background:rgba(255,255,255,0.1); color:#fff; border-radius:8px; border:1px solid rgba(255,255,255,0.2); font-size:0.83rem; font-weight:600;">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert border-0 mb-4" style="background:rgba(40,167,69,0.1); color:#155724; border-radius:10px; border-left:4px solid #28a745 !important;">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    {{-- Add User Card --}}
    <div class="add-user-card mb-4">
        <div class="d-flex align-items-center mb-3">
            <div style="width:36px; height:36px; background:rgba(255,107,53,0.12); border-radius:10px; display:flex; align-items:center; justify-content:center; margin-right:10px;">
                <i class="fas fa-user-plus" style="color:var(--primary);"></i>
            </div>
            <div>
                <div class="font-weight-bold" style="font-size:0.9rem; color:var(--secondary);">Tambah Pengguna ke Role</div>
                <div class="text-muted" style="font-size:0.75rem;">Tetapkan pengguna baru ke role {{ $role->name }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.rbac.assign-user', $role) }}" class="d-flex align-items-end flex-wrap" style="gap:10px;">
            @csrf
            <div class="flex-grow-1" style="min-width:200px;">
                <label class="text-muted mb-1" style="font-size:0.75rem; font-weight:600;">PILIH PENGGUNA</label>
                <select name="user_id" class="form-control select2" required style="border-radius:8px; border:1px solid #e0e0e0; font-size:0.84rem;">
                    <option value="">-- Pilih Pengguna --</option>
                    @foreach($availableUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary-apms">
                <i class="fas fa-plus mr-2"></i>Tambahkan
            </button>
        </form>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <form method="GET" class="d-flex flex-wrap align-items-end w-100" style="gap:10px;">
            <div style="flex:1; min-width:180px;">
                <label class="text-muted mb-1" style="font-size:0.72rem; font-weight:600; text-transform:uppercase;">CARI PENGGUNA</label>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text" style="background:#fff; border-right:none; border-radius:8px 0 0 8px;">
                            <i class="fas fa-search text-muted" style="font-size:0.75rem;"></i>
                        </span>
                    </div>
                    <input type="text" name="search" class="form-control" placeholder="Nama atau email..."
                           value="{{ request('search') }}"
                           style="border-left:none; border-radius:0 8px 8px 0;">
                </div>
            </div>
            <div style="min-width:130px;">
                <label class="text-muted mb-1" style="font-size:0.72rem; font-weight:600; text-transform:uppercase;">STATUS</label>
                <select name="status" class="form-control form-control-sm" style="border-radius:8px;">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div style="min-width:130px;">
                <label class="text-muted mb-1" style="font-size:0.72rem; font-weight:600; text-transform:uppercase;">CABANG</label>
                <select name="branch" class="form-control form-control-sm" style="border-radius:8px;">
                    <option value="">Semua</option>
                    <option value="null" {{ request('branch') === 'null' ? 'selected' : '' }}>Pusat</option>
                    <option value="notnull" {{ request('branch') === 'notnull' ? 'selected' : '' }}>Cabang</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary-apms btn-sm">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            @if(request()->anyFilled(['search','status','branch']))
            <a href="{{ route('admin.rbac.users', $role) }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px; font-size:0.83rem;">
                <i class="fas fa-times mr-1"></i> Reset
            </a>
            @endif
        </form>
    </div>

    {{-- Users Table --}}
    <div class="card-modern">
        <div class="card-header-modern">
            <span class="font-weight-bold" style="color:var(--secondary); font-size:0.9rem;">
                <i class="fas fa-list mr-2" style="color:var(--primary);"></i>Daftar Pengguna
            </span>
            <span class="badge badge-light" style="border-radius:20px; font-size:0.75rem; padding:4px 10px; color:#555;">
                {{ $users->total() }} pengguna
            </span>
        </div>
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Cabang</th>
                        <th>Status</th>
                        <th style="width:80px; text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar mr-3">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                                <div>
                                    <div class="font-weight-600" style="font-size:0.85rem; color:#222;">{{ $user->name }}</div>
                                    <div class="text-muted" style="font-size:0.72rem;">ID #{{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="font-size:0.83rem;">{{ $user->email }}</span>
                        </td>
                        <td>
                            <span class="badge-role badge-role-info">{{ $user->role }}</span>
                        </td>
                        <td>
                            @if($user->branch)
                                <span style="font-size:0.82rem;"><i class="fas fa-building text-muted mr-1" style="font-size:0.72rem;"></i>{{ $user->branch->name ?? $user->branch }}</span>
                            @else
                                <span class="text-muted" style="font-size:0.82rem;"><i class="fas fa-globe mr-1" style="font-size:0.72rem;"></i>Pusat</span>
                            @endif
                        </td>
                        <td>
                            @if(isset($user->is_active) && !$user->is_active)
                                <span class="badge-role badge-role-warning">Nonaktif</span>
                            @else
                                <span class="badge-role badge-role-success">Aktif</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <form method="POST" action="{{ route('admin.rbac.remove-user', [$role, $user]) }}"
                                  onsubmit="return confirmRemove(@js($user->name))">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        style="border-radius:7px; font-size:0.78rem; padding:4px 10px;"
                                        title="Hapus dari role">
                                    <i class="fas fa-user-minus"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-users text-muted" style="font-size:2.5rem; opacity:.25;"></i>
                            <p class="text-muted mt-3 mb-0" style="font-size:0.9rem;">Belum ada pengguna dalam role ini</p>
                            <small class="text-muted">Tambahkan pengguna menggunakan form di atas</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="p-3 border-top" style="background:#fafafa;">
            {{ $users->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
function confirmRemove(name) {
    return confirm('Hapus ' + name + ' dari role ini?');
}
</script>
@endpush
@endsection
