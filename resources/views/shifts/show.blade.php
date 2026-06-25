@extends('layouts.app')

@section('title', 'Detail Shift & Review Bukti Tutup Kasir')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-5">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">Informasi Shift</h3>
                    <div class="card-tools">
                        <a href="{{ route('shifts.reconciliation', $shift) }}" class="btn btn-sm btn-info"><i class="fas fa-balance-scale"></i> Rekonsiliasi</a>
                        <a href="{{ route('shifts.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 40%">Nama Kasir</th>
                                <td>{{ $shift->user->name }}</td>
                            </tr>
                            <tr>
                                <th>Status Berjalan</th>
                                <td>
                                    <span class="badge badge-{{ $shift->status == 'open' ? 'success' : 'secondary' }}">
                                        {{ strtoupper($shift->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Waktu Mulai</th>
                                <td>{{ $shift->start_time->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Waktu Selesai</th>
                                <td>{{ $shift->end_time ? $shift->end_time->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Modal Awal Laci</th>
                                <td>Rp {{ number_format($shift->initial_cash, 0, ',', '.') }}</td>
                            </tr>
                            @if($shift->status == 'closed')
                            <tr>
                                <th>Uang Masuk (Sistem)</th>
                                <td>Rp {{ number_format($shift->expected_cash, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Uang Fisik (Laporan Kasir)</th>
                                <td>Rp {{ number_format($shift->actual_cash, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Selisih (Laporan Kasir)</th>
                                <td>
                                    @if($shift->discrepancy == 0)
                                        <span class="text-success font-weight-bold">0 (Sesuai)</span>
                                    @else
                                        <span class="text-danger font-weight-bold">
                                            {{ $shift->discrepancy > 0 ? '+' : '' }}{{ number_format($shift->discrepancy, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Catatan Kasir</th>
                                <td>{{ $shift->notes ?: '-' }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Bukti Catatan Penutupan Laci</h3>
                    <div class="card-tools">
                        @if($shift->photo_status == 'pending')
                            <span class="badge badge-warning text-dark"><i class="fas fa-clock"></i> PENDING ACC</span>
                        @elseif($shift->photo_status == 'approved')
                            <span class="badge badge-success"><i class="fas fa-check"></i> ACC oleh {{ $shift->reviewer->name ?? 'Admin' }}</span>
                        @elseif($shift->photo_status == 'rejected')
                            <span class="badge badge-danger"><i class="fas fa-times"></i> DITOLAK oleh {{ $shift->reviewer->name ?? 'Admin' }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body text-center">
                    @if($shift->status == 'open')
                        <div class="alert alert-info py-4">
                            <i class="fas fa-info-circle fa-3x mb-3"></i><br>
                            <h5>Shift Masih Berjalan</h5>
                            <p>Kasir belum melakukan penutupan dan belum mengunggah foto bukti catatan.</p>
                        </div>
                    @elseif($shift->closing_photo_path)
                        <div style="max-height: 500px; overflow: hidden; border: 2px solid #ddd; padding: 5px; border-radius: 5px;" class="mb-3">
                            <a href="{{ Storage::url($shift->closing_photo_path) }}" target="_blank" data-toggle="tooltip" title="Klik untuk memperbesar">
                                <img src="{{ Storage::url($shift->closing_photo_path) }}" class="img-fluid" alt="Bukti Tutup Kasir" style="object-fit: contain; max-height: 480px;">
                            </a>
                        </div>
                        <p class="text-muted"><i class="fas fa-search-plus"></i> Klik gambar di atas untuk melihat detail ukuran penuh pada catatan buku.</p>

                        <!-- Approval Buttons For Admin -->
                        @if(auth()->user()->role === 'admin' && $shift->photo_status == 'pending')
                            <hr>
                            <div class="d-flex justify-content-center mt-3 gap-3" style="gap: 15px;">
                                <form action="{{ route('shifts.review-photo', $shift->id) }}" method="POST" onsubmit="return confirm('Sesuai dengan foto buku? Uang laci dan sistem PAS?');">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button class="btn btn-success btn-lg">
                                        <i class="fas fa-check-circle mr-1"></i> ACC (SESUAI)
                                    </button>
                                </form>

                                <form action="{{ route('shifts.review-photo', $shift->id) }}" method="POST" onsubmit="return confirm('Tolak foto bukti ini karena tidak sesuai/tepat? Kasir harus tahu alasannya manual via WhatsApp/Lisan.');">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button class="btn btn-danger btn-lg">
                                        <i class="fas fa-times-circle mr-1"></i> DECLINE (TOLAK)
                                    </button>
                                </form>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning py-4">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i><br>
                            <h5>Tidak Ada Foto</h5>
                            <p>Shift ini ditutup sebelum fitur upload foto catatan diberlakukan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
