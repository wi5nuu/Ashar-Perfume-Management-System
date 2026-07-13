@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Buku Besar (General Ledger)</h1>
  </div>
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <form method="GET" class="form-inline">
        <select name="account_id" class="form-control form-control-sm mr-2" required>
          <option value="">-- Pilih Akun --</option>
          @foreach($accounts as $acc)
          <option value="{{ $acc->id }}" {{ $accountId==$acc->id ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
          @endforeach
        </select>
        <input type="date" name="from" class="form-control form-control-sm mr-2" value="{{ request('from') }}">
        <input type="date" name="to" class="form-control form-control-sm mr-2" value="{{ request('to') }}">
        <button class="btn btn-sm btn-primary">Tampilkan</button>
        @if($accountId) <a href="{{ route('accounting.ledger.index') }}" class="btn btn-sm btn-secondary ml-2">Reset</a> @endif
      </form>
    </div>
    @if($accountId)
    <div class="card-body">
      <table class="table table-sm table-bordered">
        <thead><tr><th>Tanggal</th><th>No. Jurnal</th><th>Deskripsi</th><th>Debit</th><th>Kredit</th><th>Saldo</th></tr></thead>
        <tbody>
          @forelse($details as $d)
          <tr><td>{{ $d->journalEntry->date->format('d/m/Y') }}</td><td>{{ $d->journalEntry->journal_number }}</td>
            <td>{{ $d->journalEntry->description }}</td>
            <td class="text-right">{{ $d->debit>0 ? number_format($d->debit,0) : '-' }}</td>
            <td class="text-right">{{ $d->credit>0 ? number_format($d->credit,0) : '-' }}</td>
            <td class="text-right font-weight-bold">{{ number_format($d->running_balance,0) }}</td></tr>
          @empty <tr><td colspan="6" class="text-center">Tidak ada transaksi</td></tr> @endforelse
        </tbody>
        <tfoot><tr class="font-weight-bold"><td colspan="5" class="text-right">Saldo Akhir</td>
          <td class="text-right">{{ number_format($balance,0) }}</td></tr></tfoot>
      </table>
    </div>
    @endif
  </div>
</div>
@endsection
