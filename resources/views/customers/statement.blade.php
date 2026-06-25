@extends('layouts.app')
@section('title', 'Statement Pelanggan')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0"><i class="fas fa-file-alt"></i> Statement Pelanggan</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Pelanggan</a></li>
                    <li class="breadcrumb-item active">Statement</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card card-apms mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title"><i class="fas fa-user mr-2"></i>{{ $customer->name }}</h3>
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-print mr-1"></i> Cetak
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><strong>Telepon:</strong> {{ $customer->phone ?? '-' }}</div>
                <div class="col-md-4"><strong>Email:</strong> {{ $customer->email ?? '-' }}</div>
                <div class="col-md-4"><strong>Tipe:</strong> {{ ucfirst($customer->type ?? 'regular') }}</div>
            </div>
        </div>
    </div>

    <div class="card card-apms">
        <div class="card-header">
            <h3 class="card-title">Riwayat Transaksi & Pembayaran</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Invoice</th>
                            <th class="text-right">Debit (Hutang)</th>
                            <th class="text-right">Kredit (Bayar)</th>
                            <th class="text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $balance = 0; @endphp
                        @foreach($transactions as $trx)
                            {{-- Transaction entry --}}
                            @php
                                $debt = (float) $trx->debt_amount + ((float) $trx->total_amount - (float) $trx->paid_amount - (float) $trx->debt_amount > 0 ? (float) $trx->total_amount - (float) $trx->paid_amount - (float) $trx->debt_amount : 0);
                                $initialDebt = (float) $trx->total_amount - (float) $trx->paid_amount;
                                $balance += $initialDebt;
                            @endphp
                            <tr>
                                <td>{{ $trx->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge badge-info">Transaksi</span>
                                    {{ $trx->payment_method ?? '' }}
                                </td>
                                <td>{{ $trx->invoice_number }}</td>
                                <td class="text-right text-danger">Rp {{ number_format($initialDebt, 0, ',', '.') }}</td>
                                <td class="text-right">-</td>
                                <td class="text-right font-weight-bold">Rp {{ number_format($balance, 0, ',', '.') }}</td>
                            </tr>

                            {{-- Payments for this transaction --}}
                            @foreach($payments->where('transaction_id', $trx->id) as $pay)
                                @php $balance -= (float) $pay->amount; @endphp
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($pay->payment_date)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge badge-success">Pembayaran</span>
                                        {{ $pay->payment_method ?? '' }}
                                        @if($pay->notes)<small class="text-muted ml-1">{{ $pay->notes }}</small>@endif
                                    </td>
                                    <td>{{ $trx->invoice_number }}</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right text-success">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                                    <td class="text-right font-weight-bold">Rp {{ number_format($balance, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        @endforeach

                        @if($transactions->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat transaksi.</td>
                        </tr>
                        @endif
                    </tbody>
                    @if(!$transactions->isEmpty())
                    <tfoot>
                        <tr class="font-weight-bold h5">
                            <td colspan="5" class="text-right">SISA HUTANG:</td>
                            <td class="text-right {{ $balance > 0 ? 'text-danger' : 'text-success' }}">
                                Rp {{ number_format($balance, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <a href="{{ route('customers.index') }}" class="btn btn-light"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
</div>
@endsection
