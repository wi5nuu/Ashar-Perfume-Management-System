@extends('layouts.app')

@section('title', 'Daftar Pengeluaran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-apms">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title mb-0">Daftar Pengeluaran Toko</h3>
                    <a href="{{ route('expenses.create') }}" class="btn btn-primary-apms btn-sm ml-auto">
                        <i class="fas fa-plus"></i> Catat Pengeluaran Baru
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <span class="info-box-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Pengeluaran</span>
                                    <span class="info-box-number text-danger">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="d-none d-md-table-cell text-nowrap">#</th>
                                    <th class="text-nowrap">Tanggal</th>
                                    <th class="d-none d-sm-table-cell text-nowrap">Kategori</th>
                                    <th class="text-nowrap">Deskripsi</th>
                                    <th class="d-none d-lg-table-cell text-nowrap">Vendor</th>
                                    <th class="text-nowrap">Jumlah (Rp)</th>
                                    <th class="d-none d-lg-table-cell text-nowrap">Bukti</th>
                                    <th class="text-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                <tr>
                                    <td class="d-none d-md-table-cell">{{ $loop->iteration }}</td>
                                    <td class="text-nowrap">
                                        <div class="font-weight-bold">{{ $expense->date ? \Carbon\Carbon::parse($expense->date)->format('d/m/Y') : '-' }}</div>
                                        <div class="d-sm-none text-xs-mobile text-muted">
                                            {{ $expense->category->name }}
                                        </div>
                                    </td>
                                    <td class="d-none d-sm-table-cell"><span class="badge badge-info">{{ $expense->category->name }}</span></td>
                                    <td>
                                        <div class="truncate-text font-weight-bold">{{ $expense->description }}</div>
                                        <div class="d-lg-none text-xs-mobile text-muted">
                                            V: {{ $expense->vendor ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="d-none d-lg-table-cell">{{ $expense->vendor ?? '-' }}</td>
                                    <td class="text-right font-weight-bold text-nowrap">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                    <td class="d-none d-lg-table-cell">
                                        @if($expense->proof_image)
                                            <a href="{{ asset('storage/' . $expense->proof_image) }}" target="_blank" class="btn btn-xs btn-default">
                                                <i class="fas fa-image"></i> Lihat
                                            </a>
                                        @else
                                            <span class="text-muted small">Tidak ada</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada data pengeluaran.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $expenses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
