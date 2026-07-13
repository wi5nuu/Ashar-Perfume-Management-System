@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-4 text-gray-800">Nilai Persediaan (Stock Valuation)</h1>
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <form method="GET" class="form-inline">
        <select name="branch_id" class="form-control form-control-sm mr-2">
          <option value="">Semua Cabang</option>
          @foreach(App\Models\Branch::all() as $b)
          <option value="{{ $b->id }}" {{ $branchId==$b->id ? 'selected' : '' }}>{{ $b->name }}</option>
          @endforeach
        </select>
        <button class="btn btn-sm btn-primary">Tampilkan</button>
      </form>
    </div>
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-4"><strong>Total Item:</strong> {{ number_format($totalItems) }}</div>
        <div class="col-md-4"><strong>Total Nilai:</strong> Rp {{ number_format($totalValue, 0) }}</div>
      </div>
      <table class="table table-sm table-bordered">
        <thead><tr><th>Produk</th><th>SKU</th><th>Cabang</th><th class="text-right">Stok</th><th class="text-right">Harga Rata-rata</th><th class="text-right">Nilai</th></tr></thead>
        <tbody>
          @forelse($items as $i)
          <tr><td>{{ $i['product'] }}</td><td>{{ $i['sku'] }}</td><td>{{ $i['branch'] }}</td>
            <td class="text-right">{{ number_format($i['stock']) }}</td>
            <td class="text-right">{{ number_format($i['avg_price'],0) }}</td>
            <td class="text-right">{{ number_format($i['value'],0) }}</td></tr>
          @empty <tr><td colspan="6" class="text-center">Belum ada data</td></tr> @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
