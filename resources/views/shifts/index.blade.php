@extends('layouts.app')

@section('title', 'Manajemen Shift & Closing Kasir')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Active Shift Section -->
        <div class="col-md-12 mb-4">
            @if($activeShift)
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Shift Aktif (Sedang Berjalan)</h3>
                    <div class="card-tools">
                        <span class="badge badge-success px-3 py-2">OPEN</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="text-muted mb-0">Dibuka Oleh</label>
                            <p class="font-weight-bold mb-0">{{ $activeShift->user->name }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted mb-0">Waktu Mulai</label>
                            <p class="font-weight-bold mb-0">{{ $activeShift->start_time->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted mb-0">Modal Awal (Tunai)</label>
                            <p class="font-weight-bold mb-0 text-primary">Rp {{ number_format($activeShift->initial_cash, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-3 text-right">
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#closeShiftModal">
                                <i class="fas fa-sign-out-alt mr-1"></i> Tutup Shift (Closing)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Tidak Ada Shift Aktif</h3>
                </div>
                <div class="card-body text-center py-4">
                    <i class="fas fa-store-slash fa-4x text-muted mb-3"></i>
                    <h5>Anda belum membuka shift hari ini.</h5>
                    <p class="text-muted">Buka shift untuk mulai mencatat arus kas masuk dan keluar di laci kasir.</p>
                    <button type="button" class="btn btn-primary-apms btn-lg" data-toggle="modal" data-target="#openShiftModal">
                        <i class="fas fa-door-open mr-1"></i> Buka Shift Sekarang
                    </button>
                </div>
            </div>
            @endif
        </div>

        <!-- Shift History -->
        <div class="col-md-12">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Shift & Closing</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr class="text-nowrap">
                                    <th>#</th>
                                    <th>Kasir</th>
                                    <th>Waktu Shift</th>
                                    <th>Modal Awal</th>
                                    <th>Uang Masuk</th>
                                    <th>Uang Fisik</th>
                                    <th>Selisih</th>
                                    <th>Status Kerja</th>
                                    <th>Status Foto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shifts as $shift)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $shift->user->name }}</td>
                                    <td>
                                        <small>
                                            Mulai: {{ $shift->start_time->format('d/m/Y H:i') }}<br>
                                            Selesai: {{ $shift->end_time ? $shift->end_time->format('d/m/Y H:i') : '-' }}
                                        </small>
                                    </td>
                                    <td>Rp {{ number_format($shift->initial_cash, 0, ',', '.') }}</td>
                                    <td>
                                        @if($shift->status == 'closed')
                                            Rp {{ number_format($shift->expected_cash, 0, ',', '.') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($shift->status == 'closed')
                                            Rp {{ number_format($shift->actual_cash, 0, ',', '.') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($shift->status == 'closed')
                                            @if($shift->discrepancy == 0)
                                                <span class="text-success font-weight-bold">0 (Sesuai)</span>
                                            @else
                                                <span class="text-danger font-weight-bold">
                                                    {{ $shift->discrepancy > 0 ? '+' : '' }}{{ number_format($shift->discrepancy, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $shift->status == 'open' ? 'success' : 'secondary' }}">
                                            {{ strtoupper($shift->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($shift->status == 'open')
                                            <span class="text-muted">-</span>
                                        @else
                                            @if($shift->photo_status == 'pending')
                                                <span class="badge badge-warning text-dark"><i class="fas fa-clock mr-1"></i> Menunggu ACC</span>
                                            @elseif($shift->photo_status == 'approved')
                                                <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Di-ACC</span>
                                            @elseif($shift->photo_status == 'rejected')
                                                <span class="badge badge-danger"><i class="fas fa-times mr-1"></i> Ditolak</span>
                                            @endif
                                            <div class="mt-1">
                                                <a href="{{ route('shifts.show', $shift->id) }}" class="btn btn-xs btn-outline-info">Lihat Detail</a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $shifts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Open Shift -->
<div class="modal fade" id="openShiftModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary-apms">
                <h5 class="modal-title">Buka Shift Kasir</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('shifts.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Modal Awal di Laci (Tunai) *</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" name="initial_cash" class="form-control" placeholder="0" required>
                        </div>
                        <small class="text-muted">Masukkan jumlah uang kembalian yang ada di laci saat ini.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-apms">Buka Shift Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Close Shift -->
@if($activeShift)
<div class="modal fade" id="closeShiftModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">Tutup Shift (Closing Kasir)</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('shifts.update', $activeShift->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i> Sistem akan menghitung total uang yang seharusnya ada berdasarkan penjualan tunai dikurangi pengeluaran.
                    </div>
                    <div class="form-group">
                        <label>Total Uang Fisik di Laci Sekarang *</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" name="actual_cash" class="form-control" placeholder="0" required>
                        </div>
                        <small class="text-muted">Hitung semua uang tunai (kertas & koin) yang ada di laci kasir.</small>
                    </div>
                    <div class="form-group">
                        <label>Foto Bukti Buku Catatan Manual (Wajib) *</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="closing_photo" name="closing_photo" accept="image/png, image/jpeg, image/jpg" required>
                            <label class="custom-file-label" for="closing_photo">Pilih Foto...</label>
                        </div>
                        <small class="text-danger mt-1 d-block"><i class="fas fa-exclamation-triangle"></i> Maksimal ukuran: 1 MB (Untuk hemat memori server tahunan).</small>
                    </div>
                    <div class="form-group">
                        <label>Catatan Tambahan (Opsional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Contoh: Ada selisih Rp 500 karena tidak ada kembalian koin"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Simpan & Tutup Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
