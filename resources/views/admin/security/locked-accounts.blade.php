@extends('layouts.app')
@section('title', 'Akun Terkunci - APMS')

@section('content')
<div class="container-fluid pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-dark"><i class="fas fa-lock mr-2 text-danger"></i>Akun Terkunci</h4>
            <p class="text-muted">Akun yang sedang diblokir karena aktivitas mencurigakan</p>
        </div>
        <a href="{{ route('admin.security.overview') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif

    <div class="card card-apms shadow-sm border-0">
        <div class="card-header bg-white py-3 font-weight-bold">
            <i class="fas fa-list mr-1"></i> Daftar Akun Terkunci
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Cabang</th>
                            <th>Role</th>
                            <th>Terkunci Hingga</th>
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
                            <td><small class="text-danger">{{ $user->locked_until ? $user->locked_until->format('d/m/Y H:i') : 'Permanen' }}</small></td>
                            <td>
                                <form method="POST" action="{{ route('admin.security.unlock-account', $user) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Buka kunci akun {{ $user->name }}?')">
                                        <i class="fas fa-unlock mr-1"></i> Buka
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.security.force-logout', $user) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Akhiri sesi {{ $user->name }}?')">
                                        <i class="fas fa-sign-out-alt mr-1"></i> Logout Paksa
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada akun yang terkunci</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
