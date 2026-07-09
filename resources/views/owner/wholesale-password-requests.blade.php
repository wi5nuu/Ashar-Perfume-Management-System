@extends('layouts.app')

@section('title', 'Permintaan Reset Password')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="font-weight-bold"><i class="fas fa-key mr-2"></i> Permintaan Reset Password</h4>
            <p class="text-muted">Daftar permintaan reset password dari pelanggan grosir.</p>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('owner.wholesale-customers') }}">
                <i class="fas fa-users mr-1"></i> Pelanggan Grosir
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('owner.wholesale-password-requests') }}">
                <i class="fas fa-key mr-1"></i> Permintaan Reset Password
                @if($requests->where('status', 'pending')->count() > 0)
                    <span class="badge badge-danger ml-1">{{ $requests->where('status', 'pending')->count() }}</span>
                @endif
            </a>
        </li>
    </ul>

    <div class="card card-apms border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Pelanggan</th>
                            <th>Email</th>
                            <th>Diminta</th>
                            <th>Status</th>
                            <th>Diproses</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                        <tr>
                            <td class="font-weight-bold">{{ $req->user->name ?? '-' }}</td>
                            <td><code>{{ $req->user->email ?? '-' }}</code></td>
                            <td><small>{{ $req->created_at->format('d/m/Y H:i') }}</small></td>
                            <td>
                                @if($req->status === 'pending')
                                    <span class="badge badge-warning">Menunggu</span>
                                @elseif($req->status === 'approved')
                                    <span class="badge badge-success">Disetujui</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                @if($req->resolved_at)
                                    <small>{{ $req->resolved_at->format('d/m/Y H:i') }}</small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td>
                                @if($req->status === 'pending')
                                    <button class="btn btn-sm btn-success" onclick="resolveRequest({{ $req->id }}, @js($req->user->name ?? ''), @js($req->user->email ?? ''))">
                                        <i class="fas fa-check mr-1"></i> Proses & Tampilkan Password
                                    </button>
                                @elseif($req->status === 'approved' && $req->new_password)
                                    <button class="btn btn-sm btn-outline-info" onclick="showPassword({{ $req->id }})">
                                        <i class="fas fa-eye mr-1"></i> Lihat Password
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada permintaan reset password.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function resolveRequest(id, name, email) {
    Swal.fire({
        title: 'Proses Permintaan?',
        html: 'Reset password <strong>' + name + '</strong> (' + email + ')?<br><small class="text-muted">Password baru akan dibuat dan ditampilkan.</small>',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Ya, Proses!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/owner/wholesale-password-requests/' + id + '/resolve',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Password Baru untuk ' + res.name,
                        html: 'Email: <strong>' + res.email + '</strong><br><br>Password: <code style="font-size:1.3em;background:#f0f0f0;padding:6px 12px;border-radius:4px">' + res.password + '</code><br><br><small class="text-muted">Kirim password ini ke pelanggan via WhatsApp atau email. Hanya ditampilkan sekali.</small>',
                        timer: 120000,
                        showConfirmButton: true,
                        confirmButtonText: 'Saya sudah mengirimkan',
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function() {
                    Swal.fire({icon:'error', title:'Gagal!', text:'Gagal mereset password.'});
                }
            });
        }
    });
}

function showPassword(id) {
    $.ajax({
        url: '/owner/wholesale-password-requests/' + id + '/resolve',
        method: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(res) {
            Swal.fire({
                icon: 'info',
                title: 'Password ' + res.name,
                html: 'Email: <strong>' + res.email + '</strong><br><br>Password: <code style="font-size:1.3em;background:#f0f0f0;padding:6px 12px;border-radius:4px">' + res.password + '</code><br><br><small class="text-muted">Password ini sudah dikirim sebelumnya.</small>',
                confirmButtonText: 'Tutup',
            });
        },
        error: function() {
            Swal.fire({icon:'error', title:'Gagal!', text:'Gagal mengambil password.'});
        }
    });
}
</script>
@endpush