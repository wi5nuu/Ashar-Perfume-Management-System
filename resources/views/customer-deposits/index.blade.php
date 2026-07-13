@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Deposit Pelanggan</h1>
    <a href="{{ route('customer-deposits.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Deposit Baru</a>
  </div>
  <div class="card shadow mb-4">
    <div class="card-body">
      <table class="table table-sm table-bordered">
        <thead><tr><th>Pelanggan</th><th>Saldo</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
          @forelse($accounts as $a)
          <tr><td>{{ $a->customer->name }}</td><td class="text-right">Rp {{ number_format($a->balance,0) }}</td>
            <td><span class="badge badge-{{ $a->status=='active'?'success':'secondary' }}">{{ ucfirst($a->status) }}</span></td>
            <td><a href="{{ route('customer-deposits.show',$a->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td></tr>
          @empty <tr><td colspan="4" class="text-center">Belum ada deposit</td></tr> @endforelse
        </tbody>
      </table>
      {{ $accounts->links() }}
    </div>
  </div>
</div>
@endsection
