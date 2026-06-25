@extends('portal.layout')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="card card-portal">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-file-invoice mr-2"></i> Riwayat Transaksi & Pembayaran</h5>
        <button onclick="window.print()" class="btn btn-sm btn-portal">
            <i class="fas fa-print mr-1"></i> Cetak
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Invoice</th>
                        <th>Total</th>
                        <th>Dibayar</th>
                        <th>Hutang</th>
                        <th>Metode</th>
                    </tr>
                </thead>
                <tbody>
                    @php $runningBalance = 0; @endphp
                    @forelse($transactions as $txn)
                    @php
                        $paid = $txn->paid_amount ?? 0;
                        $debt = (float) $txn->debt_amount;
                        $payments = $txn->debtPayments->sum('amount');
                        $remaining = max(0, $debt - $payments);
                        $runningBalance += $remaining;
                    @endphp
                    <tr>
                        <td>{{ $txn->created_at->format('d/m/Y') }}</td>
                        <td class="font-weight-bold">{{ $txn->invoice_number }}</td>
                        <td>Rp {{ number_format($txn->total_amount, 0, ',', '.') }}</td>
                        <td class="text-success">Rp {{ number_format($paid, 0, ',', '.') }}</td>
                        <td>
                            @if($remaining > 0)
                                <span class="text-danger font-weight-bold">Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                            @else
                                <span class="text-success"><i class="fas fa-check-circle"></i> Lunas</span>
                            @endif
                        </td>
                        <td><span class="badge badge-light">{{ strtoupper($txn->payment_method ?? '-') }}</span></td>
                    </tr>
                    @if($txn->debtPayments->count() > 0)
                        @foreach($txn->debtPayments as $dp)
                        <tr class="bg-light">
                            <td colspan="2">
                                <small class="text-muted ml-4">
                                    <i class="fas fa-arrow-right mr-1"></i>
                                    Cicilan — {{ $dp->created_at->format('d/m/Y') }}
                                </small>
                            </td>
                            <td colspan="4">
                                <small class="text-success">Rp {{ number_format($dp->amount, 0, ',', '.') }}</small>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada transaksi</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($transactions->count() > 0)
                <tfoot>
                    <tr class="font-weight-bold bg-light">
                        <td colspan="4" class="text-right">Total Sisa Hutang:</td>
                        <td class="text-danger">Rp {{ number_format($runningBalance, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
