@extends('layouts.app')
@section('title', 'Catat Penerimaan Barang')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Catat Penerimaan Barang</h3>
                    <div class="card-tools">
                        <a href="{{ route('goods-receipts.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <form action="{{ route('goods-receipts.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Produk <span class="text-danger">*</span></label>
                                    <select name="product_id" class="form-control select2" required>
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} ({{ $product->barcode ?? 'N/A' }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Jumlah <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}" min="1" required>
                                    @error('quantity')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tanggal Masuk <span class="text-danger">*</span></label>
                                    <input type="date" name="received_date" class="form-control" value="{{ old('received_date', date('Y-m-d')) }}" required>
                                    @error('received_date')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Supplier</label>
                                    <input type="text" name="supplier_name" class="form-control" value="{{ old('supplier_name') }}" placeholder="Dari mana barang berasal?">
                                    @error('supplier_name')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pengantar</label>
                                    <input type="text" name="delivery_person" class="form-control" value="{{ old('delivery_person') }}" placeholder="Siapa yang mengantar?">
                                    @error('delivery_person')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Asal Barang</label>
                                    <input type="text" name="origin" class="form-control" value="{{ old('origin') }}" placeholder="Kota/daerah asal">
                                    @error('origin')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Biaya per Unit (Rp) <span class="text-danger">*</span></label>
                                    <input type="number" name="unit_cost" class="form-control" value="{{ old('unit_cost') }}" min="0" step="0.01" required>
                                    @error('unit_cost')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                            @error('notes')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                        @if(auth()->user()->isOwner() || count($branches) > 1)
                        <div class="form-group">
                            <label>Cabang <span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-control select2" required>
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', auth()->user()->branch_id) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('branch_id')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        @else
                        <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                        @endif
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Penerimaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
