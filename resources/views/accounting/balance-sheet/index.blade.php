@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Neraca (Balance Sheet)</h1>
    <small class="text-muted">Per {{ date('d/m/Y', strtotime($endDate)) }}</small>
  </div>
  <div class="row">
    <div class="col-md-6">
      <div class="card shadow mb-4"><div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">ASET</h6></div>
        <div class="card-body p-0">
          <table class="table table-sm mb-0">
            <tbody>@foreach($assets as $a) @if($a['balance']!=0) <tr><td>{{ $a['code'] }} - {{ $a['name'] }}</td><td class="text-right">{{ number_format($a['balance'],0) }}</td></tr> @endif @endforeach</tbody>
            <tfoot><tr class="font-weight-bold"><td>Total Aset</td><td class="text-right">{{ number_format($assets->sum('balance'),0) }}</td></tr></tfoot>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card shadow mb-4"><div class="card-header py-3"><h6 class="m-0 font-weight-bold text-danger">KEWAJIBAN</h6></div>
        <div class="card-body p-0">
          <table class="table table-sm mb-0">
            <tbody>@foreach($liabilities as $l) @if($l['balance']!=0) <tr><td>{{ $l['code'] }} - {{ $l['name'] }}</td><td class="text-right">{{ number_format($l['balance'],0) }}</td></tr> @endif @endforeach</tbody>
            <tfoot><tr class="font-weight-bold"><td>Total Kewajiban</td><td class="text-right">{{ number_format($liabilities->sum('balance'),0) }}</td></tr></tfoot>
          </table>
        </div>
      </div>
      <div class="card shadow mb-4"><div class="card-header py-3"><h6 class="m-0 font-weight-bold text-success">EKUITAS</h6></div>
        <div class="card-body p-0">
          <table class="table table-sm mb-0">
            <tbody>@foreach($equities as $e) @if($e['balance']!=0) <tr><td>{{ $e['code'] }} - {{ $e['name'] }}</td><td class="text-right">{{ number_format($e['balance'],0) }}</td></tr> @endif @endforeach
            <tr class="font-weight-bold text-info"><td>Laba Berjalan</td><td class="text-right">{{ number_format($netIncome,0) }}</td></tr></tbody>
            <tfoot><tr class="font-weight-bold"><td>Total Ekuitas</td><td class="text-right">{{ number_format($equities->sum('balance')+$netIncome,0) }}</td></tr></tfoot>
          </table>
        </div>
      </div>
      <div class="card shadow mb-4"><div class="card-body text-center">
        <strong>Total Kewajiban + Ekuitas:</strong> <span class="h5 ml-2">{{ number_format($liabilities->sum('balance')+$equities->sum('balance')+$netIncome,0) }}</span>
      </div></div>
    </div>
  </div>
</div>
@endsection
