@php $title = 'Permintaan Reset Password'; @endphp
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-key mr-1 text-warning"></i> Permintaan Reset Password
                    </h3>
                </div>
                <div class="card-body">
                    @if($pendingRequests->isEmpty() && $resolvedRequests->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-check-circle fa-4x mb-3 text-success d-block"></i>
                        <h5>Belum ada permintaan reset password</h5>
                    </div>
                    @else
                    {{-- Pending Requests --}}
                    @if($pendingRequests->isNotEmpty())
                    <h5 class="text-warning font-weight-bold mb-3">
                        <i class="fas fa-clock mr-1"></i> Permintaan Baru
                    </h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="bg-warning text-white">
                                <tr>
                                    <th>Pengguna</th>
                                    <th>Role</th>
                                    <th>Cabang</th>
                                    <th>Catatan</th>
                                    <th>Diminta</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingRequests as $req)
                                <tr>
                                    <td class="font-weight-bold">{{ $req->user?->name ?? '-' }}</td>
                                    <td>{{ $req->user?->role ?? '-' }}</td>
                                    <td>{{ $req->user?->branch?->name ?? 'Pusat' }}</td>
                                    <td>{{ $req->notes ?? '-' }}</td>
                                    <td>{{ $req->created_at->diffForHumans() }}</td>
                                    <td>
                                        <form action="{{ route('settings.password.reset-approve', $req) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Setujui reset password untuk ' + @js($req->user->name) + '?')">
                                                <i class="fas fa-check"></i> Reset & Tampilkan
                                            </button>
                                        </form>
                                        <form action="{{ route('settings.password.reset-reject', $req) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tolak permintaan ini?')">
                                                <i class="fas fa-times"></i> Tolak
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    {{-- Resolved History --}}
                    @if($resolvedRequests->isNotEmpty())
                    <h5 class="text-muted font-weight-bold mb-3">
                        <i class="fas fa-history mr-1"></i> Riwayat
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>Pengguna</th>
                                    <th>Status</th>
                                    <th>Password Baru</th>
                                    <th>Diproses Oleh</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resolvedRequests as $req)
                                <tr>
                                    <td>{{ $req->user?->name ?? '-' }}</td>
                                    <td>
                                        @if($req->status === 'approved')
                                        <span class="badge badge-success">Disetujui</span>
                                        @else
                                        <span class="badge badge-danger">Ditolak</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($req->status === 'approved' && $req->new_password)
                                        <code class="text-success font-weight-bold">{{ $req->new_password }}</code>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>{{ $req->resolver?->name ?? '-' }}</td>
                                    <td>{{ $req->resolved_at ? $req->resolved_at->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2">{{ $resolvedRequests->links() }}</div>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
