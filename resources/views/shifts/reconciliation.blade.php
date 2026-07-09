@extends('layouts.app')
@section('title', 'Rekonsiliasi Kas')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-balance-scale"></i> Rekonsiliasi Kas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('shifts.index') }}">Shifts</a></li>
                    <li class="breadcrumb-item active">Rekonsiliasi</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="row">
        {{-- Summary Panel --}}
        <div class="col-lg-4">
            <div class="card card-apms mb-4">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Ringkasan Shift</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr><th>Kasir</th><td>{{ $shift->user->name ?? '-' }}</td></tr>
                        <tr><th>Mulai</th><td>{{ $shift->start_time ? $shift->start_time->format('d/m/Y H:i') : '-' }}</td></tr>
                        <tr><th>Selesai</th><td>{{ $shift->end_time ? $shift->end_time->format('d/m/Y H:i') : '-' }}</td></tr>
                        <tr><th>Modal Awal</th><td>Rp {{ number_format($shift->initial_cash, 0, ',', '.') }}</td></tr>
                        <tr><th>Penjualan Kas</th><td class="text-success">+ Rp {{ number_format($cashSales, 0, ',', '.') }}</td></tr>
                        <tr><th>Pengeluaran</th><td class="text-danger">- Rp {{ number_format($cashExpenses, 0, ',', '.') }}</td></tr>
                        <tr class="border-top font-weight-bold">
                            <th>Expected</th>
                            <td>Rp {{ number_format($expectedCash, 0, ',', '.') }}</td>
                        </tr>
                        @if($shift->actual_cash !== null)
                        <tr class="font-weight-bold">
                            <th>Actual</th>
                            <td>Rp {{ number_format($shift->actual_cash, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Selisih</th>
                            <td>
                                @if($shift->discrepancy == 0)
                                    <span class="badge badge-success badge-lg">SESUAI</span>
                                @else
                                    <span class="badge badge-danger badge-lg">
                                        {{ $shift->discrepancy > 0 ? '+' : '' }}Rp {{ number_format($shift->discrepancy, 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endif
                    </table>

                    @if($shift->reviewed_at)
                        <div class="alert alert-success mt-2">
                            <i class="fas fa-check-circle mr-1"></i> Direview pada {{ $shift->reviewed_at->format('d/m/Y H:i') }}
                            @if($shift->manager_notes)
                                <br><small>Catatan: {{ $shift->manager_notes }}</small>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Denomination Calculator --}}
        <div class="col-lg-8">
            <form method="POST" action="{{ route('shifts.reconciliation.store', $shift) }}">
                @csrf
                <div class="card card-apms mb-4">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-coins mr-2"></i>Hitung Uang Fisik (Pecahan)</h3>
                    </div>
                    <div class="card-body">
                        <h6 class="font-weight-bold text-muted mb-3">UANG KERTAS</h6>
                        <div class="table-responsive">
                            <table class="table table-sm" id="denomTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Pecahan</th>
                                        <th style="width:100px">Jumlah Lembar</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($denominationValues as $value)
                                    <tr>
                                        <td class="font-weight-bold">Rp {{ number_format($value, 0, ',', '.') }}</td>
                                        <td>
                                            <input type="number" 
                                                name="denominations[{{ $value }}]" 
                                                class="form-control form-control-sm denom-input text-center" 
                                                min="0" value="{{ $shift->denominations[$value] ?? 0 }}" 
                                                data-value="{{ $value }}">
                                        </td>
                                        <td class="text-right denom-subtotal" data-value="{{ $value }}">
                                            Rp {{ number_format($value * ($shift->denominations[$value] ?? 0), 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold" style="font-size:1.1rem">
                                        <td class="text-right" colspan="2">TOTAL UANG KERTAS:</td>
                                        <td class="text-right text-primary" id="totalDenom">Rp 0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <hr>
                        <h6 class="font-weight-bold text-muted mb-3">LOGAM / RECEH</h6>
                        <div class="row">
                            @foreach([500, 200, 100] as $coin)
                            <div class="col-md-4 mb-2">
                                <label class="small font-weight-bold">Rp {{ $coin }}</label>
                                <input type="number" 
                                    name="cash_breakdown[{{ $coin }}]" 
                                    class="form-control form-control-sm coin-input text-center" 
                                    min="0" value="{{ $shift->cash_breakdown[$coin] ?? 0 }}"
                                    data-value="{{ $coin }}">
                                <small class="coin-subtotal text-muted" data-value="{{ $coin }}">
                                    Rp {{ number_format($coin * ($shift->cash_breakdown[$coin] ?? 0), 0, ',', '.') }}
                                </small>
                            </div>
                            @endforeach
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <h5>TOTAL FISIK: <span class="text-primary font-weight-bold" id="grandTotal">Rp 0</span></h5>
                                <h6>Expected: <span id="expectedDisplay">Rp {{ number_format($expectedCash, 0, ',', '.') }}</span></h6>
                                <h6>Selisih: <span id="diffDisplay" class="font-weight-bold">Rp 0</span></h6>
                            </div>
                            <button type="submit" class="btn btn-primary-apms btn-lg">
                                <i class="fas fa-save mr-1"></i> Simpan Rekonsiliasi
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Manager Review --}}
            @can('manage_employees')
            @if(!$shift->reviewed_at)
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-check mr-2"></i>Review Manager</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('shifts.reconciliation.review', $shift) }}">
                        @csrf
                        <div class="form-group">
                            <label>Catatan Manager</label>
                            <textarea name="manager_notes" class="form-control" rows="3" placeholder="Opsional: catatan tambahan..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success"
                            onclick="return confirm('Setujui rekonsiliasi ini?')">
                            <i class="fas fa-check mr-1"></i> Approve Rekonsiliasi
                        </button>
                    </form>
                </div>
            </div>
            @endif
            @endcan
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    const expectedCash = @json($expectedCash);

    function formatRp(num) {
        return 'Rp ' + num.toLocaleString('id-ID');
    }

    function recalc() {
        let totalDenom = 0;
        $('.denom-input').each(function() {
            const val = parseInt($(this).data('value')) || 0;
            const count = parseInt($(this).val()) || 0;
            const sub = val * count;
            totalDenom += sub;
            $(`.denom-subtotal[data-value="${val}"]`).text(formatRp(sub));
        });
        $('#totalDenom').text(formatRp(totalDenom));

        let totalCoin = 0;
        $('.coin-input').each(function() {
            const val = parseInt($(this).data('value')) || 0;
            const count = parseInt($(this).val()) || 0;
            const sub = val * count;
            totalCoin += sub;
            $(`.coin-subtotal[data-value="${val}"]`).text(formatRp(sub));
        });

        const grandTotal = totalDenom + totalCoin;
        $('#grandTotal').text(formatRp(grandTotal));

        const diff = grandTotal - expectedCash;
        const $diff = $('#diffDisplay');
        if (diff === 0) {
            $diff.text('Rp 0 (SESUAI)').removeClass('text-danger').addClass('text-success');
        } else {
            $diff.text((diff > 0 ? '+' : '') + formatRp(diff) + ' (SELISIH)')
                .removeClass('text-success text-danger')
                .addClass(diff > 0 ? 'text-warning' : 'text-danger');
        }
    }

    $(document).on('input', '.denom-input, .coin-input', recalc);
    recalc();
});
</script>
@endpush
