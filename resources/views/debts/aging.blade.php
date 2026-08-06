@extends('layouts.app')
@section('title', 'Hutang & Piutang — Aging Analysis')

@push('styles')
<style>
:root { --primary: #FF6B35; --primary-dark: #E55A2B; --secondary: #2D3047; }
.page-header-apms {
    background: linear-gradient(135deg, var(--secondary) 0%, #3d4166 100%);
    padding: 1.5rem 1.75rem; border-radius: 12px; margin-bottom: 1.5rem; color: #fff;
}
.page-header-apms h1 { font-size: 1.6rem; font-weight: 700; margin: 0; }
.page-header-apms .breadcrumb { background: transparent; margin: 0; padding: 0; }
.page-header-apms .breadcrumb-item a { color: rgba(255,255,255,.7); }
.page-header-apms .breadcrumb-item.active { color: rgba(255,255,255,.9); }
.page-header-apms .breadcrumb-item+.breadcrumb-item::before { color: rgba(255,255,255,.4); }
/* Summary Cards */
.summary-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-bottom: 1.5rem; }
@media(max-width:992px){ .summary-grid { grid-template-columns: repeat(2,1fr); } }
@media(max-width:576px){ .summary-grid { grid-template-columns: 1fr; } }
.summary-card {
    border-radius: 12px; padding: 1.25rem 1.5rem;
    border: none; position: relative; overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,.08);
    transition: transform .2s, box-shadow .2s;
}
.summary-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.12); }
.summary-card .card-bg-icon {
    position: absolute; right: -8px; top: -8px;
    font-size: 5rem; opacity: .07; line-height: 1;
}
.summary-card .sc-label { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; opacity: .8; margin-bottom: .35rem; }
.summary-card .sc-value { font-size: 1.4rem; font-weight: 800; line-height: 1.1; margin-bottom: .2rem; }
.summary-card .sc-sub { font-size: .75rem; opacity: .75; }
.sc-piutang       { background: linear-gradient(135deg, #28a745, #1e7e34); color: #fff; }
.sc-hutang        { background: linear-gradient(135deg, #dc3545, #a71d2a); color: #fff; }
.sc-overdue-pi    { background: linear-gradient(135deg, #fd7e14, #e56c0a); color: #fff; }
.sc-overdue-ht    { background: linear-gradient(135deg, #6f42c1, #59359a); color: #fff; }
/* Aging Cards */
.card-apms { border:none; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.07); margin-bottom:1.5rem; }
.card-apms .card-header { background:#fff; border-bottom:2px solid #f0f0f0; padding:1rem 1.5rem; border-radius:12px 12px 0 0; }
.card-apms .card-body { padding:1.5rem; }
/* Aging bucket headers */
.bucket-header {
    display: flex; align-items: center; gap: .75rem;
    padding: .85rem 1.25rem; border-radius: 10px;
    margin-bottom: 1rem; cursor: pointer;
    user-select: none; transition: opacity .2s;
}
.bucket-header:hover { opacity: .9; }
.bucket-header .bh-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
.bucket-header .bh-title { font-weight: 700; font-size: .95rem; margin: 0; }
.bucket-header .bh-count { font-size: .8rem; opacity: .8; }
.bucket-header .bh-amount { margin-left: auto; font-weight: 800; font-size: 1.05rem; }
.bucket-current  { background: rgba(40,167,69,.08);  color: #155724; border: 1px solid rgba(40,167,69,.2);  }
.bucket-warning  { background: rgba(255,193,7,.1);   color: #856404; border: 1px solid rgba(255,193,7,.25); }
.bucket-danger   { background: rgba(253,126,20,.1);  color: #7d3910; border: 1px solid rgba(253,126,20,.25); }
.bucket-critical { background: rgba(220,53,69,.1);   color: #721c24; border: 1px solid rgba(220,53,69,.2);  }
.bucket-current  .bh-icon { background: rgba(40,167,69,.15);  color: #28a745; }
.bucket-warning  .bh-icon { background: rgba(255,193,7,.2);   color: #e0a800; }
.bucket-danger   .bh-icon { background: rgba(253,126,20,.15); color: #fd7e14; }
.bucket-critical .bh-icon { background: rgba(220,53,69,.15);  color: #dc3545; }
/* Table */
.table-modern thead th { background:#f8f9fa; font-size:.76rem; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#6c757d; border-bottom:2px solid #e9ecef; padding:.75rem 1rem; white-space:nowrap; }
.table-modern tbody td { padding:.75rem 1rem; vertical-align:middle; border-color:#f2f2f2; font-size:.88rem; }
.table-modern tbody tr:hover { background:#fffaf8; }
.table-modern tfoot td { background:#f8f9fa; font-weight:700; font-size:.88rem; border-top:2px solid #e9ecef; padding:.75rem 1rem; }
/* Aging columns color */
.age-current  { color: #1e7e34; font-weight: 600; }
.age-warning  { color: #856404; font-weight: 600; }
.age-danger   { color: #7d3910; font-weight: 600; }
.age-critical { color: #721c24; font-weight: 700; }
/* Progress bar */
.debt-progress { height: 4px; border-radius: 2px; background: #e9ecef; overflow: hidden; margin-top: 3px; }
.debt-progress-bar { height: 100%; border-radius: 2px; transition: width .4s; }
.btn-primary-apms { background:var(--primary); border-color:var(--primary); color:#fff; border-radius:8px; font-weight:600; font-size:.875rem; transition:background .2s,box-shadow .2s; }
.btn-primary-apms:hover { background:var(--primary-dark); border-color:var(--primary-dark); color:#fff; box-shadow:0 4px 12px rgba(255,107,53,.3); }
.btn-action { border-radius:6px; padding:.28rem .6rem; font-size:.78rem; }
</style>

@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-hourglass-half mr-2"></i>Hutang &amp; Piutang</h1>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('debts.index') }}">Hutang</a></li>
                        <li class="breadcrumb-item active">Aging Analysis</li>
                    </ol>
                </div>
                <div class="d-flex gap-2 flex-wrap align-items-center">
                    @if(auth()->user()->isOwner())
                    <form method="GET" class="d-inline">
                        <select name="branch_id" class="form-control form-control-sm" onchange="this.form.submit()" style="border-radius:8px; min-width:150px;">
                            <option value="">Semua Cabang</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ $branchFilter == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </form>
                    @endif
                    <a href="{{ route('debts.aging', array_merge(request()->query(), ['export' => 'xlsx'])) }}" class="btn btn-outline-light btn-sm" style="border-radius:8px;">
                        <i class="fas fa-file-excel mr-1"></i> Export
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;border:none;box-shadow:0 2px 8px rgba(40,167,69,.2);">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    @php
        $bucketMeta = [
            '0-7 hari'   => ['class' => 'bucket-current',  'icon' => 'fa-clock',               'age_class' => 'age-current',  'bar_color' => '#28a745'],
            '8-30 hari'  => ['class' => 'bucket-warning',  'icon' => 'fa-hourglass-half',       'age_class' => 'age-warning',  'bar_color' => '#ffc107'],
            '31-60 hari' => ['class' => 'bucket-danger',   'icon' => 'fa-hourglass',            'age_class' => 'age-danger',   'bar_color' => '#fd7e14'],
            '60+ hari'   => ['class' => 'bucket-critical', 'icon' => 'fa-exclamation-triangle', 'age_class' => 'age-critical', 'bar_color' => '#dc3545'],
        ];
        $grandTotal = collect($grouped)->flatten()->sum('debt_amount');
    @endphp

    {{-- Summary Cards --}}
    <div class="summary-grid">
        <div class="summary-card sc-piutang">
            <div class="card-bg-icon"><i class="fas fa-arrow-down"></i></div>
            <div class="sc-label">Total Piutang</div>
            <div class="sc-value">Rp {{ number_format($summary['total_receivable'] ?? 0, 0, ',', '.') }}</div>
            <div class="sc-sub"><i class="fas fa-users mr-1"></i>{{ $summary['receivable_count'] ?? 0 }} pelanggan</div>
        </div>
        <div class="summary-card sc-hutang">
            <div class="card-bg-icon"><i class="fas fa-arrow-up"></i></div>
            <div class="sc-label">Total Hutang</div>
            <div class="sc-value">Rp {{ number_format($summary['total_payable'] ?? collect($grouped)->flatten()->sum('debt_amount'), 0, ',', '.') }}</div>
            <div class="sc-sub"><i class="fas fa-truck mr-1"></i>{{ $summary['payable_count'] ?? collect($grouped)->flatten()->count() }} transaksi</div>
        </div>
        <div class="summary-card sc-overdue-pi">
            <div class="card-bg-icon"><i class="fas fa-exclamation"></i></div>
            <div class="sc-label">Overdue Piutang</div>
            <div class="sc-value">Rp {{ number_format($summary['overdue_receivable'] ?? 0, 0, ',', '.') }}</div>
            <div class="sc-sub"><i class="fas fa-calendar-times mr-1"></i>Jatuh tempo terlewat</div>
        </div>
        <div class="summary-card sc-overdue-ht">
            <div class="card-bg-icon"><i class="fas fa-bell"></i></div>
            <div class="sc-label">Overdue Hutang</div>
            <div class="sc-value">Rp {{ number_format($summary['overdue_payable'] ?? ($grouped['60+ hari'] ?? collect())->sum('debt_amount'), 0, ',', '.') }}</div>
            <div class="sc-sub"><i class="fas fa-calendar-times mr-1"></i>Perlu segera dibayar</div>
        </div>
    </div>

    {{-- Aging Detail --}}
    <div class="card card-apms">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h3 class="card-title mb-0" style="font-size:1rem; font-weight:700; color:var(--secondary);">
                <i class="fas fa-layer-group mr-2" style="color:var(--primary);"></i>Detail Aging per Bucket
            </h3>
            <small class="text-muted">
                Total: <strong class="text-danger">Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
            </small>
        </div>
        <div class="card-body">

            @foreach($buckets as $bucket)
            @php
                $items = $grouped[$bucket] ?? collect();
                $meta  = $bucketMeta[$bucket] ?? ['class'=>'bucket-current','icon'=>'fa-info','age_class'=>'age-current','bar_color'=>'#6c757d'];
                $bucketTotal = $items->sum('debt_amount');
                $bucketPct   = $grandTotal > 0 ? ($bucketTotal / $grandTotal * 100) : 0;
            @endphp

            <div class="bucket-header {{ $meta['class'] }}" data-toggle="collapse" data-target="#bucket{{ $loop->index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                <div class="bh-icon"><i class="fas {{ $meta['icon'] }}"></i></div>
                <div>
                    <div class="bh-title">{{ $bucket }}</div>
                    <div class="bh-count">{{ $items->count() }} transaksi &bull; {{ number_format($bucketPct, 1) }}% dari total</div>
                </div>
                <div class="bh-amount">Rp {{ number_format($bucketTotal, 0, ',', '.') }}</div>
                <i class="fas fa-chevron-down ml-3" style="font-size:.8rem; opacity:.6;"></i>
            </div>

            <div id="bucket{{ $loop->index }}" class="collapse {{ $loop->first ? 'show' : '' }}">
                @if($items->count() > 0)
                <div class="table-responsive mb-4">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Pelanggan / Pihak</th>
                                <th class="d-none d-md-table-cell">No. Transaksi</th>
                                <th class="d-none d-lg-table-cell">Jatuh Tempo</th>
                                <th class="d-none d-md-table-cell">Hari Terlambat</th>
                                <th class="text-right d-none d-lg-table-cell">Total Tagihan</th>
                                <th class="text-right">Sisa Hutang</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $debt)
                            @php
                                $daysLate = $debt->due_date ? now()->diffInDays(\Carbon\Carbon::parse($debt->due_date), false) * -1 : 0;
                                $daysLate = max(0, $daysLate);
                            @endphp
                            <tr>
                                <td>
                                    <div class="font-weight-600" style="color:var(--secondary);">
                                        {{ $debt->customer->name ?? $debt->supplier->name ?? $debt->debtor_name ?? '-' }}
                                    </div>
                                    <small class="text-muted">{{ $debt->customer->phone ?? $debt->supplier->phone ?? '' }}</small>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span style="font-family:monospace; font-size:.82rem; background:#f8f9fa; padding:.1rem .35rem; border-radius:4px;">
                                        {{ $debt->transaction_number ?? $debt->invoice_number ?? 'TRX-' . str_pad($debt->id, 5, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    @if($debt->due_date)
                                        @php $due = \Carbon\Carbon::parse($debt->due_date); @endphp
                                        <span class="{{ $due->isPast() ? 'text-danger font-weight-600' : 'text-muted' }}" style="font-size:.85rem;">
                                            {{ $due->format('d M Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="d-none d-md-table-cell">
                                    @if($daysLate > 0)
                                        <span class="{{ $meta['age_class'] }}">{{ $daysLate }} hari</span>
                                        <div class="debt-progress" style="width:80px;">
                                            <div class="debt-progress-bar" style="width:{{ min(100, $daysLate/90*100) }}%; background:{{ $meta['bar_color'] }};"></div>
                                        </div>
                                    @else
                                        <span class="age-current">Belum jatuh tempo</span>
                                    @endif
                                </td>
                                <td class="text-right d-none d-lg-table-cell">
                                    Rp {{ number_format($debt->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="text-right font-weight-700 text-danger">
                                    Rp {{ number_format($debt->debt_amount, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('debts.show', $debt) }}" class="btn btn-sm btn-outline-info btn-action" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-warning btn-action btn-reminder"
                                            title="Kirim Reminder"
                                            data-id="{{ $debt->id }}"
                                            data-name="{{ $debt->customer->name ?? $debt->supplier->name ?? '-' }}"
                                            data-amount="{{ number_format($debt->debt_amount, 0, ',', '.') }}">
                                            <i class="fas fa-bell"></i>
                                        </button>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-success btn-action btn-pay"
                                            title="Catat Pembayaran"
                                            data-id="{{ $debt->id }}"
                                            data-name="{{ $debt->customer->name ?? $debt->supplier->name ?? '-' }}"
                                            data-amount="{{ $debt->debt_amount }}">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-right text-muted" style="font-size:.8rem;">
                                    Subtotal {{ $bucket }}:
                                </td>
                                <td class="text-right text-danger">
                                    Rp {{ number_format($items->sum('debt_amount'), 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="text-center py-4 mb-3 text-muted">
                    <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                    Tidak ada hutang di kategori ini.
                </div>
                @endif
            </div>

            @endforeach

        </div>
    </div>

</div>

{{-- Modal Catat Pembayaran --}}
<div class="modal fade" id="payModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:none; border-radius:12px; box-shadow:0 20px 60px rgba(0,0,0,.2);">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0; padding:1.25rem 1.5rem;">
                <h5 class="modal-title font-weight-700" style="color:var(--secondary);"><i class="fas fa-money-bill-wave mr-2 text-success"></i>Catat Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="payForm" method="POST">
                @csrf
                <div class="modal-body" style="padding:1.5rem;">
                    <div class="alert alert-light" style="border-radius:8px; border:1px solid #e9ecef;">
                        <strong id="payName">-</strong><br>
                        <small class="text-muted">Sisa hutang: <strong class="text-danger" id="payDebtDisplay">Rp 0</strong></small>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-600 text-uppercase text-muted">Jumlah Bayar <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text" style="border-radius:8px 0 0 8px;">Rp</span></div>
                            <input type="number" name="amount" class="form-control" id="payAmount" style="border-radius:0 8px 8px 0;" min="1" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-600 text-uppercase text-muted">Tanggal Bayar</label>
                        <input type="date" name="paid_date" class="form-control" style="border-radius:8px;" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-600 text-uppercase text-muted">Catatan</label>
                        <input type="text" name="notes" class="form-control" style="border-radius:8px;" placeholder="Opsional">
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f0f0; padding:1rem 1.5rem;">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal" style="border-radius:8px;">Batal</button>
                    <button type="submit" class="btn btn-primary-apms btn-sm"><i class="fas fa-save mr-1"></i> Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Bucket collapse chevron toggle
    $('[data-toggle="collapse"]').on('click', function() {
        $(this).find('.fa-chevron-down').toggleClass('fa-chevron-up fa-chevron-down');
    });

    // Reminder
    $(document).on('click', '.btn-reminder', function() {
        const name   = $(this).data('name');
        const amount = $(this).data('amount');
        const id     = $(this).data('id');
        Swal.fire({
            title: 'Kirim Reminder Hutang?',
            html: `Kirim pengingat pembayaran kepada <strong>${name}</strong><br>Sisa hutang: <strong class="text-danger">Rp ${amount}</strong>`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#FF6B35',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-bell mr-1"></i> Kirim Reminder',
            cancelButtonText: 'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                $.post('{{ url("debts") }}/' + id + '/reminder', { _token: '{{ csrf_token() }}' })
                    .done(() => Swal.fire({ icon: 'success', title: 'Reminder Terkirim!', text: 'Notifikasi telah dikirim ke ' + name, confirmButtonColor: '#FF6B35' }))
                    .fail(() => Swal.fire({ icon: 'warning', title: 'Gagal', text: 'Tidak dapat mengirim reminder.', confirmButtonColor: '#FF6B35' }));
            }
        });
    });

    // Payment modal
    $(document).on('click', '.btn-pay', function() {
        const name   = $(this).data('name');
        const amount = $(this).data('amount');
        const id     = $(this).data('id');
        $('#payName').text(name);
        $('#payDebtDisplay').text('Rp ' + parseFloat(amount).toLocaleString('id-ID'));
        $('#payAmount').val(amount).attr('max', amount);
        $('#payForm').attr('action', '{{ url("debts") }}/' + id + '/payment');
        $('#payModal').modal('show');
    });

    // Payment form submit with confirm
    $('#payForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const paid   = parseFloat($('#payAmount').val()) || 0;
        const name   = $('#payName').text();
        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            html: `Catat pembayaran <strong>Rp ${paid.toLocaleString('id-ID')}</strong> dari <strong>${name}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Catat!',
            cancelButtonText: 'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                $('#payModal').modal('hide');
                form.submit();
            }
        });
    });
});
</script>
@endpush
