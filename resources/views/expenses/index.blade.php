@extends('layouts.app')
@section('title', 'Biaya Operasional')

@section('content')
{{-- All styles loaded globally from resources/sass/_apms.scss --}}

{{-- Page Header --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-receipt mr-2"></i>Biaya Operasional</h1>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active">Biaya Operasional</li>
                    </ol>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('expenses.create') }}" class="btn btn-primary-apms">
                        <i class="fas fa-plus mr-1"></i> Tambah Biaya
                    </a>
                    <a href="{{ route('expenses.index', array_merge(request()->query(), ['export' => 'xlsx'])) }}" class="btn btn-outline-light btn-sm" style="border-radius:8px;">
                        <i class="fas fa-file-excel mr-1"></i> Export
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <x-alert />

    {{-- KPI Strip --}}
    <div class="kpi-strip">
        <div class="kpi-card">
            <div class="kpi-icon orange"><i class="fas fa-coins"></i></div>
            <div>
                <div class="kpi-label">Bulan Ini</div>
                <div class="kpi-value" style="font-size:1.05rem;">Rp {{ number_format($totalExpenses ?? 0, 0, ',', '.') }}</div>
                <div class="kpi-sub">{{ now()->format('F Y') }}</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon blue"><i class="fas fa-history"></i></div>
            <div>
                <div class="kpi-label">Bulan Lalu</div>
                <div class="kpi-value" style="font-size:1.05rem;">Rp {{ number_format($lastMonthExpenses ?? 0, 0, ',', '.') }}</div>
                <div class="kpi-sub">{{ now()->subMonth()->format('F Y') }}</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon purple"><i class="fas fa-calendar-day"></i></div>
            <div>
                <div class="kpi-label">Rata-rata Harian</div>
                <div class="kpi-value" style="font-size:1.05rem;">Rp {{ number_format($dailyAverage ?? 0, 0, ',', '.') }}</div>
                <div class="kpi-sub">30 hari terakhir</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon teal"><i class="fas fa-tags"></i></div>
            <div>
                <div class="kpi-label">Kategori Terbesar</div>
                <div class="kpi-value" style="font-size:.95rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:120px;">{{ $topCategory ?? '-' }}</div>
                <div class="kpi-sub">Pengeluaran terbanyak</div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        {{-- Donut Chart Card --}}
        <div class="col-lg-3 mb-3">
            <div class="card card-apms h-100">
                <div class="card-header">
                    <h3 class="card-title mb-0" style="font-size:.9rem; font-weight:700; color:var(--secondary);">
                        <i class="fas fa-chart-pie mr-2" style="color:var(--primary);"></i>Per Kategori
                    </h3>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center py-3">
                    <div class="chart-donut-wrap">
                        <canvas id="expenseDonut" width="180" height="180"></canvas>
                    </div>
                    <div class="mt-3 w-100" id="donutLegend" style="font-size:.78rem;"></div>
                </div>
            </div>
        </div>

        {{-- Filter + Table --}}
        <div class="col-lg-9 mb-3">
            <div class="card card-apms">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h3 class="card-title mb-0" style="font-size:1rem; font-weight:700; color:var(--secondary);">
                        <i class="fas fa-list mr-2" style="color:var(--primary);"></i>Daftar Pengeluaran
                    </h3>
                    <small class="text-muted">{{ $expenses->total() ?? 0 }} transaksi</small>
                </div>
                <div class="card-body">

                    {{-- Filter Bar --}}
                    <form method="GET" action="{{ route('expenses.index') }}">
                        <div class="filter-bar">
                            <div class="row align-items-end g-2">
                                <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                                    <label class="small font-weight-600 text-muted mb-1">Dari Tanggal</label>
                                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                                    <label class="small font-weight-600 text-muted mb-1">Sampai Tanggal</label>
                                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                                    <label class="small font-weight-600 text-muted mb-1">Kategori</label>
                                    <select name="category_id" class="form-control form-control-sm">
                                        <option value="">Semua</option>
                                        @foreach($categories ?? [] as $cat)
                                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                                    <label class="small font-weight-600 text-muted mb-1">Cari</label>
                                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Deskripsi..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-1 col-sm-6 mb-2 mb-md-0">
                                    <button type="submit" class="btn btn-primary-apms btn-sm w-100"><i class="fas fa-search"></i></button>
                                </div>
                                <div class="col-md-1 col-sm-6">
                                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-sm w-100"><i class="fas fa-times"></i></a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th class="d-none d-md-table-cell">Deskripsi</th>
                                    <th class="text-right">Jumlah</th>
                                    <th class="d-none d-lg-table-cell text-center">Bukti</th>
                                    <th class="text-center d-none d-sm-table-cell">Status</th>
                                    <th class="d-none d-lg-table-cell">Dibuat Oleh</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                @php
                                    $catColors = ['#FF6B35','#2D3047','#28a745','#007bff','#6f42c1','#20c997','#fd7e14','#e83e8c'];
                                    $colorIdx = ($loop->index % count($catColors));
                                @endphp
                                <tr>
                                    <td>
                                        <div class="font-weight-600" style="font-size:.85rem;">{{ \Carbon\Carbon::parse($expense->date)->format('d M Y') }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($expense->date)->format('l') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge-modern" style="background:{{ $catColors[$colorIdx] }}18; color:{{ $catColors[$colorIdx] }}; border:1px solid {{ $catColors[$colorIdx] }}30;">
                                            <span class="category-dot" style="background:{{ $catColors[$colorIdx] }};"></span>{{ $expense->category->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <span style="max-width:200px; display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                            {{ $expense->description ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-right font-weight-700" style="color:#dc3545;">
                                        Rp {{ number_format($expense->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center d-none d-lg-table-cell">
                                        @if($expense->proof_image)
                                            <a href="{{ asset('storage/' . $expense->proof_image) }}" target="_blank" class="btn btn-sm btn-outline-info btn-action" title="Lihat Bukti">
                                                <i class="fas fa-image"></i>
                                            </a>
                                        @else
                                            <span class="text-muted"><i class="fas fa-minus"></i></span>
                                        @endif
                                    </td>
                                    <td class="text-center d-none d-sm-table-cell">
                                        @php $status = $expense->status ?? 'approved'; @endphp
                                        @if($status === 'approved')
                                            <span class="badge badge-modern badge-approved">Disetujui</span>
                                        @elseif($status === 'pending')
                                            <span class="badge badge-modern badge-pending">Pending</span>
                                        @else
                                            <span class="badge badge-modern badge-rejected">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <small class="text-muted">{{ $expense->user->name ?? '-' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-outline-info btn-action" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('expenses.edit')
                                            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-outline-warning btn-action" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('expenses.delete')
                                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="d-inline form-delete">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-action" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="text-center py-5">
                                            <i class="fas fa-receipt fa-3x text-muted mb-3 d-block"></i>
                                            <p class="text-muted mb-1">Belum ada data pengeluaran.</p>
                                            <a href="{{ route('expenses.create') }}" class="btn btn-primary-apms btn-sm mt-2">
                                                <i class="fas fa-plus mr-1"></i> Tambah Pengeluaran
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $expenses->appends(request()->query())->links() }}</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
$(document).ready(function() {
    // Confirm delete
    $(document).on('submit', '.form-delete', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Hapus Pengeluaran?',
            text: 'Data tidak dapat dikembalikan setelah dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FF6B35',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then(r => { if (r.isConfirmed) form.submit(); });
    });

    // Donut chart
    @php
        $chartLabels = [];
        $chartData = [];
        $chartColors = ['#FF6B35','#2D3047','#28a745','#007bff','#6f42c1','#20c997','#fd7e14','#e83e8c'];
        if(isset($categoryStats)) {
            foreach($categoryStats as $i => $cs) {
                $chartLabels[] = $cs->name ?? 'Lainnya';
                $chartData[] = $cs->total ?? 0;
            }
        }
    @endphp
    const labels = @json($chartLabels);
    const data   = @json($chartData);
    const colors = ['#FF6B35','#2D3047','#28a745','#007bff','#6f42c1','#20c997','#fd7e14','#e83e8c'];

    if (labels.length > 0) {
        const ctx = document.getElementById('expenseDonut').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{ data: data, backgroundColor: colors.slice(0, labels.length), borderWidth: 2, borderColor: '#fff' }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '65%',
                plugins: { legend: { display: false }, tooltip: {
                    callbacks: { label: (c) => ' Rp ' + c.raw.toLocaleString('id-ID') }
                }}
            }
        });
        // Legend
        const total = data.reduce((a,b)=>a+b,0);
        let legendHtml = '';
        labels.forEach((l, i) => {
            const pct = total > 0 ? Math.round(data[i]/total*100) : 0;
            legendHtml += `<div class="d-flex align-items-center justify-content-between mb-1">
                <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${colors[i]};margin-right:5px;"></span>${l}</span>
                <span class="font-weight-600">${pct}%</span></div>`;
        });
        document.getElementById('donutLegend').innerHTML = legendHtml;
    } else {
        document.getElementById('expenseDonut').parentElement.innerHTML = '<p class="text-muted text-center small py-3">Belum ada data kategori.</p>';
    }
});
</script>
@endpush
