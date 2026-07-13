@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Target Penjualan</h1>
    <a href="{{ route('sales-targets.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Target Baru</a>
  </div>
  <div class="card shadow mb-4">
    <div class="card-body">
      <table class="table table-sm table-bordered">
        <thead><tr><th>Bulan</th><th>Cabang</th><th>Sales</th><th>Target</th><th>Aksi</th></tr></thead>
        <tbody>
          @forelse($targets as $t)
          <tr><td>{{ DateTime::createFromFormat('!m',$t->month)->format('F') }} {{ $t->year }}</td>
            <td>{{ $t->branch?->name ?? 'Semua' }}</td><td>{{ $t->user?->name ?? 'Semua' }}</td>
            <td class="text-right">Rp {{ number_format($t->target_amount,0) }}</td>
            <td><a href="{{ route('sales-targets.show',$t->id) }}" class="btn btn-sm btn-info"><i class="fas fa-chart-simple"></i></a></td></tr>
          @empty <tr><td colspan="5" class="text-center">Belum ada target</td></tr> @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
