@extends('layouts.app')

@section('title', 'Audit Stok Mendadak')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-apms">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title mb-0">Riwayat Audit Stok</h3>
                    <button type="button" class="btn btn-primary-apms btn-sm ml-auto" data-toggle="modal" data-target="#newAuditModal">
                        <i class="fas fa-plus"></i> Mulai Audit Acak
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal Audit</th>
                                    <th>Auditor</th>
                                    <th>Jumlah Produk</th>
                                    <th>Status</th>
                                    <th>Selisih (Unit)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($audits as $audit)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $audit->audit_date->format('d/m/Y H:i') }}</td>
                                    <td>{{ $audit->user?->name ?? '-' }}</td>
                                    <td>{{ $audit->items->count() }} Item</td>
                                    <td>
                                        @if($audit->status == 'draft')
                                            <span class="badge badge-warning">Dalam Proses</span>
                                        @else
                                            <span class="badge badge-success">Selesai</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $totalDiscrepancy = $audit->items->sum('discrepancy');
                                        @endphp
                                        @if($totalDiscrepancy == 0)
                                            <span class="text-success font-weight-bold">0 (Sesuai)</span>
                                        @else
                                            <span class="text-danger font-weight-bold">{{ $totalDiscrepancy > 0 ? '+' : '' }}{{ $totalDiscrepancy }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('stock_audits.show', $audit->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                            <form action="{{ route('stock_audits.destroy', $audit->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus riwayat audit ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada riwayat audit.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $audits->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal New Audit -->
<div class="modal fade" id="newAuditModal" tabindex="-1" role="dialog" aria-labelledby="newAuditModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary-apms">
                <h5 class="modal-title" id="newAuditModalLabel">Mulai Audit Stok Acak</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('stock_audits.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Jumlah Produk yang Akan Di-Audit *</label>
                        <select name="limit" class="form-control" required>
                            <option value="5">5 Produk Acak</option>
                            <option value="10">10 Produk Acak</option>
                            <option value="20">20 Produk Acak</option>
                        </select>
                        <small class="text-muted">Sistem akan memilih produk secara acak untuk diperiksa kecocokan stoknya.</small>
                    </div>
                    <div class="form-group">
                        <label>Catatan (Opsional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Contoh: Audit rutin mingguan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-apms">Mulai Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
