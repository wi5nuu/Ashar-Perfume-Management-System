@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Deposit {{ $account->customer->name }}</h1>
    <div>
      <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#depositModal"><i class="fas fa-plus"></i> Setor</button>
      <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#withdrawModal"><i class="fas fa-minus"></i> Tarik</button>
    </div>
  </div>
  <div class="row"><div class="col-md-4 mb-4">
    <div class="card border-left-primary shadow h-100 py-2"><div class="card-body">
      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Saldo Saat Ini</div>
      <div class="h3 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($account->balance,0) }}</div>
    </div></div>
  </div></div>
  <div class="card shadow">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Riwayat Transaksi</h6></div>
    <div class="card-body">
      <table class="table table-sm">
        <thead><tr><th>Tanggal</th><th>Tipe</th><th>Jumlah</th><th>Saldo Sebelum</th><th>Saldo Sesudah</th><th>Keterangan</th></tr></thead>
        <tbody>
          @foreach($account->transactions as $t)
          <tr><td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
            <td><span class="badge badge-{{ $t->type=='deposit'?'success':'danger' }}">{{ ucfirst($t->type) }}</span></td>
            <td class="text-right">Rp {{ number_format($t->amount,0) }}</td>
            <td class="text-right">Rp {{ number_format($t->balance_before,0) }}</td>
            <td class="text-right">Rp {{ number_format($t->balance_after,0) }}</td>
            <td>{{ $t->description }}</td></tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="modal fade" id="depositModal"><div class="modal-dialog">
  <form method="POST" action="{{ route('customer-deposits.transaction',$account->id) }}" class="modal-content">
    @csrf <input type="hidden" name="type" value="deposit">
    <div class="modal-header"><h5>Setor Tunai</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
    <div class="modal-body">
      <div class="form-group"><label>Jumlah</label><input type="number" name="amount" class="form-control" step="0.01" min="0" required></div>
      <div class="form-group"><label>Keterangan</label><input type="text" name="description" class="form-control"></div>
    </div>
    <div class="modal-footer"><button type="submit" class="btn btn-success">Simpan</button></div>
  </form>
</div></div>
<div class="modal fade" id="withdrawModal"><div class="modal-dialog">
  <form method="POST" action="{{ route('customer-deposits.transaction',$account->id) }}" class="modal-content">
    @csrf <input type="hidden" name="type" value="withdrawal">
    <div class="modal-header"><h5>Tarik Tunai</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
    <div class="modal-body">
      <div class="form-group"><label>Jumlah</label><input type="number" name="amount" class="form-control" step="0.01" min="0" required></div>
      <div class="form-group"><label>Keterangan</label><input type="text" name="description" class="form-control"></div>
    </div>
    <div class="modal-footer"><button type="submit" class="btn btn-warning">Simpan</button></div>
  </form>
</div></div>
@endsection
