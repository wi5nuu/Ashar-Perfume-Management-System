@extends('layouts.app')
@section('title', 'Manajemen Cabang - APMS')

@section('content')
{{-- All styles loaded globally from resources/sass/_apms.scss --}}

<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-store-alt mr-2"></i>Manajemen Cabang</h1>
                    <p class="mb-0 mt-1" style="color:rgba(255,255,255,.65);font-size:.82rem;">
                        Kelola semua cabang dan pantau kinerja secara terpusat
                    </p>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active">Cabang</li>
                    </ol>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="btn-group view-toggle" role="group">
                        <button class="btn btn-outline-light btn-sm active" id="btn-table-view" title="Tampilan Tabel"><i class="fas fa-list"></i></button>
                        <button class="btn btn-outline-light btn-sm" id="btn-grid-view" title="Tampilan Grid"><i class="fas fa-th-large"></i></button>
                    </div>
                    <a href="{{ route('branches.create') }}" class="btn btn-primary-apms">
                        <i class="fas fa-plus mr-1"></i> Tambah Cabang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid pt-2 pb-5">
    <x-alert />
    </a>
  </div>
</div>

{{-- Period Filter --}}
<div class="card card-apms mb-4">
  <div class="card-body py-2 px-3">
    <div class="d-flex align-items-center flex-wrap" style="gap:6px;">
      <span class="text-muted mr-1" style="font-size:.8rem;"><i class="fas fa-calendar-alt mr-1"></i>Periode:</span>
      @foreach(['today' => 'Hari Ini', 'this_week' => 'Minggu Ini', 'this_month' => 'Bulan Ini', 'this_year' => 'Tahun Ini'] as $key => $label)
        <a href="{{ route('branches.index', ['period' => $key]) }}" class="period-btn {{ $period === $key ? 'active' : '' }}">{{ $label }}</a>
      @endforeach
    </div>
  </div>
</div>

{{-- KPI Cards --}}
<div class="row mb-4">
  <div class="col-6 col-md-3 mb-3">
    <div class="kpi-card card">
      <div class="card-body d-flex align-items-center" style="gap:14px;">
        <div class="kpi-icon" style="background:rgba(255,107,53,.12);"><i class="fas fa-store-alt" style="color:var(--primary)"></i></div>
        <div><div class="kpi-label">Total Cabang</div><div class="kpi-value">{{ $branches->count() }}</div><div class="kpi-sub text-muted">Terdaftar</div></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3 mb-3">
    <div class="kpi-card card">
      <div class="card-body d-flex align-items-center" style="gap:14px;">
        <div class="kpi-icon" style="background:rgba(16,185,129,.12);"><i class="fas fa-check-circle" style="color:#10B981;"></i></div>
        <div><div class="kpi-label">Cabang Aktif</div><div class="kpi-value">{{ $branches->where('is_active', true)->count() }}</div><div class="kpi-sub text-success">Beroperasi</div></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3 mb-3">
    <div class="kpi-card card">
      <div class="card-body d-flex align-items-center" style="gap:14px;">
        <div class="kpi-icon" style="background:rgba(59,130,246,.12);"><i class="fas fa-money-bill-wave" style="color:#3B82F6;"></i></div>
        <div><div class="kpi-label">Total Omzet</div><div class="kpi-value" style="font-size:1.1rem;">Rp {{ number_format($totalRevenue/1000000, 1) }}jt</div><div class="kpi-sub text-info">{{ $periodLabel }}</div></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3 mb-3">
    <div class="kpi-card card">
      <div class="card-body d-flex align-items-center" style="gap:14px;">
        <div class="kpi-icon" style="background:rgba(245,158,11,.12);"><i class="fas fa-chart-line" style="color:#F59E0B;"></i></div>
        <div><div class="kpi-label">Total Pengeluaran</div><div class="kpi-value" style="font-size:1.1rem;">Rp {{ number_format($totalExpenses/1000000, 1) }}jt</div><div class="kpi-sub text-warning">{{ $periodLabel }}</div></div>
      </div>
    </div>
  </div>
