@extends('layouts.app')
@section('title', 'Manajemen Gudang - APMS')

@section('content')
{{-- All styles loaded globally from resources/sass/_apms.scss --}}

<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-warehouse mr-2"></i>Manajemen Gudang</h1>
                    <p class="mb-0 mt-1" style="color:rgba(255,255,255,.65);font-size:.82rem;">
                        Kelola lokasi penyimpanan stok dan kapasitas gudang secara terpusat
                    </p>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active">Gudang</li>
                    </ol>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="btn-group view-toggle" role="group">
                        <button class="btn btn-outline-light btn-sm" id="btn-grid-view" title="Grid"><i class="fas fa-th-large"></i></button>
                        <button class="btn btn-outline-light btn-sm active" id="btn-table-view" title="Tabel"><i class="fas fa-list"></i></button>
                    </div>
                    <a href="{{ route('warehouses.create') }}" class="btn btn-primary-apms">
                        <i class="fas fa-plus mr-1"></i> Tambah Gudang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid pt-2 pb-5">
    <x-alert />

{{-- KPI Cards --}}
<div class="row mb-4">
  <div class="col-6 col-md-3 mb-3">
    <div class="kpi-card card"><div class="card-body d-flex align-items-center" style="gap:14px;">
      <div class="kpi-icon" style="background:rgba(255,107,53,.12);"><i class="fas fa-warehouse" style="color:var(--primary)"></i></div>
      <div><div class="kpi-label">Total Gudang</div><div class="kpi-value">{{ $warehouses->count() }}</div><div style="font-size:.72rem;color:#8a94a6;">Lokasi aktif</div></div>
    </div></div>
  </div>
  <div class="col-6 col-md-3 mb-3">
    <div class="kpi-card card"><div class="card-body d-flex align-items-center" style="gap:14px;">
      <div class="kpi-icon" style="background:rgba(59,130,246,.12);"><i class="fas fa-boxes" style="color:#3B82F6;"></i></div>
      <div><div class="kpi-label">Total SKU</div><div class="kpi-value">{{ $warehouses->sum(function($w){ return $w->inventories->count(); }) }}</div><div style="font-size:.72rem;color:#8a94a6;">Item unik</div></div>
    </div></div>
  </div>
  <div class="col-6 col-md-3 mb-3">
    <div class="kpi-card card"><div class="card-body d-flex align-items-center" style="gap:14px;">
      <div class="kpi-icon" style="background:rgba(16,185,129,.12);"><i class="fas fa-check-circle" style="color:#10B981;"></i></div>
      <div><div class="kpi-label">Gudang Aktif</div><div class="kpi-value">{{ $warehouses->where('is_active', true)->count() }}</div><div style="font-size:.72rem;color:#10B981;">Beroperasi</div></div>
    </div></div>
  </div>
  <div class="col-6 col-md-3 mb-3">
    <div class="kpi-card card"><div class="card-body d-flex align-items-center" style="gap:14px;">
      <div class="kpi-icon" style="background:rgba(139,92,246,.12);"><i class="fas fa-layer-group" style="color:#8B5CF6;"></i></div>
      <div><div class="kpi-label">Total Stok</div><div class="kpi-value">{{ number_format($warehouses->sum(function($w){ return $w->inventories->sum('quantity') ?? 0; })) }}</div><div style="font-size:.72rem;color:#8a94a6;">Unit tersimpan</div></div>
    </div></div>
  </div>
</div>

