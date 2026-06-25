@php $title = 'Monitoring Owner'; @endphp
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Stats Row --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-primary">
                <div class="inner">
                    <h3>{{ number_format($todayRevenue, 0, ',', '.') }}</h3>
                    <p>Pendapatan Hari Ini</p>
                </div>
                <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-success">
                <div class="inner">
                    <h3>{{ $todayTransactions }}</h3>
                    <p>Transaksi Hari Ini</p>
                </div>
                <div class="icon"><i class="fas fa-shopping-cart"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-info">
                <div class="inner">
                    <h3>{{ $totalBranches }}</h3>
                    <p>Total Cabang</p>
                </div>
                <div class="icon"><i class="fas fa-store"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-warning">
                <div class="inner">
                    <h3>{{ $totalUsers }} <small style="font-size:14px;">users</small></h3>
                    <p>Total Pengguna Sistem</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Notifications Column --}}
        <div class="col-md-5">
            <div class="card card-apms">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-bell mr-1 text-warning"></i> Notifikasi
                        @if($unreadCount > 0)
                        <span class="badge badge-danger ml-1">{{ $unreadCount }}</span>
                        @endif
                    </h3>
                    @if($unreadCount > 0)
                    <form action="{{ route('owner.notifications.read-all') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-check-double"></i> Tandai Semua Dibaca
                        </button>
                    </form>
                    @endif
                </div>
                <div class="card-body p-0">
                    @forelse($notifications as $notif)
                    @php $data = $notif->data; @endphp
                    <div class="p-3 border-bottom {{ $notif->read_at ? '' : 'bg-light-apms' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-start">
                                <div class="mr-2">
                                    @if(($data['type'] ?? '') === 'password_reset_requested')
                                    <i class="fas fa-key text-warning fa-lg"></i>
                                    @elseif(($data['type'] ?? '') === 'password_reset_approved')
                                    <i class="fas fa-check-circle text-success fa-lg"></i>
                                    @else
                                    <i class="fas fa-bell text-info fa-lg"></i>
                                    @endif
                                </div>
                                <div>
                                    <strong>{{ $data['message'] ?? 'Notifikasi' }}</strong>
                                    @if(($data['type'] ?? '') === 'password_reset_requested')
                                    <div class="small text-muted mt-1">
                                        Role: {{ $data['user_role'] ?? '-' }} | 
                                        Cabang: {{ $data['user_branch'] ?? '-' }}
                                    </div>
                                    @endif
                                    <div class="small text-muted mt-1">
                                        {{ $notif->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                            @if(!$notif->read_at)
                            <form action="{{ route('owner.notifications.read', $notif->id) }}" method="POST" class="m-0">
                                @csrf
                                <button class="btn btn-sm btn-link text-muted p-0 ml-2" title="Tandai dibaca">
                                    <i class="fas fa-circle text-warning" style="font-size:8px;"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-bell-slash fa-3x mb-2 d-block"></i>
                        <h6>Tidak ada notifikasi</h6>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Password Reset Requests --}}
            <div class="card card-apms mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-key mr-1 text-warning"></i> Permintaan Reset Password
                        @if($pendingResets->count() > 0)
                        <span class="badge badge-danger ml-1">{{ $pendingResets->count() }} pending</span>
                        @endif
                    </h3>
                    <a href="{{ route('settings.password.reset-requests') }}" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-external-link-alt"></i> Kelola
                    </a>
                </div>
                <div class="card-body p-0">
                    @forelse($pendingResets as $req)
                    <div class="p-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $req->user->name ?? '-' }}</strong>
                                <div class="small text-muted">
                                    {{ $req->user->email ?? '-' }} 
                                    @if($req->user->branch)
                                    &middot; {{ $req->user->branch->name }}
                                    @endif
                                </div>
                                @if($req->notes)
                                <small class="text-muted d-block mt-1"><i class="fas fa-quote-left mr-1"></i>{{ $req->notes }}</small>
                                @endif
                                <small class="text-muted d-block mt-1">{{ $req->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="text-nowrap">
                                <form action="{{ route('settings.password.reset-approve', $req) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm" onclick="return confirm('Setujui reset untuk {{ $req->user->name }}?')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('settings.password.reset-reject', $req) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Tolak?')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                        <h6>Tidak ada permintaan pending</h6>
                    </div>
                    @endforelse
                </div>
                @if($recentResolvedResets->isNotEmpty())
                <div class="card-footer">
                    <small class="text-muted font-weight-bold">Riwayat Terbaru:</small>
                    @foreach($recentResolvedResets as $req)
                    <div class="d-flex justify-content-between small text-muted mt-1">
                        <span>{{ $req->user->name ?? '-' }}</span>
                        <span>
                            @if($req->status === 'approved')
                            <span class="text-success">Disetujui</span>
                            @else
                            <span class="text-danger">Ditolak</span>
                            @endif
                            &middot; {{ $req->resolved_at ? $req->resolved_at->diffForHumans() : '-' }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Branch Activity Column --}}
        <div class="col-md-7">
            {{-- Branch Activity Table --}}
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-store mr-1 text-primary"></i> Aktivitas Cabang Hari Ini
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Cabang</th>
                                    <th class="text-center">Karyawan</th>
                                    <th class="text-center">Staff Toko</th>
                                    <th class="text-center">Transaksi Hari Ini</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($branches as $branch)
                                <tr>
                                    <td><strong>{{ $branch->name }}</strong></td>
                                    <td class="text-center">{{ $branch->employee_count }}</td>
                                    <td class="text-center">{{ $branch->store_employee_count }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $branch->today_transactions > 0 ? 'success' : 'secondary' }}">
                                            {{ $branch->today_transactions }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($branch->today_transactions > 0)
                                        <span class="badge badge-success"><i class="fas fa-circle mr-1" style="font-size:6px;"></i> Aktif</span>
                                        @else
                                        <span class="badge badge-secondary"><i class="fas fa-circle mr-1" style="font-size:6px;"></i> Tidak Ada Aktivitas</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">Belum ada cabang</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Recent Transactions --}}
            <div class="card card-apms mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-shopping-cart mr-1 text-success"></i> Transaksi Terbaru (Semua Cabang)
                    </h3>
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-list"></i> Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentTransactions as $t)
                    <div class="p-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="font-weight-bold">{{ $t->invoice_number }}</small>
                                <div class="small text-muted">
                                    {{ $t->branch->name ?? '-' }} 
                                    &middot; {{ $t->user->name ?? '-' }}
                                    @if($t->customer)
                                    &middot; {{ $t->customer->name }}
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="font-weight-bold text-success">
                                    Rp {{ number_format($t->total_amount, 0, ',', '.') }}
                                </span>
                                <div class="small text-muted">{{ $t->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-receipt fa-2x mb-2 d-block"></i>
                        <h6>Belum ada transaksi</h6>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    // Auto-refresh every 60 seconds for pending count
    setTimeout(function() {
        location.reload();
    }, 60000);
});
</script>
@endpush
