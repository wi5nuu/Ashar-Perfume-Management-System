@extends('layouts.app')
@section('title', 'Laporan Aging Hutang')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0"><i class="fas fa-hourglass-half"></i> Laporan Aging Hutang</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('debts.index') }}">Hutang</a></li>
                    <li class="breadcrumb-item active">Aging Report</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    {{-- Summary Cards --}}
    <div class="row mb-4">
        @php
            $bucketColors = ['0-7 hari' => 'success', '8-30 hari' => 'warning', '31-60 hari' => 'orange', '60+ hari' => 'danger'];
            $bucketIcons = ['0-7 hari' => 'fa-clock', '8-30 hari' => 'fa-hourglass-half', '31-60 hari' => 'fa-hourglass', '60+ hari' => 'fa-exclamation-triangle'];
        @endphp
        @foreach($buckets as $bucket)
        <div class="col-md-3">
            <div class="small-box bg-{{ $bucketColors[$bucket] ?? 'secondary' }}">
                <div class="inner">
                    <h3>Rp {{ number_format(($grouped[$bucket] ?? collect())->sum('debt_amount'), 0, ',', '.') }}</h3>
                    <p>{{ $bucket }} ({{ ($grouped[$bucket] ?? collect())->count() }} transaksi)</p>
                </div>
                <div class="icon"><i class="fas {{ $bucketIcons[$bucket] ?? 'fa-info' }}"></i></div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card card-apms">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Detail Aging per Bucket</h3>
            <div>
                {{-- Branch Filter (owner only) --}}
                @if(auth()->user()->isOwner())
                <form method="GET" class="form-inline d-inline">
                    <select name="branch_id" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                        <option value="">Semua Cabang</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ $branchFilter == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </form>
                @endif
            </div>
        </div>
        <div class="card-body">
            @foreach($buckets as $bucket)
            @php $items = $grouped[$bucket] ?? collect(); @endphp
            <h5 class="font-weight-bold text-{{ $bucketColors[$bucket] ?? 'secondary' }} mt-3 mb-2">
                <i class="fas {{ $bucketIcons[$bucket] }} mr-1"></i> {{ $bucket }}
                <span class="badge badge-{{ $bucketColors[$bucket] ?? 'secondary' }}">{{ $items->count() }}</span>
            </h5>
            @if($items->count())
            <div class="table-responsive mb-3">
                <table class="table table-hover table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Tanggal</th>
                            <th class="text-center">Hari</th>
                            <th class="text-right">Total</th>
                            <th class="text-right">Sisa Hutang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $debt)
                        <tr>
                            <td><strong>{{ $debt->invoice_number }}</strong></td>
                            <td>{{ $customers[$debt->customer_id] ?? 'Umum' }}</td>
                            <td>{{ \Carbon\Carbon::parse($debt->created_at)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $debt->days_overdue > 60 ? 'danger' : ($debt->days_overdue > 30 ? 'warning' : 'secondary') }}">
                                    {{ $debt->days_overdue }} hari
                                </span>
                            </td>
                            <td class="text-right">Rp {{ number_format($debt->total_amount, 0, ',', '.') }}</td>
                            <td class="text-right font-weight-bold text-danger">Rp {{ number_format($debt->debt_amount, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="5" class="text-right">Subtotal:</td>
                            <td class="text-right text-danger">Rp {{ number_format($items->sum('debt_amount'), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
                <p class="text-muted mb-3">Tidak ada hutang di kategori ini.</p>
            @endif
            @endforeach
        </div>
    </div>
</div>
@endsection
