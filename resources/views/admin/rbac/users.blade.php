@extends('layouts.app')
@section('title', 'Pengguna Role: ' . $role->name . ' - APMS')

@section('content')
<div class="container-fluid pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-dark"><i class="fas fa-users mr-2 text-primary"></i>Pengguna Role: {{ $role->name }}
                @if(request('branch') === 'null')
                    <small class="text-muted font-weight-normal">(Pusat)</small>
                @elseif(request('branch') === 'notnull')
                    <small class="text-muted font-weight-normal">(Cabang)</small>
                @endif
            </h4>
        </div>
        <div>
            <a href="{{ route('admin.rbac.show', $role) }}" class="btn btn-outline-primary btn-sm mr-1"><i class="fas fa-key mr-1"></i> Izin</a>
            <a href="{{ route('admin.rbac.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif

    <div class="card card-apms shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 font-weight-bold">
            <i class="fas fa-user-plus mr-1"></i> Tambah Pengguna ke Role
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.rbac.assign-user', $role) }}" class="form-inline">
                @csrf
                <div class="form-group mr-2 flex-grow-1">
                    <select name="user_id" class="form-control w-100 select2" required>
                        <option value="">-- Pilih Pengguna --</option>
                        @foreach($availableUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Tambahkan</button>
            </form>
        </div>
    </div>

    <div class="card card-apms shadow-sm border-0">
        <div class="card-header bg-white py-3 font-weight-bold">
            <i class="fas fa-list mr-1"></i> Daftar Pengguna
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Cabang</th>
                            <th>Role Saat Ini</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="font-weight-bold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->branch->name ?? 'Pusat' }}</td>
                            <td><span class="badge badge-info">{{ $user->role }}</span></td>
                            <td>
                                <form method="POST" action="{{ route('admin.rbac.remove-user', [$role, $user]) }}" onsubmit="return confirm('Hapus {{ $user->name }} dari role ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-user-minus"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pengguna dalam role ini</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
        <div class="card-footer bg-white">{{ $users->links() }}</div>
        @endif
    </div>
</div>
@endsection
