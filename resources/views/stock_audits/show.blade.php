@extends('layouts.app')

@section('title', 'Detail Audit Stok')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-apms">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('stock_audits.index') }}" class="btn btn-outline-secondary btn-sm mr-3">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <h3 class="card-title mb-0 font-weight-bold">Audit Stok #{{ $stockAudit->id }} <span class="text-muted text-sm ml-2">({{ $stockAudit->audit_date->format('d/m/Y') }})</span></h3>
                    </div>
                    <div class="card-tools">
                        <span class="badge badge-{{ $stockAudit->status == 'draft' ? 'warning' : 'success' }} px-3 py-2 text-sm shadow-sm">
                            {{ $stockAudit->status == 'draft' ? 'Dalam Proses' : 'Selesai' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Informasi Audit</h5>
                            <table class="table table-sm">
                                <tr><td>Auditor</td><td>: {{ $stockAudit->user->name }}</td></tr>
                                <tr><td>Catatan</td><td>: {{ $stockAudit->notes ?? '-' }}</td></tr>
                            </table>
                        </div>
                    </div>

                    <form action="{{ route('stock_audits.update-items', $stockAudit->id) }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th class="text-center" style="width: 150px;">Stok Sistem (A)</th>
                                        <th class="text-center" style="width: 150px;">Stok Fisik (B)</th>
                                        <th class="text-center" style="width: 150px;">Selisih (B - A)</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stockAudit->items as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->product->name }}</strong><br>
                                            <small class="text-muted">{{ $item->product->size }} | {{ $item->product->brand }}</small>
                                        </td>
                                        <td class="text-center font-weight-bold">{{ $item->system_stock }}</td>
                                        <td>
                                            @if($stockAudit->status == 'draft')
                                                <input type="number" name="items[{{ $item->id }}][physical_stock]" 
                                                       class="form-control text-center physical-input" 
                                                       value="{{ $item->physical_stock ?? '' }}" 
                                                       data-system="{{ $item->system_stock }}" 
                                                       min="0" required>
                                            @else
                                                <div class="text-center font-weight-bold">{{ $item->physical_stock }}</div>
                                            @endif
                                        </td>
                                        <td class="text-center discrepancy-val">
                                            @if($item->physical_stock !== null)
                                                <span class="{{ $item->discrepancy == 0 ? 'text-success' : 'text-danger' }} font-weight-bold">
                                                    {{ $item->discrepancy > 0 ? '+' : '' }}{{ $item->discrepancy }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($stockAudit->status == 'draft')
                                                <input type="text" name="items[{{ $item->id }}][notes]" class="form-control form-control-sm" value="{{ $item->notes }}">
                                            @else
                                                {{ $item->notes ?? '-' }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($stockAudit->status == 'draft')
                        <div class="mt-4 text-right">
                            <button type="submit" name="save" class="btn btn-outline-primary">
                                <i class="fas fa-save mr-1"></i> Simpan Sementara
                            </button>
                            <button type="submit" name="complete" value="1" class="btn btn-success" onclick="return confirm('Apakah Anda sudah yakin semua hitungan sudah benar?')">
                                <i class="fas fa-check-double mr-1"></i> Selesaikan Audit
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.physical-input').on('input', function() {
            let physical = parseInt($(this).val()) || 0;
            let system = parseInt($(this).data('system'));
            let discrepancy = physical - system;
            let target = $(this).closest('tr').find('.discrepancy-val');
            
            let colorClass = discrepancy === 0 ? 'text-success' : 'text-danger';
            let prefix = discrepancy > 0 ? '+' : '';
            
            target.html(`<span class="${colorClass} font-weight-bold">${prefix}${discrepancy}</span>`);
        });
    });
</script>
@endpush
