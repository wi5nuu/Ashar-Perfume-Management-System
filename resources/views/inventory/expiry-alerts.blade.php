@extends('layouts.app')

@section('title', 'Alert Kadaluarsa Produk')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">Alert Kadaluarsa Produk</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventaris</a></li>
                    <li class="breadcrumb-item active">Kadaluarsa</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">

    {{-- Alert Summary Strip --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card card-modern border-left-danger">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="expiry-icon mr-3" style="background:rgba(220,53,69,0.12);">
                            <i class="fas fa-skull-crossbones text-danger"></i>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">Sudah Kadaluarsa</div>
                            <h3 class="mb-0 font-weight-bold text-danger">
                                {{ $critical->filter(fn($i) => \Carbon\Carbon::parse($i->expiry_date)->isPast())->count() }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card card-modern border-left-warning">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="expiry-icon mr-3" style="background:rgba(255,193,7,0.12);">
                            <i class="fas fa-exclamation-circle text-warning"></i>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">Kadaluarsa &le; 7 Hari</div>
                            <h3 class="mb-0 font-weight-bold text-warning">
                                {{ $critical->filter(fn($i) => !(\Carbon\Carbon::parse($i->expiry_date)->isPast()) && \Carbon\Carbon::parse($i->expiry_date)->diffInDays(now()) <= 7)->count() }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card card-modern border-left-orange">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="expiry-icon mr-3" style="background:rgba(255,107,53,0.12);">
                            <i class="fas fa-hourglass-half" style="color:var(--primary);"></i>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">Kadaluarsa &le; 30 Hari</div>
                            <h3 class="mb-0 font-weight-bold" style="color:var(--primary);">
                                {{ $critical->count() }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card card-modern border-left-info">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="expiry-icon mr-3" style="background:rgba(23,162,184,0.12);">
                            <i class="fas fa-clock text-info"></i>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">Kadaluarsa 31-90 Hari</div>
                            <h3 class="mb-0 font-weight-bold text-info">
                                {{ $warning->count() + $notice->count() }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Critical / Expired Table --}}
    <div class="card card-apms mb-4">
        <div class="card-header d-flex align-items-center">
            <h5 class="card-title mb-0 font-weight-bold">
                <span class="expiry-dot bg-danger mr-2"></span>
                <i class="fas fa-skull-crossbones mr-2 text-danger"></i>
                Kadaluarsa / Dalam 30 Hari
                <span class="badge badge-danger ml-2">{{ $critical->count() }}</span>
            </h5>
            <div class="ml-auto">
                <button class="btn btn-outline-danger btn-sm" onclick="disposeAll('critical')">
                    <i class="fas fa-trash-alt mr-1"></i> Tandai Semua Dibuang
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            @if($critical->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="text-muted">Tidak ada produk kritis</h5>
                    <p class="text-muted">Semua produk masih dalam batas aman</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Batch / Lot</th>
                                <th>Gudang</th>
                                <th class="text-center">Qty</th>
                                <th>Tgl Kadaluarsa</th>
                                <th class="text-center">Sisa Hari</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($critical as $item)
                            @php
                                $expiry = \Carbon\Carbon::parse($item->expiry_date);
                                $daysLeft = now()->diffInDays($expiry, false);
                                $isExpired = $expiry->isPast();
                            @endphp
                            <tr class="{{ $isExpired ? 'row-expired' : 'row-critical' }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="product-thumb-sm mr-2">
                                            <i class="fas fa-spray-can"></i>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $item->product->name ?? '-' }}</div>
                                            <small class="text-muted">{{ $item->product->sku ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="text-muted">{{ $item->batch_number ?? '-' }}</span></td>
                                <td>{{ $item->warehouse->name ?? 'Utama' }}</td>
                                <td class="text-center font-weight-bold">{{ $item->quantity }}</td>
                                <td>
                                    <span class="{{ $isExpired ? 'text-danger font-weight-bold' : 'text-warning font-weight-bold' }}">
                                        {{ $expiry->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($isExpired)
                                        <span class="badge badge-modern badge-danger">
                                            <i class="fas fa-times mr-1"></i> Kadaluarsa
                                        </span>
                                    @else
                                        <span class="badge badge-modern badge-warning">
                                            {{ abs($daysLeft) }} hari lagi
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-danger btn-sm"
                                                onclick="markDisposed({{ $item->id }})"
                                                title="Tandai Dibuang">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        <a href="{{ route('inventory.index') }}"
                                           class="btn btn-info btn-sm"
                                           title="Lihat Produk">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Warning: 31-60 Hari --}}
    <div class="card card-apms mb-4">
        <div class="card-header d-flex align-items-center">
            <h5 class="card-title mb-0 font-weight-bold">
                <span class="expiry-dot bg-warning mr-2"></span>
                <i class="fas fa-hourglass-half mr-2 text-warning"></i>
                Kadaluarsa dalam 31-60 Hari
                <span class="badge badge-warning ml-2">{{ $warning->count() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($warning->isEmpty())
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <p class="text-muted mb-0">Tidak ada produk dalam rentang ini</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Batch / Lot</th>
                                <th>Gudang</th>
                                <th class="text-center">Qty</th>
                                <th>Tgl Kadaluarsa</th>
                                <th class="text-center">Sisa Hari</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($warning as $item)
                            @php
                                $expiry = \Carbon\Carbon::parse($item->expiry_date);
                                $daysLeft = now()->diffInDays($expiry, false);
                            @endphp
                            <tr class="row-warning">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="product-thumb-sm mr-2">
                                            <i class="fas fa-spray-can"></i>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $item->product->name ?? '-' }}</div>
                                            <small class="text-muted">{{ $item->product->sku ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="text-muted">{{ $item->batch_number ?? '-' }}</span></td>
                                <td>{{ $item->warehouse->name ?? 'Utama' }}</td>
                                <td class="text-center font-weight-bold">{{ $item->quantity }}</td>
                                <td>
                                    <span class="text-warning font-weight-bold">{{ $expiry->format('d/m/Y') }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-modern badge-warning">{{ abs($daysLeft) }} hari</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-danger btn-sm" onclick="markDisposed({{ $item->id }})" title="Tandai Dibuang">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        <a href="{{ route('inventory.index') }}" class="btn btn-info btn-sm" title="Lihat Produk">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Notice: 61-90 Hari --}}
    <div class="card card-apms mb-4">
        <div class="card-header d-flex align-items-center">
            <h5 class="card-title mb-0 font-weight-bold">
                <span class="expiry-dot bg-info mr-2"></span>
                <i class="fas fa-clock mr-2 text-info"></i>
                Perhatian: 61-90 Hari
                <span class="badge badge-info ml-2">{{ $notice->count() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($notice->isEmpty())
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <p class="text-muted mb-0">Tidak ada produk dalam rentang ini</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Batch / Lot</th>
                                <th>Gudang</th>
                                <th class="text-center">Qty</th>
                                <th>Tgl Kadaluarsa</th>
                                <th class="text-center">Sisa Hari</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notice as $item)
                            @php
                                $expiry = \Carbon\Carbon::parse($item->expiry_date);
                                $daysLeft = now()->diffInDays($expiry, false);
                            @endphp
                            <tr class="row-notice">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="product-thumb-sm mr-2">
                                            <i class="fas fa-spray-can"></i>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $item->product->name ?? '-' }}</div>
                                            <small class="text-muted">{{ $item->product->sku ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="text-muted">{{ $item->batch_number ?? '-' }}</span></td>
                                <td>{{ $item->warehouse->name ?? 'Utama' }}</td>
                                <td class="text-center font-weight-bold">{{ $item->quantity }}</td>
                                <td>
                                    <span class="text-info font-weight-bold">{{ $expiry->format('d/m/Y') }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-modern badge-info">{{ abs($daysLeft) }} hari</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-danger btn-sm" onclick="markDisposed({{ $item->id }})" title="Tandai Dibuang">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        <a href="{{ route('inventory.index') }}" class="btn btn-info btn-sm" title="Lihat Produk">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
.card-modern {
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}
.card-modern:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}
.border-left-danger  { border-left: 4px solid #dc3545 !important; }
.border-left-warning { border-left: 4px solid #ffc107 !important; }
.border-left-orange  { border-left: 4px solid var(--primary) !important; }
.border-left-info    { border-left: 4px solid #17a2b8 !important; }
.expiry-icon {
    width: 46px;
    height: 46px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.expiry-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
}
.product-thumb-sm {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    background: linear-gradient(135deg, #e9ecef, #dee2e6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 0.85rem;
    flex-shrink: 0;
}
.row-expired { background-color: rgba(220, 53, 69, 0.06) !important; }
.row-expired:hover { background-color: rgba(220, 53, 69, 0.1) !important; }
.row-critical { background-color: rgba(255, 193, 7, 0.06) !important; }
.row-critical:hover { background-color: rgba(255, 193, 7, 0.1) !important; }
.row-warning { background-color: rgba(255, 107, 53, 0.04) !important; }
.row-warning:hover { background-color: rgba(255, 107, 53, 0.08) !important; }
.row-notice { background-color: rgba(23, 162, 184, 0.04) !important; }
.row-notice:hover { background-color: rgba(23, 162, 184, 0.08) !important; }
.table-modern thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.72rem;
    letter-spacing: 0.5px;
    color: #495057;
    padding: 0.9rem 0.75rem;
    white-space: nowrap;
}
.table-modern td { vertical-align: middle; padding: 0.8rem 0.75rem; }
.badge-modern {
    padding: 0.35em 0.65em;
    font-weight: 600;
    border-radius: 4px;
    font-size: 0.75rem;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    window.markDisposed = function(id) {
        Swal.fire({
            title: 'Tandai sebagai Dibuang?',
            text: 'Produk akan dicatat sebagai disposed dan stok akan dikurangi.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tandai Dibuang',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Produk telah ditandai sebagai disposed',
                    timer: 1800,
                    showConfirmButton: false
                }).then(() => location.reload());
            }
        });
    };

    window.disposeAll = function(group) {
        Swal.fire({
            title: 'Buang Semua Produk Kritis?',
            text: 'Semua produk kadaluarsa/kritis akan ditandai sebagai disposed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Buang Semua',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Semua produk kritis telah ditandai sebagai disposed',
                    timer: 1800,
                    showConfirmButton: false
                }).then(() => location.reload());
            }
        });
    };
});
</script>
@endpush
