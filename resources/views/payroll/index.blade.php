@extends('layouts.app')
@section('title', 'Penggajian')

@section('content')
{{-- All styles loaded globally from resources/sass/_apms.scss --}}

<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-money-check-alt mr-2"></i>Penggajian</h1>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active">Penggajian</li>
                    </ol>
                </div>
                    </ol>
                </nav>
                <h4 class="mb-0 font-weight-bold" style="font-size:1.35rem;">
                    <i class="fas fa-file-invoice-dollar mr-2" style="color:var(--primary);"></i>Manajemen Penggajian
                </h4>
                <p class="mb-0 mt-1" style="color:rgba(255,255,255,.6);font-size:.82rem;">
                    Periode: <strong style="color:#fff;">{{ $month }}</strong>
                </p>
            </div>
            <div class="d-flex flex-wrap" style="gap:.5rem;">
                <form action="{{ route('payroll.generate') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <button type="submit" class="btn btn-primary-apms">
                        <i class="fas fa-sync mr-1"></i> Generate Payroll {{ $month }}
                    </button>
                </form>
                <button class="btn" onclick="window.print()"
                    style="border:1.5px solid rgba(255,255,255,.4);color:#fff;border-radius:8px;font-size:.85rem;font-weight:600;padding:.5rem 1.1rem;">
                    <i class="fas fa-download mr-1"></i> Export
                </button>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    @php
        $totalGaji     = $employees->sum(fn($e) => $e->payrolls->first()->total_salary ?? 0);
        $sudahDibayar  = $employees->filter(fn($e) => $e->payrolls->first()?->status === 'paid')->count();
        $belumDibayar  = $employees->filter(fn($e) => $e->payrolls->first() && $e->payrolls->first()?->status !== 'paid')->count();
        $totalPotongan = $employees->sum(fn($e) => $e->payrollSettings?->deduction ?? 0);
    @endphp
    <div class="row mb-4" style="row-gap:1rem;">
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="kpi-value" style="color:var(--primary);font-size:1.2rem;">
                            Rp {{ number_format($totalGaji, 0, ',', '.') }}
                        </div>
                        <div class="kpi-label">Total Gaji Bulan Ini</div>
                    </div>
                    <div class="kpi-icon" style="background:rgba(255,107,53,.1);color:var(--primary);">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="kpi-value" style="color:#1a7a45;">{{ $sudahDibayar }}</div>
                        <div class="kpi-label">Sudah Dibayar</div>
                    </div>
                    <div class="kpi-icon" style="background:#e8f9f0;color:#1a7a45;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="kpi-value" style="color:#e65100;">{{ $belumDibayar }}</div>
                        <div class="kpi-label">Belum Dibayar</div>
                    </div>
                    <div class="kpi-icon" style="background:#fff3e0;color:#e65100;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="kpi-value" style="color:#c0392b;font-size:1.2rem;">
                            Rp {{ number_format($totalPotongan, 0, ',', '.') }}
                        </div>
                        <div class="kpi-label">Total Potongan</div>
                    </div>
                    <div class="kpi-icon" style="background:#fef0f0;color:#c0392b;">
                        <i class="fas fa-minus-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar d-flex flex-wrap align-items-center justify-content-between" style="gap:.75rem;">
        <form action="{{ route('payroll.index') }}" method="GET" class="d-flex flex-wrap align-items-center" style="gap:.5rem;">
            <select name="month_filter" class="form-control form-control-sm" style="width:120px;">
                @for($m=1; $m<=12; $m++)
                <option value="{{ date('Y-m', mktime(0,0,0,$m,1)) }}"
                    {{ request('month_filter', $month) === date('Y-m', mktime(0,0,0,$m,1)) ? 'selected' : '' }}>
                    {{ date('F Y', mktime(0,0,0,$m,1)) }}
                </option>
                @endfor
            </select>
            <select name="department" class="form-control form-control-sm" style="width:130px;">
                <option value="">Semua Dept.</option>
                <option value="operations" {{ request('department')=='operations'?'selected':'' }}>Operasional</option>
                <option value="sales"      {{ request('department')=='sales'?'selected':'' }}>Penjualan</option>
                <option value="warehouse"  {{ request('department')=='warehouse'?'selected':'' }}>Gudang</option>
                <option value="finance"    {{ request('department')=='finance'?'selected':'' }}>Keuangan</option>
            </select>
            <select name="status" class="form-control form-control-sm" style="width:130px;">
                <option value="">Semua Status</option>
                <option value="paid"    {{ request('status')=='paid'?'selected':'' }}>Dibayar</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                <option value="draft"   {{ request('status')=='draft'?'selected':'' }}>Draft</option>
            </select>
            <button type="submit" class="btn btn-primary-apms btn-sm">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
        </form>
        <div class="d-flex" style="gap:.5rem;">
            <button class="btn btn-sm" id="btnBulkPay" disabled
                style="border:1.5px solid var(--primary);color:var(--primary);border-radius:8px;font-weight:600;font-size:.82rem;opacity:.5;">
                <i class="fas fa-money-bill-wave mr-1"></i> Bayar Terpilih
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="card card-apms">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table-modern w-100">
                    <thead>
                        <tr>
                            <th style="padding-left:1.25rem;width:40px;">
                                <input type="checkbox" id="checkAll" style="width:16px;height:16px;accent-color:var(--primary);">
                            </th>
                            <th>Karyawan</th>
                            <th>Jabatan</th>
                            <th>Gaji Pokok</th>
                            <th>Tunjangan</th>
                            <th>Potongan</th>
                            <th>Gaji Bersih</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $e)
                        @php
                            $p = $e->payrolls->first();
                            $colors = ['#FF6B35','#4e73df','#1cc88a','#6f42c1','#e83e8c'];
                            $color  = $colors[crc32($e->name) % count($colors)];
                            $initials = strtoupper(mb_substr($e->nickname ?? $e->name, 0, 1))
                                      . strtoupper(mb_substr(explode(' ', $e->name)[1] ?? '', 0, 1));
                            $status = $p?->status ?? ($p ? 'generated' : 'draft');
                        @endphp
                        <tr>
                            <td style="padding-left:1.25rem;">
                                <input type="checkbox" class="row-check" data-id="{{ $e->id }}"
                                    style="width:16px;height:16px;accent-color:var(--primary);">
                            </td>
                            <td>
                                <div class="d-flex align-items-center" style="gap:.6rem;">
                                    <div class="employee-avatar" style="background:{{ $color }};">{{ $initials }}</div>
                                    <div>
                                        <div class="font-weight-bold" style="font-size:.88rem;color:#2d3047;">
                                            {{ $e->nickname ?? $e->name }}
                                        </div>
                                        <div style="font-size:.75rem;color:#8a8fa8;">{{ $e->branch->name ?? 'Pusat' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($e->role)
                                <span style="font-size:.82rem;background:#f3e8ff;color:#6f42c1;padding:.25em .65em;border-radius:20px;font-weight:600;">
                                    {{ ucfirst($e->role) }}
                                </span>
                                @else <span style="color:#c0c0c0;">—</span> @endif
                            </td>
                            <td style="font-size:.85rem;">Rp {{ number_format($e->basic_salary ?? 0, 0, ',', '.') }}</td>
                            <td style="font-size:.85rem;color:#0c7abf;">
                                Rp {{ number_format($e->payrollSettings?->allowance ?? 0, 0, ',', '.') }}
                            </td>
                            <td style="font-size:.85rem;color:#c0392b;">
                                Rp {{ number_format($e->payrollSettings?->deduction ?? 0, 0, ',', '.') }}
                            </td>
                            <td>
                                <span class="font-weight-bold" style="color:#1a7a45;font-size:.88rem;">
                                    Rp {{ number_format($p->total_salary ?? 0, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                @if($p)
                                    @if($p->status === 'paid')
                                        <span class="badge-modern badge-paid"><i class="fas fa-check-circle" style="font-size:.65rem;"></i> Dibayar</span>
                                    @elseif($p->status === 'pending')
                                        <span class="badge-modern badge-pending"><i class="fas fa-clock" style="font-size:.65rem;"></i> Pending</span>
                                    @else
                                        <span class="badge-modern" style="background:#e8f9f0;color:#1a7a45;"><i class="fas fa-calculator" style="font-size:.65rem;"></i> Terhitung</span>
                                    @endif
                                @else
                                    <span class="badge-modern badge-draft"><i class="fas fa-file" style="font-size:.65rem;"></i> Draft</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center" style="gap:.35rem;">
                                    <a href="{{ route('employees.show', $e) }}"
                                       style="width:30px;height:30px;border-radius:7px;background:#e8f0fe;color:#3b5bdb;display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;"
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($p && $p->status !== 'paid')
                                    <button type="button"
                                        style="width:30px;height:30px;border-radius:7px;background:#e8f9f0;color:#1a7a45;border:none;display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;cursor:pointer;"
                                        title="Bayar" onclick="confirmPay({{ $e->id }}, '{{ $e->nickname ?? $e->name }}')">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9">
                                <div style="padding:4rem 2rem;text-align:center;">
                                    <div style="font-size:3rem;color:#d1d5e8;margin-bottom:1rem;"><i class="fas fa-file-invoice-dollar"></i></div>
                                    <h5 style="color:#3d4268;">Belum ada data payroll</h5>
                                    <p class="text-muted" style="font-size:.88rem;">Generate payroll terlebih dahulu untuk periode ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($employees->count())
        <div class="card-footer bg-white border-0 px-4 py-3" style="border-top:1px solid #f2f3f8!important;">
            <span style="font-size:.82rem;color:#8a8fa8;">{{ $employees->count() }} karyawan ditemukan</span>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Check all
document.getElementById('checkAll').addEventListener('change', function() {
    document.querySelectorAll('.row-check').forEach(function(c) { c.checked = this.checked; }, this);
    updateBulkBtn();
});
document.querySelectorAll('.row-check').forEach(function(c) {
    c.addEventListener('change', updateBulkBtn);
});
function updateBulkBtn() {
    var checked = document.querySelectorAll('.row-check:checked').length;
    var btn = document.getElementById('btnBulkPay');
    btn.disabled = checked === 0;
    btn.style.opacity = checked > 0 ? '1' : '.5';
    if (checked > 0) btn.innerHTML = '<i class="fas fa-money-bill-wave mr-1"></i> Bayar ' + checked + ' Terpilih';
    else btn.innerHTML = '<i class="fas fa-money-bill-wave mr-1"></i> Bayar Terpilih';
}
function confirmPay(id, name) {
    Swal.fire({
        title: 'Konfirmasi Pembayaran',
        html: 'Tandai gaji <strong>' + name + '</strong> sebagai sudah dibayar?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1a7a45',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Bayar',
        cancelButtonText: 'Batal',
        reverseButtons: true,
    }).then(function(result) {
        if (result.isConfirmed) {
            document.getElementById('pay-form-' + id).submit();
        }
    });
}
</script>
@endpush
