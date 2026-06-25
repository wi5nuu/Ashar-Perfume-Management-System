@extends('layouts.app')

@section('title', 'Detail Pelanggan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i> {{ $customer->name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th style="width: 40%">Kode Pelanggan</th>
                                    <td>{{ $customer->customer_code }}</td>
                                </tr>
                                <tr>
                                    <th>Nama</th>
                                    <td>{{ $customer->name }}</td>
                                </tr>
                                <tr>
                                    <th>Telepon</th>
                                    <td>{{ $customer->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $customer->email ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th style="width: 40%">Tipe Pelanggan</th>
                                    <td>
                                        @if($customer->type == 'wholesale')
                                            <span class="badge badge-info">Wholesale</span>
                                        @elseif($customer->type == 'vip')
                                            <span class="badge badge-success">VIP</span>
                                        @else
                                            <span class="badge badge-secondary">Retail</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($customer->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Terdaftar</th>
                                    <td>{{ $customer->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alamat</label>
                        <p>{{ $customer->address ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">Statistik</h3>
                </div>
                <div class="card-body">
                    <div class="info-box bg-light">
                        <div class="info-box-icon bg-primary">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Transaksi</span>
                            <span class="info-box-number">{{ $customer->transactions->count() }}</span>
                        </div>
                    </div>

                    <div class="info-box bg-light">
                        <div class="info-box-icon bg-success">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Belanja</span>
                            <span class="info-box-number">
                                Rp {{ number_format($customer->transactions->sum('total_amount'), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="info-box bg-light">
                        <div class="info-box-icon bg-warning">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="info-box-content">
                            <span class="info-box-text">Poin Loyalty</span>
                            <span class="info-box-number">
                                {{ floor($customer->transactions->sum('total_amount') / 10000) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-apms mt-3">
                <div class="card-header">
                    <h3 class="card-title">Portal Pelanggan</h3>
                </div>
                <div class="card-body">
                    @if($customer->portal_token)
                        <p class="small text-muted mb-2">Bagikan link ini kepada pelanggan untuk akses portal mandiri:</p>
                        <div class="input-group input-group-sm mb-2">
                            <input type="text" class="form-control" id="portal-link" value="{{ route('portal.dashboard', $customer->portal_token) }}" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('portal-link').value); $(this).text('Copied!');">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <a href="{{ route('portal.dashboard', $customer->portal_token) }}" target="_blank" class="btn btn-sm btn-primary-apms btn-block">
                            <i class="fas fa-external-link-alt mr-1"></i> Buka Portal
                        </a>
                    @else
                        <p class="small text-muted mb-2">Belum ada link portal. Generate untuk memberi akses mandiri.</p>
                        <form action="{{ route('customers.portal-token', $customer->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary-apms btn-block">
                                <i class="fas fa-link mr-1"></i> Generate Portal Link
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card card-apms mt-3">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Transaksi Terbaru</h3>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @if($customer->transactions->isEmpty())
                        <p class="text-muted text-center">Belum ada transaksi</p>
                    @else
                        @foreach($customer->transactions->sortByDesc('created_at')->take(5) as $transaction)
                        <div class="mb-2 pb-2 border-bottom">
                            <div class="d-flex justify-content-between">
                                <strong>#{{ $transaction->id }}</strong>
                                <span class="text-success">
                                    Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                </span>
                            </div>
                            <small class="text-muted">{{ $transaction->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
