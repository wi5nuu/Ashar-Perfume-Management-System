@extends('layouts.app')
@section('title', 'Edit Supplier')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="page-header-apms mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h1 class="page-header-title"><i class="fas fa-edit mr-2"></i> Edit Supplier</h1>
                <p class="page-header-subtitle">{{ $supplier->name }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-light btn-sm">
                    <i class="fas fa-eye mr-1"></i> Detail
                </a>
                <a href="{{ route('suppliers.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <strong>Mohon periksa kembali form:</strong>
            <ul class="mb-0 mt-1 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <form action="{{ route('suppliers.update', $supplier) }}" method="POST" id="supplierForm">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- Left: Info Utama --}}
            <div class="col-lg-7">
                <div class="card card-apms mb-4">
                    <div class="card-header">
                        <h5 class="mb-0 font-weight-bold">
                            <i class="fas fa-building mr-2 text-primary-apms"></i> Informasi Supplier
                        </h5>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label class="font-weight-600">Nama Supplier <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $supplier->name) }}"
                                   placeholder="Nama perusahaan atau toko supplier"
                                   required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-600">Narahubung (PIC)</label>
                                    <input type="text"
                                           name="contact_person"
                                           class="form-control @error('contact_person') is-invalid @enderror"
                                           value="{{ old('contact_person', $supplier->contact_person) }}"
                                           placeholder="Nama kontak person">
                                    @error('contact_person')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-600">Nomor Telepon</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        </div>
                                        <input type="text"
                                               name="phone"
                                               class="form-control @error('phone') is-invalid @enderror"
                                               value="{{ old('phone', $supplier->phone) }}"
                                               placeholder="08xx-xxxx-xxxx">
                                    </div>
                                    @error('phone')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-600">Email</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email"
                                       name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $supplier->email) }}"
                                       placeholder="supplier@email.com">
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-600">Alamat</label>
                            <textarea name="address"
                                      class="form-control @error('address') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Alamat lengkap supplier...">{{ old('address', $supplier->address) }}</textarea>
                            @error('address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- Right: Status & Meta --}}
            <div class="col-lg-5">
                <div class="card card-apms mb-4">
                    <div class="card-header">
                        <h5 class="mb-0 font-weight-bold">
                            <i class="fas fa-toggle-on mr-2 text-success"></i> Status Supplier
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-600" for="is_active">
                                    Supplier Aktif
                                </label>
                                <small class="text-muted d-block mt-1">
                                    Supplier nonaktif tidak akan muncul di daftar pilihan Purchase Order.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info Meta --}}
                <div class="card card-apms mb-4">
                    <div class="card-header">
                        <h5 class="mb-0 font-weight-bold">
                            <i class="fas fa-info-circle mr-2 text-info"></i> Informasi Sistem
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr class="border-bottom">
                                    <td class="text-muted pl-3 py-2">Dibuat</td>
                                    <td class="py-2 pr-3">{{ $supplier->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted pl-3 py-2">Diperbarui</td>
                                    <td class="py-2 pr-3">{{ $supplier->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3 py-2">Total PO</td>
                                    <td class="py-2 pr-3 font-weight-bold">{{ $supplier->purchase_orders_count ?? $supplier->purchaseOrders()->count() }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Danger Zone --}}
                @can('manage_suppliers')
                <div class="card border-danger mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 font-weight-bold text-danger">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Danger Zone
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">Hapus supplier secara permanen. Aksi ini tidak bisa dibatalkan. Supplier yang memiliki Purchase Order aktif tidak dapat dihapus.</p>
                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" id="deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-outline-danger btn-sm btn-block"
                                    onclick="confirmDelete()">
                                <i class="fas fa-trash mr-1"></i> Hapus Supplier Ini
                            </button>
                        </form>
                    </div>
                </div>
                @endcan
            </div>
        </div>

        {{-- Action Bar --}}
        <div class="d-flex justify-content-between align-items-center pt-2 pb-4">
            <a href="{{ route('suppliers.index') }}" class="btn btn-light">
                <i class="fas fa-times mr-1"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary-apms px-4">
                <i class="fas fa-save mr-1"></i> Simpan Perubahan
            </button>
        </div>

    </form>
</div>
@endsection

@push('styles')
<style>
.font-weight-600 { font-weight: 600; }
</style>
@endpush

@push('scripts')
<script>
function confirmDelete() {
    Swal.fire({
        title: 'Hapus Supplier?',
        text: 'Tindakan ini permanen dan tidak dapat dibatalkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm').submit();
        }
    });
}
</script>
@endpush
