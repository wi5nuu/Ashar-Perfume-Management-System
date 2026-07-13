@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Laporan Penjualan Harian</h1>
  </div>
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <form method="GET" class="form-inline">
        <input type="date" name="date" class="form-control form-control-sm mr-2" value="{{ $date }}">
        <button class="btn btn-sm btn-primary">Tampilkan</button>
      </form>
    </div>
    <div class="card-body">
      <div class="row mb-4">
        <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body">
          <h6>Total Penjualan</h6><h3>Rp {{ number_format($totalSales,0) }}</h3></div></div></div>
        <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body">
          <h6>Transaksi</h6><h3>{{ $totalTransactions }}</h3></div></div></div>
        <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body">
          <h6>Eceran</h6><h3>Rp {{ number_format($retailSales,0) }} ({{ $retailCount }})</h3></div></div></div>
        <div class="col-md-3"><div class="card bg-warning text-white"><div class="card-body">
          <h6>Grosir</h6><h3>Rp {{ number_format($wholesaleSales,0) }} ({{ $wholesaleCount }})</h3></div></div></div>
      </div>
      @if($topProducts->count())
      <h5>Produk Terlaris</h5>
      <table class="table table-sm table-bordered">
        <thead><tr><th>Produk</th><th>Qty</th><th>Total</th></tr></thead>
        <tbody>
          @foreach($topProducts as $p)
          <tr><td>{{ $p->product->name }}</td><td>{{ $p->qty }}</td><td>Rp {{ number_format($p->total,0) }}</td></tr>
          @endforeach
        </tbody>
      </table>
      @endif
    </div>
  </div>
</div>
@endsection
