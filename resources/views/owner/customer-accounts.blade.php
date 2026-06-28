@extends('layouts.app')

@section('title', 'Data Akun Pelanggan')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="font-weight-bold"><i class="fas fa-address-card mr-2"></i> Data Akun Pelanggan</h4>
            <p class="text-muted">Semua akun pelanggan yang memiliki akses login — password di-hash dan tidak dapat ditampilkan. Gunakan tombol <strong>Reset Password</strong> untuk memberikan password baru.</p>
        </div>
    </div>

    <div class="card card-apms border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Tipe</th>
                            <th>Bergabung</th>
                            <th>Terakhir Login</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $a)
                        <tr>
                            <td class="font-weight-bold">{{ $a->name }}</td>
                            <td><code>{{ $a->email }}</code></td>
                            <td>
                                <span class="text-muted" style="font-family:monospace">••••••••</span>
                            </td>
                            <td>
                                @if($a->role === 'wholesale_customer')
                                <span class="badge badge-info">Grosir</span>
                                @else
                                <span class="badge badge-secondary">{{ $a->role }}</span>
                                @endif
                            </td>
                            <td><small>{{ $a->created_at->format('d/m/Y') }}</small></td>
                            <td><small>{{ $a->last_login_at ? $a->last_login_at->format('d/m/Y H:i') : '-' }}</small></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada akun pelanggan.</td></tr>
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
// Password di-hash dan tidak bisa ditampilkan. Gunakan tombol Reset Password untuk memberikan password baru.
</script>
@endpush