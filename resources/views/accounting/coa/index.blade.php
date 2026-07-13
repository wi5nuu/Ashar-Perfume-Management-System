@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Chart of Accounts</h1>
    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#coaModal"><i class="fas fa-plus"></i> Akun Baru</button>
  </div>
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <form method="GET" class="form-inline">
        <select name="type" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
          <option value="">Semua Tipe</option>
          @foreach(App\Models\ChartOfAccount::TYPES as $k => $v)
          <option value="{{ $k }}" {{ request('type')==$k ? 'selected' : '' }}>{{ $v }}</option>
          @endforeach
        </select>
        <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Cari..." value="{{ request('search') }}">
        <button class="btn btn-sm btn-secondary">Cari</button>
      </form>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm table-bordered">
          <thead><tr><th>Kode</th><th>Nama Akun</th><th>Tipe</th><th>Saldo Normal</th><th>Level</th><th>Status</th></tr></thead>
          <tbody>
            @forelse($accounts as $acc)
            <tr><td>{{ $acc->code }}</td><td>{{ $acc->name }}</td>
              <td>{{ App\Models\ChartOfAccount::TYPES[$acc->type] ?? $acc->type }}</td>
              <td>{{ ucfirst($acc->normal_balance) }}</td><td>{{ $acc->level }}</td>
              <td>{!! $acc->is_active ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Nonaktif</span>' !!}</td></tr>
            @empty <tr><td colspan="6" class="text-center">Belum ada akun</td></tr> @endforelse
          </tbody>
        </table>
      </div>
      {{ $accounts->links() }}
    </div>
  </div>
</div>
<div class="modal fade" id="coaModal"><div class="modal-dialog">
  <form method="POST" action="{{ route('accounting.coa.store') }}" class="modal-content">
    @csrf
    <div class="modal-header"><h5 class="modal-title">Tambah Akun Baru</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
    <div class="modal-body">
      <div class="form-group"><label>Kode Akun</label><input type="text" name="code" class="form-control" required></div>
      <div class="form-group"><label>Nama Akun</label><input type="text" name="name" class="form-control" required></div>
      <div class="form-group"><label>Tipe</label>
        <select name="type" class="form-control" required>
          @foreach(App\Models\ChartOfAccount::TYPES as $k => $v) <option value="{{ $k }}">{{ $v }}</option> @endforeach
        </select>
      </div>
      <div class="form-group"><label>Induk Akun</label>
        <select name="parent_id" class="form-control">
          <option value="">-- Tidak Ada --</option>
          @foreach(App\Models\ChartOfAccount::active()->orderBy('code')->get() as $p)
          <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group"><label>Deskripsi</label><textarea name="description" class="form-control" rows="2"></textarea></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
  </form>
</div></div>
@endsection
