@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Neraca Saldo (Trial Balance)</h1>
  </div>
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <form method="GET" class="form-inline">
        <input type="date" name="end_date" class="form-control form-control-sm mr-2" value="{{ $endDate }}">
        <button class="btn btn-sm btn-primary">Tampilkan</button>
      </form>
    </div>
    <div class="card-body">
      <table class="table table-sm table-bordered">
        <thead><tr><th>Kode</th><th>Nama Akun</th><th>Debit</th><th>Kredit</th></tr></thead>
        <tbody>
          @forelse($accounts as $acc)
          @if($acc['balance'] != 0)
          <tr><td>{{ $acc['code'] }}</td><td>{{ $acc['name'] }}</td>
            <td class="text-right">{{ $acc['debit']>0 ? number_format($acc['debit'],0) : '' }}</td>
            <td class="text-right">{{ $acc['credit']>0 ? number_format($acc['credit'],0) : '' }}</td></tr>
          @endif
          @empty <tr><td colspan="4" class="text-center">Belum ada data</td></tr> @endforelse
        </tbody>
        <tfoot><tr class="font-weight-bold"><td colspan="2" class="text-right">TOTAL</td>
          <td class="text-right">{{ number_format($accounts->sum('debit'),0) }}</td>
          <td class="text-right">{{ number_format($accounts->sum('credit'),0) }}</td></tr></tfoot>
      </table>
    </div>
  </div>
</div>
@endsection