</div>

{{-- Map Placeholder --}}
<div class="map-placeholder mb-4">
  <div class="text-center">
    <i class="fas fa-map-marked-alt fa-3x mb-3" style="color:#93c5fd;"></i>
    <h6 style="color:#3B82F6;font-weight:700;">Peta Lokasi Cabang</h6>
    <p class="text-muted mb-0" style="font-size:.8rem;">Integrasi Google Maps / Leaflet.js tersedia<br>untuk visualisasi lokasi seluruh cabang</p>
  </div>
</div>

{{-- Table View --}}
<div id="view-table">
<div class="card card-apms">
  <div class="card-header d-flex align-items-center" style="background:#fff;border-bottom:1px solid #f1f3f6;border-radius:14px 14px 0 0;padding:14px 20px;">
    <i class="fas fa-list mr-2" style="color:var(--primary)"></i>
    <span style="font-weight:700;font-size:.9rem;color:var(--secondary);">Daftar Semua Cabang</span>
    <span class="badge ml-2" style="background:rgba(255,107,53,.12);color:var(--primary);border-radius:20px;padding:3px 10px;font-size:.72rem;">{{ $branches->count() }} cabang</span>
    <div class="ml-auto">
      <input type="text" id="search-branch" class="form-control form-control-sm" style="border-radius:8px;border:1.5px solid #e5e7eb;font-size:.82rem;width:220px;" placeholder="Cari cabang...">
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-modern mb-0" id="branch-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Cabang</th>
            <th>Kota</th>
            <th>Omzet ({{ $periodLabel }})</th>
            <th>Pengeluaran</th>
            <th>Laba</th>
            <th>Status</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($branches as $i => $branch)
          @php $colors = ['#FF6B35','#3B82F6','#10B981','#8B5CF6','#F59E0B','#EF4444']; $color = $colors[$i % count($colors)]; @endphp
          <tr>
            <td class="text-muted" style="font-size:.78rem;">{{ $i + 1 }}</td>
            <td>
              <div class="d-flex align-items-center" style="gap:10px;">
                <div class="branch-avatar" style="background:{{ $color }};">{{ strtoupper(substr($branch->name, 0, 2)) }}</div>
                <div>
                  <div style="font-weight:700;font-size:.87rem;color:var(--secondary);">{{ $branch->name }}</div>
                  <div style="font-size:.72rem;color:#8a94a6;">{{ $branch->address ?? '-' }}</div>
                </div>
              </div>
            </td>
            <td style="font-size:.83rem;">{{ $branch->city ?? '-' }}</td>
            <td>
              <span style="font-weight:700;color:#10B981;">Rp {{ number_format($branch->period_revenue ?? 0, 0, ',', '.') }}</span>
            </td>
            <td>
              <span style="color:#EF4444;">Rp {{ number_format($branch->period_expenses ?? 0, 0, ',', '.') }}</span>
            </td>
            <td>
              @php $profit = ($branch->period_revenue ?? 0) - ($branch->period_expenses ?? 0); @endphp
              <span style="font-weight:700;color:{{ $profit >= 0 ? '#10B981' : '#EF4444' }};">Rp {{ number_format($profit, 0, ',', '.') }}</span>
            </td>
            <td>
              @if($branch->is_active ?? true)
                <span class="badge-active"><i class="fas fa-circle mr-1" style="font-size:.45rem;"></i>Aktif</span>
              @else
                <span class="badge-inactive"><i class="fas fa-circle mr-1" style="font-size:.45rem;"></i>Nonaktif</span>
              @endif
            </td>
            <td class="text-center">
              <a href="{{ route('branches.show', $branch) }}" class="btn btn-sm btn-outline-primary" style="border-radius:7px;" title="Detail"><i class="fas fa-eye"></i></a>
              <a href="{{ route('branches.edit', $branch) }}" class="btn btn-sm btn-outline-warning" style="border-radius:7px;" title="Edit"><i class="fas fa-edit"></i></a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-5">
              <i class="fas fa-store-slash fa-3x mb-3" style="color:#d1d5db;"></i>
              <h6 style="color:#9CA3AF;">Belum ada cabang</h6>
              <a href="{{ route('branches.create') }}" class="btn btn-primary-apms btn-sm mt-2"><i class="fas fa-plus mr-1"></i>Tambah Cabang Pertama</a>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
