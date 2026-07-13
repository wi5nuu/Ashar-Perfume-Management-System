@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Arus Kas (Cash Flow)</h1>
    <small>{{ date('d/m/Y',strtotime($startDate)) }} - {{ date('d/m/Y',strtotime($endDate)) }}</small>
  </div>
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <form method="GET" class="form-inline">
        <input type="date" name="start_date" class="form-control form-control-sm mr-2" value="{{ $startDate }}">
        <input type="date" name="end_date" class="form-control form-control-sm mr-2" value="{{ $endDate }}">
        <button class="btn btn-sm btn-primary">Tampilkan</button>
      </form>
    </div>
    <div class="card-body">
      <table class="table table-sm table-bordered">
        <tr><th colspan="2" class="text-success">ARUS KAS DARI AKTIVITAS OPERASI</th></tr>
        <tr><td>Penerimaan Kas dari Penjualan</td><td class="text-right">{{ number_format($cashIn,0) }}</td></tr>
        <tr><td>Pembayaran Kas untuk Beban</td><td class="text-right">({{ number_format($cashOut,0) }})</td></tr>
        <tr class="font-weight-bold"><td>Kas Bersih dari Aktivitas Operasi</td><td class="text-right">{{ number_format($cashIn-$cashOut,0) }}</td></tr>
        <tr><th colspan="2" class="text-warning">ARUS KAS DARI AKTIVITAS INVESTASI</th></tr>
        <tr><td colspan="2" class="text-center text-muted">(Belum tersedia)</td></tr>
        <tr><th colspan="2" class="text-info">ARUS KAS DARI AKTIVITAS PENDANAAN</th></tr>
        <tr><td colspan="2" class="text-center text-muted">(Belum tersedia)</td></tr>
      </table>
    </div>
  </div>
</div>
@endsection
