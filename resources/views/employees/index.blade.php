@extends('layouts.app')

@section('title', 'Manajemen Karyawan')

@section('content')
<div class="container-fluid pt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="font-weight-bold text-dark mb-0"><i class="fas fa-users mr-2 text-primary"></i>Manajemen Karyawan</h3>
        @if(auth()->user()->isOwner())
        <div class="btn-group">
            <a href="{{ route('employees.create') }}" class="btn btn-primary-apms">
                <i class="fas fa-plus mr-1"></i> Tambah Karyawan
            </a>
            <a href="{{ route('admin.rbac.index') }}" class="btn btn-outline-primary-apms ml-2">
                <i class="fas fa-shield-alt mr-1"></i> Atur Hak Akses
            </a>
        </div>
        @endif
    </div>

    {{-- Filter tabs --}}
    <div class="mb-3">
        <div class="btn-group btn-group-sm" role="group">
            <a href="{{ route('employees.index', ['filter' => 'all']) }}" class="btn {{ $filter === 'all' ? 'btn-primary-apms' : 'btn-outline-secondary' }}">
                <i class="fas fa-users mr-1"></i> Semua
            </a>
            <a href="{{ route('employees.index', ['filter' => 'login']) }}" class="btn {{ $filter === 'login' ? 'btn-primary-apms' : 'btn-outline-secondary' }}">
                <i class="fas fa-user-shield mr-1"></i> Akses Login
            </a>
            <a href="{{ route('employees.index', ['filter' => 'store']) }}" class="btn {{ $filter === 'store' ? 'btn-primary-apms' : 'btn-outline-secondary' }}">
                <i class="fas fa-user-clock mr-1"></i> Karyawan Toko
            </a>
        </div>
    </div>

    <div class="card card-apms shadow-sm">
        <div class="card-body p-0">
            @if($employees->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="pl-4">Nama</th>
                            <th>Kontak</th>
                            <th>Cabang</th>
                            <th>Tipe</th>
                            <th>Posisi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                        <tr>
                            <td class="pl-4">
                                <div class="font-weight-bold">{{ $employee->full_name ?? $employee->name }}</div>
                                <small class="text-muted">{{ $employee->employee_id ?? 'No ID' }}</small>
                            </td>
                            <td>
                                <div class="small">{{ $employee->email }}</div>
                                <small class="text-muted">{{ $employee->phone ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge badge-light border">{{ $employee->branch->name ?? 'Pusat' }}</span>
                            </td>
                            <td>
                                @if($employee->can_login)
                                    <span class="badge badge-success" title="Dapat login ke sistem"><i class="fas fa-lock-open mr-1"></i>Login</span>
                                @else
                                    <span class="badge badge-secondary" title="Hanya absensi"><i class="fas fa-clock mr-1"></i>Toko</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ ucfirst($employee->role) }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" id="deleteForm{{ $employee->id }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-form-id="deleteForm{{ $employee->id }}" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5 text-muted">Belum ada data karyawan.</div>
            @endif
        </div>
        <div class="card-footer bg-white border-0">
            {{ $employees->links() }}
        </div>
    </div>
</div>
@endsection
