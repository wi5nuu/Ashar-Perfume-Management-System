@extends('layouts.app')

@section('title', 'Periode Akuntansi')

@push('styles')
<style>
    :root { --primary:#FF6B35; --primary-dark:#E55A2B; --secondary:#2D3047; }
    .page-header-bar { background:#fff; border-radius:14px; padding:1.2rem 1.6rem; margin-bottom:1.5rem; box-shadow:0 2px 12px rgba(0,0,0,.06); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.8rem; }
    .page-header-bar h4 { font-weight:700; color:var(--secondary); margin:0; font-size:1.15rem; display:flex; align-items:center; gap:.5rem; }
    .page-header-bar h4 i { color:var(--primary); }
    .table-card { background:#fff; border-radius:14px; border:1px solid #eef0f8; box-shadow:0 2px 12px rgba(45,48,71,0.07); overflow:hidden; }
    .table-card .table { margin:0; font-size:.85rem; color:var(--secondary); }
    .table-card .table thead th { background:#f8f9ff; border-bottom:2px solid #eef0f8; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#8892a4; padding:.85rem 1rem; border-top:none; white-space:nowrap; }
    .table-card .table tbody td { padding:.85rem 1rem; vertical-align:middle; border-top:none; border-bottom:1px solid #f5f6fb; }
    .badge-period-open { background:rgba(39,174,96,.1); color:#1a8a4a; padding:.3rem .7rem; border-radius:20px; font-size:.72rem; font-weight:700; }
    .badge-period-closed { background:rgba(127,127,127,.12); color:#666; padding:.3rem .7rem; border-radius:20px; font-size:.72rem; font-weight:700; }
    .btn-close-period { background:var(--primary); color:#fff; border:none; padding:.32rem .8rem; border-radius:7px; font-size:.78rem; font-weight:600; display:inline-flex; align-items:center; gap:.35rem; }
    .btn-close-period:hover { background:var(--primary-dark); color:#fff; }
    .btn-close-period:disabled { background:#e8e8e8; color:#aaa; cursor:not-allowed; }
    .filter-card { background:#fff; border-radius:14px; border:1px solid #eef0f8; box-shadow:0 2px 8px rgba(45,48,71,0.05); padding:1.25rem 1.5rem; margin-bottom:1.25rem; }
    .filter-card .form-control { border-radius:8px; border:1.5px solid #e4e8f0; font-size:.85rem; color:var(--secondary); }
    .filter-card .form-control:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(255,107,53,.1); }
</style>
@endpush

@section('content')
<div class="container-fluid pb-4">

    <div class="page-header-bar">
        <h4><i class="fas fa-calendar-alt"></i> Periode Akuntansi</h4>
        <a href="{{ route('accounting.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;font-size:.84rem">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    {{-- Create Period --}}
    <div class="filter-card">
        <form method="POST" action="{{ route('accounting.periods.store') }}">
            @csrf
            <div class="row align-items-end" style="row-gap:.75rem">
                <div class="col-md-3">
                    <label class="mb-1" style="font-size:.75rem;font-weight:600;color:#8892a4;text-transform:uppercase;letter-spacing:.4px">Nama Periode</label>
                    <input type="text" name="name" class="form-control" placeholder="contoh: Agustus 2026" required>
                </div>
                <div class="col-md-3">
                    <label class="mb-1" style="font-size:.75rem;font-weight:600;color:#8892a4;text-transform:uppercase;letter-spacing:.4px">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="mb-1" style="font-size:.75rem;font-weight:600;color:#8892a4;text-transform:uppercase;letter-spacing:.4px">Tanggal Selesai</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn w-100" style="background:var(--primary);color:#fff;border:none;border-radius:8px;font-weight:600;font-size:.85rem;">
                        <i class="fas fa-plus mr-1"></i>Buat Periode
                    </button>
                </div>
            </div>
            @error('name') <div class="text-danger mt-2" style="font-size:.8rem">{{ $message }}</div> @enderror
        </form>
    </div>

    {{-- Periods Table --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Rentang Tanggal</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Jurnal</th>
                        <th class="text-center">Ditutup Pada</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periods as $p)
                    <tr>
                        <td style="font-weight:600">{{ $p->name }}</td>
                        <td>{{ $p->start_date->format('d M Y') }} &ndash; {{ $p->end_date->format('d M Y') }}</td>
                        <td class="text-center">
                            @if($p->is_closed)
                                <span class="badge-period-closed"><i class="fas fa-lock mr-1"></i>Ditutup</span>
                            @else
                                <span class="badge-period-open"><i class="fas fa-lock-open mr-1"></i>Terbuka</span>
                            @endif
                        </td>
                        <td class="text-center" style="font-weight:600">{{ number_format($p->journals_count) }}</td>
                        <td class="text-center" style="color:#8892a4;font-size:.8rem">
                            {{ $p->closed_at ? $p->closed_at->format('d M Y, H:i') : '&mdash;' }}
                        </td>
                        <td class="text-right">
                            @if(!$p->is_closed)
                            <form method="POST" action="{{ route('accounting.periods.close', $p->id) }}" class="d-inline"
                                  onsubmit="return confirm('Tutup periode {{ $p->name }}? Jurnal baru tidak dapat diposting ke periode ini setelah ditutup.');">
                                @csrf
                                <button type="submit" class="btn-close-period"><i class="fas fa-lock"></i> Tutup Periode</button>
                            </form>
                            @else
                            <span style="color:#ccc;font-size:.8rem">&mdash;</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5" style="color:#8892a4;font-size:.85rem">
                            <i class="fas fa-calendar-times fa-2x d-block mb-2" style="color:#c0c8d8"></i>Belum ada periode. Buat periode pertama Anda.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($errors->has('close'))
    <div class="alert alert-danger mt-3" style="font-size:.85rem;border-radius:10px">
        <i class="fas fa-exclamation-triangle mr-1"></i>{{ $errors->first('close') }}
    </div>
    @endif

    {{ $periods->links() }}
</div>
@endsection