</div>

{{-- Grid View --}}
<div id="view-grid" style="display:none;">
<div class="row">
  @forelse($branches as $i => $branch)
  @php $colors = ['#FF6B35','#3B82F6','#10B981','#8B5CF6','#F59E0B','#EF4444']; $color = $colors[$i % count($colors)]; $profit = ($branch->period_revenue ?? 0) - ($branch->period_expenses ?? 0); @endphp
  <div class="col-sm-6 col-lg-4 mb-4">
    <div class="branch-grid-card card">
      <div class="card-top">
        <div class="d-flex align-items-center mb-3" style="gap:12px;">
          <div class="branch-avatar" style="background:{{ $color }};width:46px;height:46px;font-size:1rem;">{{ strtoupper(substr($branch->name, 0, 2)) }}</div>
          <div>
            <div style="font-weight:700;font-size:.92rem;color:var(--secondary);">{{ $branch->name }}</div>
            <div style="font-size:.73rem;color:#8a94a6;"><i class="fas fa-map-marker-alt mr-1"></i>{{ $branch->city ?? '-' }}</div>
          </div>
          <div class="ml-auto">
            @if($branch->is_active ?? true)<span class="badge-active">Aktif</span>@else<span class="badge-inactive">Nonaktif</span>@endif
          </div>
        </div>
      </div>
      <div class="metric-row"><span class="text-muted">Omzet</span><span style="font-weight:700;color:#10B981;">Rp {{ number_format($branch->period_revenue ?? 0, 0, ',', '.') }}</span></div>
      <div class="metric-row"><span class="text-muted">Pengeluaran</span><span style="color:#EF4444;">Rp {{ number_format($branch->period_expenses ?? 0, 0, ',', '.') }}</span></div>
      <div class="metric-row"><span class="text-muted">Laba Bersih</span><span style="font-weight:800;color:{{ $profit >= 0 ? '#10B981' : '#EF4444' }};">Rp {{ number_format($profit, 0, ',', '.') }}</span></div>
      <div class="metric-row">
        <a href="{{ route('branches.show', $branch) }}" class="btn btn-outline-primary btn-sm" style="border-radius:7px;font-size:.75rem;"><i class="fas fa-eye mr-1"></i>Detail</a>
        <a href="{{ route('branches.edit', $branch) }}" class="btn btn-outline-warning btn-sm" style="border-radius:7px;font-size:.75rem;"><i class="fas fa-edit mr-1"></i>Edit</a>
      </div>
    </div>
  </div>
  @empty
  <div class="col-12 text-center py-5"><p class="text-muted">Belum ada cabang</p></div>
  @endforelse
</div>
</div>

</div>
@endsection

@push('scripts')
<script>
$(function(){
  // View toggle
  $('#btn-table-view').on('click', function(){
    $('#view-table').show(); $('#view-grid').hide();
    $(this).addClass('active'); $('#btn-grid-view').removeClass('active');
  });
  $('#btn-grid-view').on('click', function(){
    $('#view-grid').show(); $('#view-table').hide();
    $(this).addClass('active'); $('#btn-table-view').removeClass('active');
  });
  // Search filter
  $('#search-branch').on('input', function(){
    var q = $(this).val().toLowerCase();
    $('#branch-table tbody tr').each(function(){
      $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
    });
  });
  @if(session('success'))
  Swal.fire({icon:'success',title:'Berhasil',text:@json(session('success')),confirmButtonColor:'#FF6B35',timer:3000,timerProgressBar:true});
  @endif
});
</script>
@endpush
