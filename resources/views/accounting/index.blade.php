@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Akuntansi</h1>
    <div>
      <a href="{{ route('accounting.journal.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Jurnal Baru</a>
      <a href="{{ route('accounting.coa.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-book"></i> Kelola Akun</a>
    </div>
  </div>
  @if($currentPeriod)
  <div class="alert alert-info"><i class="fas fa-calendar-alt"></i> Periode Aktif: <strong>{{ $currentPeriod->name }}</strong> ({{ $currentPeriod->start_date->format('d M Y') }} - {{ $currentPeriod->end_date->format('d M Y') }})</div>
  @endif
  <div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card border-left-primary shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center">
        <div class="col mr-2"><div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Akun</div><div class="h5 mb-0 font-weight-bold text-gray-800">{{ $coaCount }}</div></div>
        <div class="col-auto"><i class="fas fa-book fa-2x text-gray-300"></i></div>
      </div></div></div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card border-left-success shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center">
        <div class="col mr-2"><div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Jurnal</div><div class="h5 mb-0 font-weight-bold text-gray-800">{{ $journalCount }}</div></div>
        <div class="col-auto"><i class="fas fa-file-invoice fa-2x text-gray-300"></i></div>
      </div></div></div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card border-left-warning shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center">
        <div class="col mr-2"><div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Belum Diposting</div><div class="h5 mb-0 font-weight-bold text-gray-800">{{ $unpostedCount }}</div></div>
        <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
      </div></div></div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-6 mb-4">
      <div class="card shadow"><div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Laporan Keuangan</h6></div>
        <div class="card-body">
          <div class="list-group">
            <a href="{{ route('accounting.trial-balance.index') }}" class="list-group-item list-group-item-action"><i class="fas fa-balance-scale"></i> Neraca Saldo</a>
            <a href="{{ route('accounting.ledger.index') }}" class="list-group-item list-group-item-action"><i class="fas fa-book-open"></i> Buku Besar</a>
            <a href="{{ route('accounting.income-statement.index') }}" class="list-group-item list-group-item-action"><i class="fas fa-chart-line"></i> Laba Rugi</a>
            <a href="{{ route('accounting.balance-sheet.index') }}" class="list-group-item list-group-item-action"><i class="fas fa-file-invoice-dollar"></i> Neraca</a>
            <a href="{{ route('accounting.cash-flow.index') }}" class="list-group-item list-group-item-action"><i class="fas fa-money-bill-wave"></i> Arus Kas</a>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6 mb-4">
      <div class="card shadow"><div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Periode Akuntansi</h6></div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm">
              <thead><tr><th>Nama</th><th>Periode</th><th>Status</th></tr></thead>
              <tbody>
                @forelse($periods as $p)
                <tr><td>{{ $p->name }}</td><td>{{ $p->start_date->format('d/m/Y') }} - {{ $p->end_date->format('d/m/Y') }}</td>
                  <td>{!! $p->is_closed ? '<span class="badge badge-secondary">Tutup</span>' : '<span class="badge badge-success">Aktif</span>' !!}</td></tr>
                @empty <tr><td colspan="3" class="text-center">Belum ada periode</td></tr> @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
