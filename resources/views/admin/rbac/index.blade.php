@extends('layouts.app')
@section('title', 'Manajemen Role & Izin - APMS')

@section('content')
<div class="container-fluid pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="font-weight-bold text-dark"><i class="fas fa-shield-alt mr-2 text-primary"></i>Manajemen Role & Izin</h3>
            <p class="text-muted">Atur hak akses pengguna berdasarkan peran</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif

    <div class="row">
        @php $adminPusatRole = $roles->firstWhere('slug', 'admin_pusat'); @endphp
        @php $adminCabangRole = $roles->firstWhere('slug', 'admin'); @endphp
        @foreach($roles as $role)
        @if($role->slug === 'admin_pusat')
        {{-- Admin Pusat --}}
        <div class="col-md-4 mb-4">
            <div class="card card-apms shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold mb-0">
                        <i class="fas fa-building text-danger mr-1"></i> Admin Pusat
                    </h5>
                    <span class="badge badge-secondary">System</span>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Pusat — mengelola seluruh cabang</p>
                    <div class="d-flex justify-content-between text-center mb-3">
                        <div>
                            <span class="d-block font-weight-bold h5 mb-0">{{ $adminPusatCount }}</span>
                            <small class="text-muted">Pengguna</small>
                        </div>
                        <div>
                            <span class="d-block font-weight-bold h5 mb-0">{{ $adminPusatRole ? $adminPusatRole->permissions_count : 0 }}</span>
                            <small class="text-muted">Izin</small>
                        </div>
                    </div>
                </div>
                @if($adminPusatRole)
                <div class="card-footer bg-white border-top-0">
                    <div class="btn-group btn-block">
                        <a href="{{ route('admin.rbac.show', $adminPusatRole) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-key mr-1"></i> Atur Izin
                        </a>
                        <a href="{{ route('admin.rbac.users', $adminPusatRole) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-users mr-1"></i> Pengguna
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @elseif($role->slug === 'admin')
        {{-- Admin Cabang --}}
        <div class="col-md-4 mb-4">
            <div class="card card-apms shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold mb-0">
                        <i class="fas fa-store text-danger mr-1"></i> Admin Cabang
                    </h5>
                    <span class="badge badge-secondary">System</span>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Cabang — mengelola 1 cabang spesifik ({{ $branches }} cabang aktif)</p>
                    <div class="d-flex justify-content-between text-center mb-3">
                        <div>
                            <span class="d-block font-weight-bold h5 mb-0">{{ $adminCabangCount }}</span>
                            <small class="text-muted">Pengguna</small>
                        </div>
                        <div>
                            <span class="d-block font-weight-bold h5 mb-0">{{ $adminCabangRole ? $adminCabangRole->permissions_count : 0 }}</span>
                            <small class="text-muted">Izin</small>
                        </div>
                    </div>
                </div>
                @if($adminCabangRole)
                <div class="card-footer bg-white border-top-0">
                    <div class="btn-group btn-block">
                        <a href="{{ route('admin.rbac.show', $adminCabangRole) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-key mr-1"></i> Atur Izin
                        </a>
                        <a href="{{ route('admin.rbac.users', $adminCabangRole) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-users mr-1"></i> Pengguna
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @else
        <div class="col-md-4 mb-4">
            <div class="card card-apms shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold mb-0">
                        @if($role->slug === 'owner')
                            <i class="fas fa-crown text-warning mr-1"></i>
                        @elseif($role->slug === 'manager')
                            <i class="fas fa-user-tie text-info mr-1"></i>
                        @elseif($role->slug === 'cashier')
                            <i class="fas fa-cash-register text-success mr-1"></i>
                        @else
                            <i class="fas fa-user mr-1"></i>
                        @endif
                        {{ $role->name }}
                    </h5>
                    @if($role->is_system)
                        <span class="badge badge-secondary">System</span>
                    @endif
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">{{ $role->description ?? 'Tidak ada deskripsi' }}</p>
                    <div class="d-flex justify-content-between text-center mb-3">
                        <div>
                            <span class="d-block font-weight-bold h5 mb-0">{{ $role->users_count }}</span>
                            <small class="text-muted">Pengguna</small>
                        </div>
                        <div>
                            <span class="d-block font-weight-bold h5 mb-0">{{ $role->permissions_count }}</span>
                            <small class="text-muted">Izin</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <div class="btn-group btn-block">
                        <a href="{{ route('admin.rbac.show', $role) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-key mr-1"></i> Atur Izin
                        </a>
                        <a href="{{ route('admin.rbac.users', $role) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-users mr-1"></i> Pengguna
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>
</div>
@endsection
