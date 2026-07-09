@extends('layouts.app')
@section('title', 'Buat Retur Penjualan')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0"><i class="fas fa-undo-alt"></i> Buat Retur Penjualan</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('returns.index') }}">Retur</a></li>
                    <li class="breadcrumb-item active">Buat Baru</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif

    {{-- Transaction Info --}}
    <div class="card card-apms mb-3">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-receipt mr-2"></i>Transaksi Asal</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><strong>Invoice:</strong> {{ $transaction->invoice_number }}</div>
                <div class="col-md-4"><strong>Customer:</strong> {{ $transaction->customer->name ?? 'Umum' }}</div>
                <div class="col-md-4"><strong>Total:</strong> Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('returns.store') }}">
        @csrf
        <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">

        <div class="card card-apms">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-box-open mr-2"></i>Pilih Item yang Diretur</h3>
            </div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label>Alasan Retur <span class="text-danger">*</span></label>
                    <textarea name="reason" class="form-control" rows="2" required placeholder="Jelaskan alasan retur...">{{ old('reason') }}</textarea>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm" id="returnItems">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:10%">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>Produk</th>
                                <th class="text-center">Qty Beli</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center" style="width:120px">Qty Retur</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaction->details as $detail)
                            <tr class="return-row">
                                <td><input type="checkbox" class="row-check" data-index="{{ $loop->index }}"></td>
                                <td>{{ $detail->product->name ?? $detail->product_name ?? '-' }}</td>
                                <td class="text-center">{{ $detail->quantity }}</td>
                                <td class="text-center">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <input type="number" name="items[{{ $loop->index }}][quantity]" 
                                        class="form-control form-control-sm qty-return text-center" 
                                        min="0" max="{{ $detail->quantity }}" value="0"
                                        data-price="{{ $detail->price }}" data-max="{{ $detail->quantity }}" disabled>
                                    <input type="hidden" name="items[{{ $loop->index }}][detail_id]" value="{{ $detail->id }}">
                                </td>
                                <td class="text-right row-subtotal">Rp 0</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold" style="font-size:1.1rem">
                                <td colspan="5" class="text-right">TOTAL REFUND:</td>
                                <td class="text-right text-danger" id="grandTotal">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer text-right">
                <a href="{{ route('returns.index') }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-primary-apms">
                    <i class="fas fa-save mr-1"></i> Buat Retur
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    function formatRp(n) { return 'Rp ' + n.toLocaleString('id-ID'); }

    function recalc() {
        let total = 0;
        let hasItem = false;
        $('.return-row').each(function() {
            const $input = $(this).find('.qty-return');
            if ($input.prop('disabled')) return;
            const qty = parseInt($input.val()) || 0;
            const price = parseFloat($input.data('price')) || 0;
            const sub = qty * price;
            $(this).find('.row-subtotal').text(formatRp(sub));
            total += sub;
            if (qty > 0) hasItem = true;
        });
        $('#grandTotal').text(formatRp(total));
    }

    $('#selectAll').on('change', function() {
        const checked = $(this).is(':checked');
        $('.row-check').each(function() {
            $(this).prop('checked', checked);
            const $input = $(this).closest('tr').find('.qty-return');
            $input.prop('disabled', !checked);
            if (!checked) { $input.val(0); }
            else { $input.val($input.data('max')); }
        });
        recalc();
    });

    $(document).on('change', '.row-check', function() {
        const $input = $(this).closest('tr').find('.qty-return');
        $input.prop('disabled', !$(this).is(':checked'));
        if (!$(this).is(':checked')) { $input.val(0); }
        else { $input.val($input.data('max')); }
        recalc();
    });

    $(document).on('input', '.qty-return', function() {
        const max = parseInt($(this).data('max')) || 0;
        if (parseInt($(this).val()) > max) $(this).val(max);
        recalc();
    });
});
</script>
@endpush