{{-- Grid View --}}
<div id="view-grid" style="display:none;">
<div class="row">
  @forelse($warehouses as $i => $wh)
  @php $colors = ['#FF6B35','#3B82F6','#10B981','#8B5CF6','#F59E0B','#EF4444']; $color = $colors[$i % count($colors)]; $skuCount = $wh->inventories->count(); $cap = $wh->capacity ?? 100; $used = min($skuCount, $cap); $pct = $cap > 0 ? round(($used / $cap) * 100) : 0; $barColor = $pct >= 90 ? '#EF4444' : ($pct >= 70 ? '#F59E0B' : '#10B981'); @endphp
  <div class="col-sm-6 col-lg-4 mb-4">
    <div class="wh-grid-card">
      <div class="wh-top">
        <div class="d-flex align-items-start" style="gap:12px;">
          <div class="wh-avatar" style="background:{{ $color }};"><i class="fas fa-warehouse"></i></div>
          <div class="flex-grow-1">
            <div style="font-weight:700;font-size:.92rem;color:var(--secondary);">{{ $wh->name }}</div>
            <div style="font-size:.73rem;color:#8a94a6;"><code style="background:#f3f4f6;padding:1px 6px;border-radius:4px;font-size:.7rem;">{{ $wh->code }}</code></div>
            <div style="font-size:.73rem;color:#8a94a6;margin-top:2px;"><i class="fas fa-map-marker-alt mr-1"></i>{{ $wh->branch->name ?? 'Pusat' }}</div>
          </div>
          <div>@if($wh->is_active)<span class="badge-active">Aktif</span>@else<span class="badge-inactive">Nonaktif</span>@endif</div>
        </div>
      </div>
      <div class="wh-progress-wrap">
        <div class="d-flex justify-content-between mb-1" style="font-size:.72rem;">
          <span class="text-muted">Kapasitas Terpakai</span>
          <span style="font-weight:700;color:{{ $barColor }};">{{ $pct }}%</span>
        </div>
        <div class="progress-apms"><div class="progress-bar" style="width:{{ $pct }}%;background:{{ $barColor }};"></div></div>
        <div style="font-size:.7rem;color:#8a94a6;margin-top:3px;">{{ $used }} / {{ $cap }} kapasitas</div>
      </div>
      <div class="wh-meta-row"><span class="text-muted">SKU Tersimpan</span><span style="font-weight:700;color:var(--secondary);">{{ $skuCount }} item</span></div>
      <div class="wh-meta-row">
        <a href="{{ route('warehouses.edit', $wh->id) }}" class="btn btn-outline-warning btn-sm" style="border-radius:7px;font-size:.75rem;"><i class="fas fa-edit mr-1"></i>Edit</a>
        <form action="{{ route('warehouses.destroy', $wh->id) }}" method="POST" class="d-inline" onsubmit="return confirmDelete(event, '{{ $wh->name }}')">@csrf @method('DELETE')
          <button type="submit" class="btn btn-outline-danger btn-sm" style="border-radius:7px;font-size:.75rem;"><i class="fas fa-trash mr-1"></i>Hapus</button>
        </form>
      </div>
    </div>
  </div>
  @empty
  <div class="col-12 text-center py-5"><p class="text-muted">Belum ada gudang</p></div>
  @endforelse
</div>
</div>

