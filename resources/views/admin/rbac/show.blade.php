@extends('layouts.app')
@section('title', 'Izin Role: ' . $role->name . ' - APMS')

@section('content')
<div class="container-fluid pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-dark"><i class="fas fa-key mr-2 text-primary"></i>Izin Role: {{ $role->name }}</h4>
        </div>
        <a href="{{ route('admin.rbac.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif

    <form method="POST" action="{{ route('admin.rbac.sync-permissions', $role) }}">
        @csrf
        <div class="card card-apms shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="font-weight-bold">Daftar Izin</span>
                <button type="submit" class="btn btn-primary-apms btn-sm"><i class="fas fa-save mr-1"></i> Simpan Izin</button>
            </div>
            <div class="card-body">
                @foreach($permissions as $group => $perms)
                <div class="mb-4">
                    <h6 class="font-weight-bold text-primary border-bottom pb-2">{{ $group }}</h6>
                    <div class="row">
                        @foreach($perms as $perm)
                        <div class="col-md-4 col-sm-6 mb-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="permissions[]"
                                    id="perm_{{ $perm['id'] }}" value="{{ $perm['id'] }}"
                                    {{ $role->permissions->contains($perm['id']) ? 'checked' : '' }}
                                    {{ $role->is_system && $role->slug === 'owner' ? 'disabled' : '' }}>
                                <label class="custom-control-label" for="perm_{{ $perm['id'] }}">
                                    {{ $perm['name'] }}
                                    <small class="d-block text-muted">{{ $perm['description'] }}</small>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </form>
</div>
@endsection
