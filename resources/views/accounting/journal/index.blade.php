@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Jurnal</h1>
    <a href="{{ route('accounting.journal.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Jurnal Baru</a>
  </div>
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <form method="GET" class="form-inline">
        <select name="status" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
          <option value="">Semua</option>
          <option value="draft" {{ request('status')=='draft' ? 'selected' : '' }}>Draft</option>
          <option value="posted" {{ request('status')=='posted' ? 'selected' : '' }}>Posted</option>
        </select>
        <input type="date" name="from" class="form-control form-control-sm mr-2" value="{{ request('from') }}">
        <input type="date" name="to" class="form-control form-control-sm mr-2" value="{{ request('to') }}">
        <button class="btn btn-sm btn-secondary">Filter</button>
      </form>
    </div>
    <div class="card-body">
      <table class="table table-sm table-bordered">
        <thead><tr><th>No. Jurnal</th><th>Tanggal</th><th>Deskripsi</th><th>Debit</th><th>Kredit</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
          @forelse($journals as $j)
          <tr><td>{{ $j->journal_number }}</td><td>{{ $j->date->format('d/m/Y') }}</td><td>{{ Str::limit($j->description, 50) }}</td>
            <td class="text-right">{{ number_format($j->total_debit, 0) }}</td><td class="text-right">{{ number_format($j->total_credit, 0) }}</td>
            <td>@if($j->status=='posted')<span class="badge badge-success">Posted</span>@else<span class="badge badge-warning">Draft</span>@endif</td>
            <td><a href="{{ route('accounting.journal.show', $j->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td></tr>
          @empty <tr><td colspan="7" class="text-center">Belum ada jurnal</td></tr> @endforelse
        </tbody>
      </table>
      {{ $journals->links() }}
    </div>
  </div>
</div>
@endsection