{{-- Table View --}}
<div id="view-table">
<div class="card card-apms">
  <div class="card-header d-flex align-items-center" style="background:#fff;border-bottom:1px solid #f1f3f6;border-radius:14px 14px 0 0;padding:14px 20px;">
    <i class="fas fa-warehouse mr-2" style="color:var(--primary)"></i>
    <span style="font-weight:700;font-size:.9rem;color:var(--secondary);">Daftar Gudang</span>
    <span class="badge ml-2" style="background:rgba(255,107,53,.12);color:var(--primary);border-radius:20px;padding:3px 10px;font-size:.72rem;">{{ $warehouses->count() }} gudang</span>
    <div class="ml-auto"><input type="text" id="search-wh" class="form-control form-control-sm" style="border-radius:8px;border:1.5px solid #e5e7eb;font-size:.82rem;width:200px;" placeholder="Cari gudang..."></div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-modern mb-0" id="wh-table">
        <thead><tr>
          <th>Kode</th><th>Nama Gudang</th><th>Cabang</th><th>SKU</th><th>Kapasitas</th><th>Status</th><th class="text-center">Aksi</th>
        </tr></thead>
        <tbody>
          @forelse($warehouses as $i => $wh)
          @php $colors = ['#FF6B35','#3B82F6','#10B981','#8B5CF6','#F59E0B','#EF4444']; $color = $colors[$i % count($colors)]; $skuCount = $wh->inventories->count(); $cap = $wh->capacity ?? 100; $used = min($skuCount, $cap); $pct = $cap > 0 ? round(($used/$cap)*100) : 0; $barColor = $pct >= 90 ? '#EF4444' : ($pct >= 70 ? '#F59E0B' : '#10B981'); @endphp
          <tr>
            <td><code style="background:#f3f4f6;padding:2px 8px;border-radius:5px;font-size:.78rem;">{{ $wh->code }}</code></td>
            <td>
              <div class="d-flex align-items-center" style="gap:10px;">
                <div style="width:32px;height:32px;border-radius:8px;background:{{ $color }};display:flex;align-items:center;justify-content:center;color:#fff;font-size:.8rem;flex-shrink:0;"><i class="fas fa-warehouse"></i></div>
                <span style="font-weight:700;font-size:.87rem;color:var(--secondary);">{{ $wh->name }}</span>
              </div>
            </td>
            <td style="font-size:.83rem;">{{ $wh->branch->name ?? '-' }}</td>
            <td><span style="font-weight:700;">{{ $skuCount }}</span> <span class="text-muted">item</span></td>
            <td style="min-width:140px;">
              <div class="d-flex align-items-center" style="gap:8px;">
                <div class="progress-apms flex-grow-1"><div class="progress-bar" style="width:{{ $pct }}%;background:{{ $barColor }};"></div></div>
                <span style="font-size:.75rem;font-weight:700;color:{{ $barColor }};min-width:32px;">{{ $pct }}%</span>
              </div>
            </td>
            <td>@if($wh->is_active)<span class="badge-active">Aktif</span>@else<span class="badge-inactive">Nonaktif</span>@endif</td>
            <td class="text-center">
              <a href="{{ route('warehouses.edit', $wh->id) }}" class="btn btn-sm btn-outline-warning" style="border-radius:7px;" title="Edit"><i class="fas fa-edit"></i></a>
              <form action="{{ route('warehouses.destroy', $wh->id) }}" method="POST" class="d-inline" onsubmit="return confirmDelete(event, '{{ $wh->name }}')">@csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius:7px;" title="Hapus"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center py-5">
            <i class="fas fa-warehouse fa-3x mb-3" style="color:#d1d5db;"></i>
            <h6 style="color:#9CA3AF;">Belum ada gudang</h6>
            <a href="{{ route('warehouses.create') }}" class="btn btn-primary-apms btn-sm mt-2"><i class="fas fa-plus mr-1"></i>Tambah Gudang</a>
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if($warehouses->hasPages())
  <div class="card-footer" style="border-radius:0 0 14px 14px;background:#fafbfc;">{{ $warehouses->links() }}</div>
  @endif
</div>
</div>

</div>
@endsection

@push('scripts')
<script>
function confirmDelete(e, name){
  e.preventDefault();
  var form = e.target;
  Swal.fire({
    title: 'Hapus Gudang?',
    html: 'Gudang <strong>' + name + '</strong> akan dihapus permanen.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#EF4444',
    cancelButtonColor: '#6B7280',
    confirmButtonText: '<i class="fas fa-trash mr-1"></i> Hapus',
    cancelButtonText: 'Batal'
  }).then(function(result){ if(result.isConfirmed) form.submit(); });
  return false;
}
$(function(){
  $('#btn-grid-view').on('click', function(){ $('#view-grid').show(); $('#view-table').hide(); $(this).addClass('active'); $('#btn-table-view').removeClass('active'); });
  $('#btn-table-view').on('click', function(){ $('#view-table').show(); $('#view-grid').hide(); $(this).addClass('active'); $('#btn-grid-view').removeClass('active'); });
  $('#search-wh').on('input', function(){ var q=$(this).val().toLowerCase(); $('#wh-table tbody tr').each(function(){ $(this).toggle($(this).text().toLowerCase().indexOf(q)>-1); }); });
  @if(session('success'))
  Swal.fire({icon:'success',title:'Berhasil',text:@json(session('success')),confirmButtonColor:'#FF6B35',timer:3000,timerProgressBar:true});
  @endif
});
</script>
@endpush
