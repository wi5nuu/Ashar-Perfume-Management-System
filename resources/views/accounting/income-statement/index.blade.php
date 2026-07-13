@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Laporan Laba Rugi</h1>
    <small class="text-muted">Per {{ date('d/m/Y', strtotime($endDate)) }}</small>
  </div>
  <div class="card shadow mb-4">
    <div class="card-body">
      <h5 class="font-weight-bold text-success">PENDAPATAN</h5>
      <table class="table table-sm table-bordered mb-4">
        <thead><tr><th>Akun</th><th class="text-right">Jumlah</th></tr></thead>
        <tbody>
          @foreach($income as $i) @if($i['balance']!=0)
          <tr><td>{{ $i['code'] }} - {{ $i['name'] }}</td><td class="text-right">{{ number_format($i['balance'],0) }}</td></tr>
          @endif @endforeach
        </tbody>
        <tfoot><tr class="font-weight-bold"><td>Total Pendapatan</td><td class="text-right">{{ number_format($ti,0) }}</td></tr></tfoot>
      </table>
      <h5 class="font-weight-bold text-danger">BEBAN</h5>
      <table class="table table-sm table-bordered mb-4">
        <thead><tr><th>Akun</th><th class="text-right">Jumlah</th></tr></thead>
        <tbody>
          @foreach($expense as $e) @if($e['balance']!=0)
          <tr><td>{{ $e['code'] }} - {{ $e['name'] }}</td><td class="text-right">{{ number_format($e['balance'],0) }}</td></tr>
          @endif @endforeach
        </tbody>
        <tfoot><tr class="font-weight-bold"><td>Total Beban</td><td class="text-right">{{ number_format($te,0) }}</td></tr></tfoot>
      </table>
      <div class="row"><div class="col-md-6 offset-md-6">
        <table class="table table-sm">
          <tr class="{{ ($ti-$te)>=0 ? 'table-success' : 'table-danger' }}">
            <th>LABA / (RUGI) BERSIH</th>
            <th class="text-right">{{ number_format($ti-$te,0) }}</th>
          </tr>
        </table>
      </div></div>
    </div>
  </div>
</div>
@endsection
