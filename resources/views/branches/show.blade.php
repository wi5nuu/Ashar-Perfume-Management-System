@extends('layouts.app')
@section('title', 'Laporan Cabang: ' . $branch->name . ' - APMS')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="font-weight-bold mb-0">
                <i class="fas fa-store-alt mr-2 text-info"></i>{{ $branch->name }}
                @if($branch->code)<small class="badge badge-secondary ml-2">{{ $branch->code }}</small>@endif
                @if(!$branch->is_active)<span class="badge badge-danger ml-2">NONAKTIF</span>@endif
            </h3>
            <small class="text-muted">
                @if($branch->address)<i class="fas fa-map-marker-alt mr-1"></i>{{ $branch->address }}@if($branch->city), {{ $branch->city }}@endif &nbsp;|&nbsp;@endif
                @if($branch->phone)<i class="fas fa-phone mr-1"></i>{{ $branch->phone }} &nbsp;|&nbsp;@endif
                @if($branch->manager_name)<i class="fas fa-user-tie mr-1"></i>PIC: {{ $branch->manager_name }}@endif
            </small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('branches.edit', $branch) }}" class="btn btn-outline-warning btn-sm mr-2">
                <i class="fas fa-edit mr-1"></i>Edit
            </a>
            <a href="{{ route('branches.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i>Kembali
            </a>
        </div>
    </div>

    {{-- Period Filter --}}
    <div class="card card-apms mb-3">
        <div class="card-body p-2">
            <div class="d-flex align-items-center flex-wrap">
                <span class="font-weight-bold text-muted mr-2"><i class="fas fa-calendar-alt mr-1"></i>Periode:</span>
                @foreach(['today' => 'Hari Ini', 'this_week' => 'Minggu Ini', 'this_month' => 'Bulan Ini', 'this_year' => 'Tahun Ini'] as $key => $label)
                    <a href="{{ route('branches.show', [$branch, 'period' => $key]) }}"
                       class="btn btn-sm {{ $period === $key ? 'btn-primary-apms' : 'btn-outline-secondary' }} mr-1">{{ $label }}</a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row mb-3">
        <div class="col-md-4 mb-2">
            <div class="card card-apms text-center">
                <div class="card-body py-3">
                    <div class="text-muted small"><i class="fas fa-chart-line mr-1 text-primary"></i>Omzet ({{ $periodLabel }})</div>
                    <h4 class="font-weight-bold text-primary mb-0">Rp {{ number_format($revenue, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <div class="card card-apms text-center">
                <div class="card-body py-3">
                    <div class="text-muted small"><i class="fas fa-receipt mr-1 text-danger"></i>Pengeluaran</div>
                    <h4 class="font-weight-bold text-danger mb-0">Rp {{ number_format($expenses, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            @php $profit = $revenue - $expenses; @endphp
            <div class="card card-apms text-center">
                <div class="card-body py-3">
                    <div class="text-muted small"><i class="fas fa-hand-holding-usd mr-1 text-success"></i>Profit Bersih</div>
                    <h4 class="font-weight-bold {{ $profit >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                        Rp {{ number_format($profit, 0, ',', '.') }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Monthly Chart --}}
        <div class="col-md-8 mb-3">
            <div class="card card-apms">
                <div class="card-header py-2">
                    <h5 class="card-title mb-0"><i class="fas fa-chart-bar mr-2"></i>Grafik Omzet Bulanan ({{ now()->year }})</h5>
                </div>
                <div class="card-body">
                    <canvas id="branchChart" height="100"></canvas>
                </div>
            </div>
        </div>

        {{-- Staff List --}}
        <div class="col-md-4 mb-3">
            <div class="card card-apms h-100">
                <div class="card-header py-2">
                    <h5 class="card-title mb-0"><i class="fas fa-users mr-2"></i>Staf Cabang ({{ $staff->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($staff as $s)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                            <div>
                                <i class="fas fa-user-circle mr-2 text-muted"></i>
                                <span class="font-weight-bold">{{ $s->name }}</span>
                            </div>
                            <span class="badge badge-info">{{ ucfirst($s->role) }}</span>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted py-3">Belum ada staf di cabang ini.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="card card-apms">
        <div class="card-header py-2">
            <h5 class="card-title mb-0"><i class="fas fa-history mr-2"></i>Transaksi Terbaru ({{ $periodLabel }})</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" style="font-size:0.85rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="pl-3">Invoice</th>
                            <th>Pelanggan</th>
                            <th>Metode</th>
                            <th class="text-right">Total</th>
                            <th>Status</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $tx)
                        <tr>
                            <td class="pl-3"><a href="{{ route('transactions.show', $tx) }}"><small>{{ $tx->invoice_number }}</small></a></td>
                            <td>{{ $tx->customer->name ?? 'Umum' }}</td>
                            <td><span class="badge badge-secondary">
                                {{ strtoupper($tx->payment_method) }}
                                @if($tx->payment_method === 'ewallet' && $tx->ewallet_type)
                                    <br><small>({{ strtoupper($tx->ewallet_type) }})</small>
                                @elseif($tx->payment_method === 'transfer' && $tx->transfer_type)
                                    <br><small>({{ strtoupper($tx->transfer_type) }})</small>
                                @endif
                            </span></td>
                            <td class="text-right font-weight-bold">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge badge-{{ $tx->payment_status === 'paid' ? 'success' : ($tx->payment_status === 'partial' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($tx->payment_status) }}
                                </span>
                            </td>
                            <td><small>{{ $tx->created_at->format('d/m H:i') }}</small></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">Tidak ada transaksi pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3/dist/chart.min.js"></script>
<script>
const chartData = @json($chartData);
const ctx = document.getElementById('branchChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartData.map(d => d.month),
        datasets: [{
            label: 'Omzet (Rp)',
            data: chartData.map(d => d.sales),
            backgroundColor: 'rgba(255, 107, 53, 0.7)',
            borderColor: '#FF6B35',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(1) + ' Jt' } }
        }
    }
});
</script>
@endpush
