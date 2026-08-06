@extends('layouts.app')
@section('title', 'Detail Supplier')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="page-header-apms mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h1 class="page-header-title"><i class="fas fa-truck mr-2"></i> {{ $supplier->name }}</h1>
                <p class="page-header-subtitle">
                    Detail & riwayat Purchase Order supplier
                    <span class="ml-2 badge {{ $supplier->is_active ? 'badge-success' : 'badge-secondary' }}"
                          style="font-size:0.75rem;border-radius:20px;padding:3px 10px;">
                        {{ $supplier->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </p>
            </div>
            <div class="d-flex gap-2">
                @can('manage_suppliers')
                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary-apms btn-sm">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                @endcan
                <a href="{{ route('suppliers.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Info Supplier --}}
        <div class="col-lg-4 mb-4">
            <div class="card card-apms h-100">
                <div class="card-header">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-building mr-2 text-primary-apms"></i> Informasi Supplier
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            <tr class="border-bottom">
                                <td class="text-muted pl-3 py-3" style="width:120px;">Nama</td>
                                <td class="py-3 pr-3 font-weight-bold">{{ $supplier->name }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="text-muted pl-3 py-2">Narahubung</td>
                                <td class="py-2 pr-3">{{ $supplier->contact_person ?: '-' }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="text-muted pl-3 py-2">Telepon</td>
                                <td class="py-2 pr-3">
                                    @if($supplier->phone)
                                        <a href="tel:{{ $supplier->phone }}" class="text-dark">
                                            <i class="fas fa-phone mr-1 text-success"></i>{{ $supplier->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="text-muted pl-3 py-2">Email</td>
                                <td class="py-2 pr-3">
                                    @if($supplier->email)
                                        <a href="mailto:{{ $supplier->email }}" class="text-dark">
                                            <i class="fas fa-envelope mr-1 text-info"></i>{{ $supplier->email }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="text-muted pl-3 py-2">Alamat</td>
                                <td class="py-2 pr-3">{{ $supplier->address ?: '-' }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="text-muted pl-3 py-2">Status</td>
                                <td class="py-2 pr-3">
                                    <span class="badge {{ $supplier->is_active ? 'badge-success' : 'badge-secondary' }}"
                                          style="border-radius:20px;padding:3px 10px;">
                                        {{ $supplier->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="text-muted pl-3 py-2">Bergabung</td>
                                <td class="py-2 pr-3">{{ $supplier->created_at->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted pl-3 py-2">Total PO</td>
                                <td class="py-2 pr-3 font-weight-bold" style="color:var(--primary)">
                                    {{ $supplier->purchase_orders_count }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Riwayat PO --}}
        <div class="col-lg-8 mb-4">
            <div class="card card-apms">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-file-invoice mr-2 text-warning"></i> Riwayat Purchase Order
                    </h5>
                    <a href="{{ route('purchase-orders.index') }}?supplier={{ $supplier->id }}"
                       class="btn btn-sm btn-outline-primary">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th>No. PO</th>
                                    <th>Tgl Order</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $po)
                                    @php
                                        $statusConfig = [
                                            'draft'     => ['class' => 'badge-secondary', 'label' => 'Draft'],
                                            'sent'      => ['class' => 'badge-info',      'label' => 'Dikirim'],
                                            'partial'   => ['class' => 'badge-warning',   'label' => 'Parsial'],
                                            'received'  => ['class' => 'badge-success',   'label' => 'Selesai'],
                                            'cancelled' => ['class' => 'badge-danger',    'label' => 'Batal'],
                                        ];
                                        $sc = $statusConfig[$po->status] ?? ['class' => 'badge-secondary', 'label' => $po->status];
                                    @endphp
                                    <tr>
                                        <td class="font-weight-bold">{{ $po->po_number }}</td>
                                        <td>{{ $po->order_date->format('d/m/Y') }}</td>
                                        <td class="font-weight-bold">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge {{ $sc['class'] }}" style="border-radius:12px;padding:3px 10px;">
                                                {{ $sc['label'] }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('purchase-orders.show', $po) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="fas fa-file-invoice fa-3x text-muted mb-3 d-block"></i>
                                            <span class="text-muted">Belum ada Purchase Order dari supplier ini.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
.font-weight-600 { font-weight: 600; }
</style>
@endpush
