@extends('layouts.app')

@section('title', 'Gudang')

@section('content')
<div class="container-fluid">
    <div class="card card-apms">
        <div class="card-header d-flex align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-warehouse mr-2"></i> Daftar Gudang</h3>
            <a href="{{ route('warehouses.create') }}" class="btn btn-primary-apms btn-sm ml-auto">
                <i class="fas fa-plus mr-1"></i> Tambah Gudang
            </a>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-striped table-valign-middle">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Gudang</th>
                        <th>Cabang</th>
                        <th>Stok Items</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warehouses as $wh)
                    <tr>
                        <td><code>{{ $wh->code }}</code></td>
                        <td class="font-weight-bold">{{ $wh->name }}</td>
                        <td>{{ $wh->branch->name ?? '-' }}</td>
                        <td>{{ $wh->inventories->count() }}</td>
                        <td>
                            @if($wh->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('warehouses.edit', $wh->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('warehouses.destroy', $wh->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus gudang ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada gudang</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($warehouses->hasPages())
        <div class="card-footer">
            {{ $warehouses->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
