@extends('layouts.app')

@section('title', 'Manajemen Kas Bon (Piutang)')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">Daftar Piutang Pelanggan</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="d-none d-md-table-cell text-nowrap">#</th>
                                    <th class="text-nowrap">Invoice & Pelanggan</th>
                                    <th class="d-none d-lg-table-cell text-nowrap">Tanggal</th>
                                    <th class="d-none d-sm-table-cell text-nowrap">Tagihan</th>
                                    <th class="d-none d-md-table-cell text-nowrap">Dibayar</th>
                                    <th class="text-nowrap">Sisa Hutang</th>
                                    <th class="text-nowrap text-center">Status</th>
                                    <th class="text-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $trx)
                                <tr>
                                    <td class="d-none d-md-table-cell">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="font-weight-bold truncate-text">{{ $trx->invoice_number }}</div>
                                        <div class="text-xs-mobile text-muted">
                                            {{ $trx->customer->name ?? 'Umum' }}
                                        </div>
                                        <div class="d-lg-none text-xs-mobile text-muted">
                                            {{ $trx->created_at->format('d/m/y') }}
                                        </div>
                                    </td>
                                    <td class="d-none d-lg-table-cell text-nowrap">{{ $trx->created_at->format('d/m/Y') }}</td>
                                    <td class="d-none d-sm-table-cell text-nowrap">Rp {{ number_format($trx->final_amount, 0, ',', '.') }}</td>
                                    <td class="d-none d-md-table-cell text-nowrap">Rp {{ number_format($trx->paid_amount, 0, ',', '.') }}</td>
                                    <td class="text-nowrap"><span class="text-danger font-weight-bold">Rp {{ number_format($trx->debt_amount, 0, ',', '.') }}</span></td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $trx->payment_status == 'partial' ? 'warning' : 'danger' }} text-xs-mobile">
                                            {{ $trx->payment_status == 'partial' ? 'Cicil' : 'Hutang' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#paymentModal{{ $trx->id }}">
                                            <i class="fas fa-money-bill-wave mr-1"></i> Bayar
                                        </button>
                                    </td>
                                </tr>

                                <!-- Payment Modal -->
                                <div class="modal fade" id="paymentModal{{ $trx->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-success">
                                                <h5 class="modal-title text-white">Catat Pembayaran Hutang</h5>
                                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <form action="{{ route('debts.payment', $trx->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="row mb-3">
                                                        <div class="col-6">
                                                            <small class="text-muted">No. Invoice</small>
                                                            <p class="font-weight-bold mb-0">{{ $trx->invoice_number }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">Sisa Hutang</small>
                                                            <p class="font-weight-bold text-danger mb-0">Rp {{ number_format($trx->debt_amount, 0, ',', '.') }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Jumlah Pembayaran *</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                                            <input type="number" name="amount" class="form-control" max="{{ $trx->debt_amount }}" value="{{ $trx->debt_amount }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Metode Pembayaran *</label>
                                                        <select name="payment_method" class="form-control" required>
                                                            <option value="cash">Tunai (Laci Kasir)</option>
                                                            <option value="transfer">Transfer Bank</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Catatan</label>
                                                        <textarea name="notes" class="form-control" rows="2" placeholder="Contoh: Bayar lunas / Cicilan ke-2"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success">Simpan Pembayaran</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                                        <h5>Semua piutang telah lunas!</h5>
                                        <p class="text-muted">Tidak ada transaksi yang menunggak saat ini.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
