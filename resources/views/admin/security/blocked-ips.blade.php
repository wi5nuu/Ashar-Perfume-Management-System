@extends('layouts.app')
@section('title', 'IP Diblokir - APMS')

@section('content')
<div class="container-fluid pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-dark"><i class="fas fa-ban mr-2 text-danger"></i>Alamat IP Diblokir</h4>
            <p class="text-muted">IP yang diblokir karena aktivitas mencurigakan</p>
        </div>
        <a href="{{ route('admin.security.overview') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif

    <div class="card card-apms shadow-sm border-0">
        <div class="card-header bg-white py-3 font-weight-bold">
            <i class="fas fa-list mr-1"></i> Daftar IP Diblokir
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr><th>IP Address</th><th>Alasan</th><th>Percobaan</th><th>Diblokir Hingga</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse($ips as $ip)
                        <tr>
                            <td><code>{{ $ip->ip_address }}</code></td>
                            <td>{{ $ip->reason ?? '-' }}</td>
                            <td>{{ $ip->attempts }}</td>
                            <td><small class="text-danger">{{ $ip->blocked_until ? $ip->blocked_until->format('d/m/Y H:i') : 'Permanen' }}</small></td>
                            <td>
                                <form method="POST" action="{{ route('admin.security.unblock-ip', $ip->ip_address) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Buka blokir IP {{ addslashes($ip->ip_address) }}?')">
                                        <i class="fas fa-unlock mr-1"></i> Buka
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada IP yang diblokir</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($ips->hasPages())
        <div class="card-footer bg-white">{{ $ips->links() }}</div>
        @endif
    </div>
</div>
@endsection
