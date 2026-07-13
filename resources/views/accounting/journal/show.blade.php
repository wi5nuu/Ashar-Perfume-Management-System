@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Jurnal #{{ $journal->journal_number }}</h1>
    <div>
      @if($journal->status=='draft')
      <form method="POST" action="{{ route('accounting.journal.post', $journal->id) }}" class="d-inline">
        @csrf
        <button class="btn btn-success btn-sm" onclick="return confirm('Posting jurnal?')"><i class="fas fa-check"></i> Posting</button>
      </form>
      @endif
      <a href="{{ route('accounting.journal.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>
  </div>
  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-3"><strong>No. Jurnal:</strong> {{ $journal->journal_number }}</div>
        <div class="col-md-3"><strong>Tanggal:</strong> {{ $journal->date->format('d/m/Y') }}</div>
        <div class="col-md-3"><strong>Status:</strong>
          @if($journal->status=='posted')<span class="badge badge-success">Posted</span>@else<span class="badge badge-warning">Draft</span>@endif
        </div>
        <div class="col-md-3"><strong>Periode:</strong> {{ $journal->period->name ?? '-' }}</div>
      </div>
      <div class="mb-3"><strong>Deskripsi:</strong> {{ $journal->description }}</div>
      <table class="table table-sm table-bordered">
        <thead><tr><th>Kode</th><th>Nama Akun</th><th>Debit</th><th>Kredit</th><th>Memo</th></tr></thead>
        <tbody>
          @foreach($journal->details as $d)
          <tr><td>{{ $d->account->code ?? '-' }}</td><td>{{ $d->account->name ?? '-' }}</td>
            <td class="text-right">{{ $d->debit>0 ? number_format($d->debit,0) : '-' }}</td>
            <td class="text-right">{{ $d->credit>0 ? number_format($d->credit,0) : '-' }}</td>
            <td>{{ $d->memo }}</td></tr>
          @endforeach
        </tbody>
        <tfoot><tr class="font-weight-bold"><td colspan="2" class="text-right">TOTAL</td>
          <td class="text-right">{{ number_format($journal->total_debit,0) }}</td>
          <td class="text-right">{{ number_format($journal->total_credit,0) }}</td><td></td></tr></tfoot>
      </table>
    </div>
  </div>
</div>
@endsection
