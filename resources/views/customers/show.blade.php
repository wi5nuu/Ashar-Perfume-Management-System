@extends('layouts.app')

@section('title', 'Detail Pelanggan')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">Detail Pelanggan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Pelanggan</a></li>
                    <li class="breadcrumb-item active">{{ $customer->name }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Profile Header Card -->
    <div class="card card-apms mb-4">
        <div class="card-body py-4">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle-large mr-4">
                            {{ strtoupper(substr($customer->name, 0, 2)) }}
                        </div>
                        <div>
                            <h2 class="mb-2 font-weight-bold">{{ $customer->name }}</h2>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="text-muted mr-3">
                                    <i class="fas fa-hashtag"></i> {{ $customer->customer_code ?? '-' }}
                                </span>
                                @if($customer->type == 'wholesale')
                                    <span class="badge badge-modern badge-info mr-2">
                                        <i class="fas fa-store mr-1"></i> Grosir
                                    </span>
                                @elseif($customer->type == 'vip')
                                    <span class="badge badge-modern badge-warning mr-2">
                                        <i class="fas fa-crown mr-1"></i> VIP
                                    </span>
                                @else
                                    <span class="badge badge-modern badge-secondary mr-2">
                                        <i class="fas fa-user mr-1"></i> Retail
                                    </span>
                                @endif
                                @if($customer->is_active)
                                    <span class="badge badge-modern badge-success">
                                        <i class="fas fa-check-circle mr-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge badge-modern badge-danger">
                                        <i class="fas fa-times-circle mr-1"></i> Nonaktif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-lg-right mt-3 mt-lg-0">
                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Metric Cards Row -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card card-modern h-100">
                <div class="card-body text-center">
                    <div class="metric-icon mb-3 mx-auto" style="background-color: rgba(255, 107, 53, 0.1);">
                        <i class="fas fa-shopping-cart fa-2x" style="color: var(--primary);"></i>
                    </div>
                    <h3 class="mb-1 font-weight-bold">Rp {{ number_format($customer->transactions->sum('total_amount'), 0, ',', '.') }}</h3>
                    <p class="text-muted mb-0">Total Pembelian</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card card-modern h-100">
                <div class="card-body text-center">
                    <div class="metric-icon mb-3 mx-auto" style="background-color: rgba(23, 162, 184, 0.1);">
                        <i class="fas fa-receipt fa-2x text-info"></i>
                    </div>
                    <h3 class="mb-1 font-weight-bold">{{ $customer->transactions->count() }}</h3>
                    <p class="text-muted mb-0">Jumlah Transaksi</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card card-modern h-100">
                <div class="card-body text-center">
                    <div class="metric-icon mb-3 mx-auto" style="background-color: rgba(40, 167, 69, 0.1);">
                        <i class="fas fa-chart-line fa-2x text-success"></i>
                    </div>
                    <h3 class="mb-1 font-weight-bold">
                        Rp {{ $customer->transactions->count() > 0 ? number_format($customer->transactions->sum('total_amount') / $customer->transactions->count(), 0, ',', '.') : '0' }}
                    </h3>
                    <p class="text-muted mb-0">Rata-rata Belanja</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card card-modern h-100">
                <div class="card-body text-center">
                    <div class="metric-icon mb-3 mx-auto" style="background-color: rgba(255, 193, 7, 0.1);">
                        <i class="fas fa-star fa-2x text-warning"></i>
                    </div>
                    <h3 class="mb-1 font-weight-bold">{{ floor($customer->transactions->sum('total_amount') / 10000) }}</h3>
                    <p class="text-muted mb-0">Poin Loyalty</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Section -->
    <div class="card card-apms">
        <div class="card-header p-0 border-bottom">
            <ul class="nav nav-tabs-custom" id="customerTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab">
                        <i class="fas fa-info-circle mr-2"></i>Info Umum
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="transactions-tab" data-toggle="tab" href="#transactions" role="tab">
                        <i class="fas fa-history mr-2"></i>Riwayat Transaksi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="debts-tab" data-toggle="tab" href="#debts" role="tab">
                        <i class="fas fa-hand-holding-usd mr-2"></i>Hutang/Cicilan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="loyalty-tab" data-toggle="tab" href="#loyalty" role="tab">
                        <i class="fas fa-gift mr-2"></i>Poin & Reward
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="customerTabsContent">
                <!-- Info Umum Tab -->
                <div class="tab-pane fade show active" id="info" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold mb-3">Data Pribadi</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%" class="text-muted">Kode Pelanggan</td>
                                    <td class="font-weight-bold">{{ $customer->customer_code ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nama Lengkap</td>
                                    <td class="font-weight-bold">{{ $customer->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">NIK (KTP)</td>
                                    <td class="font-weight-bold">{{ $customer->nik ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Jenis Kelamin</td>
                                    <td class="font-weight-bold">
                                        @if($customer->gender == 'male')
                                            Laki-laki
                                        @elseif($customer->gender == 'female')
                                            Perempuan
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Terdaftar Sejak</td>
                                    <td class="font-weight-bold">{{ $customer->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="font-weight-bold mb-3">Kontak & Preferensi</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%" class="text-muted">Telepon</td>
                                    <td class="font-weight-bold">
                                        <i class="fas fa-phone-alt text-primary mr-2"></i>{{ $customer->phone ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Email</td>
                                    <td class="font-weight-bold">
                                        <i class="fas fa-envelope text-primary mr-2"></i>{{ $customer->email ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Aroma Favorit</td>
                                    <td class="font-weight-bold">{{ $customer->aroma_preferences ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Alamat</td>
                                    <td class="font-weight-bold">{{ $customer->address ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Transaksi Tab -->
                <div class="tab-pane fade" id="transactions" role="tabpanel">
                    @if($customer->transactions->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada transaksi</h5>
                            <p class="text-muted">Riwayat transaksi pelanggan akan muncul di sini</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-modern">
                                <thead>
                                    <tr>
                                        <th>ID Transaksi</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->transactions->sortByDesc('created_at') as $transaction)
                                    <tr>
                                        <td class="font-weight-bold">#{{ $transaction->id }}</td>
                                        <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="font-weight-bold text-success">
                                            Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            <span class="badge badge-modern badge-success">Selesai</span>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Hutang/Cicilan Tab -->
                <div class="tab-pane fade" id="debts" role="tabpanel">
                    <div class="text-center py-5">
                        <i class="fas fa-hand-holding-usd fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada hutang outstanding</h5>
                        <p class="text-muted">Pelanggan tidak memiliki cicilan atau hutang yang belum lunas</p>
                    </div>
                </div>

                <!-- Poin & Reward Tab -->
                <div class="tab-pane fade" id="loyalty" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card card-modern">
                                <div class="card-body text-center">
                                    <i class="fas fa-star fa-3x text-warning mb-3"></i>
                                    <h3 class="font-weight-bold">{{ floor($customer->transactions->sum('total_amount') / 10000) }}</h3>
                                    <p class="text-muted mb-0">Total Poin Tersedia</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card card-modern">
                                <div class="card-body text-center">
                                    <i class="fas fa-gift fa-3x text-success mb-3"></i>
                                    <h3 class="font-weight-bold">0</h3>
                                    <p class="text-muted mb-0">Reward Terpakai</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Setiap Rp 10.000 pembelian = 1 poin loyalty. Poin dapat ditukar dengan diskon atau hadiah menarik.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-circle-large {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 28px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.metric-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-modern {
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.card-modern:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.nav-tabs-custom {
    border-bottom: 2px solid #e3e6f0;
}

.nav-tabs-custom .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 600;
    padding: 1rem 1.5rem;
    transition: all 0.3s ease;
}

.nav-tabs-custom .nav-link:hover {
    color: var(--primary);
    background-color: rgba(255, 107, 53, 0.05);
}

.nav-tabs-custom .nav-link.active {
    color: var(--primary);
    border-bottom: 3px solid var(--primary);
    background-color: transparent;
}

.badge-modern {
    padding: 0.4em 0.75em;
    font-weight: 600;
    border-radius: 4px;
    font-size: 0.8rem;
}

.table-modern thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    color: #495057;
}

.gap-2 > * {
    margin-right: 0.5rem;
}

@media (max-width: 768px) {
    .avatar-circle-large {
        width: 60px;
        height: 60px;
        font-size: 20px;
    }

    .nav-tabs-custom .nav-link {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Copy portal link
    window.copyPortalLink = function() {
        const linkInput = document.getElementById('portal-link');
        linkInput.select();
        document.execCommand('copy');
        
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Link portal telah disalin',
            timer: 1500,
            showConfirmButton: false
        });
    };
});
</script>
@endpush
