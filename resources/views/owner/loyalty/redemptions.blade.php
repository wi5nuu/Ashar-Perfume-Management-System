@extends('layouts.app')
@section('title', 'Promo Kredit (Redemption)')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="page-header-apms mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h1 class="page-header-title"><i class="fas fa-gift mr-2"></i> Promo Kredit</h1>
                <p class="page-header-subtitle">Kelola program reward & redemption pelanggan grosir</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('owner.loyalty.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Loyalty
                </a>
                <button class="btn btn-primary-apms btn-sm" data-toggle="modal" data-target="#createModal">
                    <i class="fas fa-plus mr-1"></i> Tambah Promo
                </button>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- Promo Cards --}}
    <div class="row">
        @forelse($redemptions as $r)
            @php
                $iconMap = [
                    'discount_percent' => ['icon' => 'fa-percent',  'color' => 'var(--primary)'],
                    'paket_usaha'      => ['icon' => 'fa-box',       'color' => '#28a745'],
                    'free_shipping'    => ['icon' => 'fa-truck',     'color' => '#17a2b8'],
                    'product'          => ['icon' => 'fa-gift',      'color' => '#ffc107'],
                ];
                $ic = $iconMap[$r->reward_type] ?? ['icon' => 'fa-star', 'color' => '#6c757d'];
            @endphp
            <div class="col-sm-6 col-lg-4 col-xl-3 mb-4">
                <div class="card card-apms h-100 {{ $r->is_active ? '' : 'opacity-60' }}">
                    <div class="card-body d-flex flex-column text-center py-4 px-3">

                        {{-- Icon --}}
                        <div class="mb-3">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                                 style="width:60px;height:60px;background:{{ $ic['color'] }}1a;border:2px solid {{ $ic['color'] }}33;">
                                <i class="fas {{ $ic['icon'] }} fa-lg" style="color:{{ $ic['color'] }};"></i>
                            </div>
                        </div>

                        {{-- Name & Desc --}}
                        <h6 class="font-weight-bold mb-1">{{ $r->name }}</h6>
                        @if($r->description)
                            <p class="small text-muted mb-2 flex-grow-1">{{ $r->description }}</p>
                        @else
                            <p class="mb-2 flex-grow-1"></p>
                        @endif

                        {{-- Badges --}}
                        <div class="d-flex justify-content-center flex-wrap mb-3" style="gap:6px;">
                            <span class="badge px-2 py-1" style="background:#FFC107;color:#212529;font-size:0.75rem;border-radius:12px;">
                                <i class="fas fa-coins mr-1"></i>{{ number_format($r->credits_required, 0, ',', '.') }} kredit
                            </span>
                            <span class="badge-modern badge-modern-info">
                                @if($r->reward_type === 'discount_percent')
                                    {{ $r->reward_value }}% Diskon
                                @elseif($r->reward_type === 'paket_usaha')
                                    Paket Usaha
                                @elseif($r->reward_type === 'free_shipping')
                                    Gratis Ongkir
                                @else
                                    {{ $r->reward_value }}
                                @endif
                            </span>
                        </div>

                        {{-- Meta --}}
                        <div class="small text-muted mb-3">
                            @if($r->max_uses_per_customer > 0)
                                <i class="fas fa-user-check mr-1"></i>Maks {{ $r->max_uses_per_customer }}x/pelanggan
                            @else
                                <i class="fas fa-infinity mr-1"></i>Tanpa batas
                            @endif
                            &nbsp;&middot;&nbsp;
                            @if($r->is_active)
                                <span class="text-success font-weight-bold">Aktif</span>
                            @else
                                <span class="text-danger font-weight-bold">Nonaktif</span>
                            @endif
                        </div>

                        {{-- Edit Button --}}
                        <button class="btn btn-sm btn-outline-primary mt-auto"
                                data-toggle="modal" data-target="#editModal{{ $r->id }}">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </button>
                    </div>
                </div>
            </div>

            {{-- Edit Modal --}}
            <div class="modal fade" id="editModal{{ $r->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <form method="POST" action="{{ route('owner.loyalty.redemption.update', $r->id) }}">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title font-weight-bold">
                                    <i class="fas fa-edit mr-2 text-primary-apms"></i>Edit Promo
                                </h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="font-weight-600">Nama</label>
                                    <input type="text" name="name" class="form-control" value="{{ $r->name }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-600">Deskripsi</label>
                                    <textarea name="description" class="form-control" rows="2">{{ $r->description }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-600">Kredit Diperlukan</label>
                                    <input type="number" name="credits_required" class="form-control" value="{{ $r->credits_required }}" required min="1">
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-600">Tipe Hadiah</label>
                                    <select name="reward_type" class="form-control" required>
                                        <option value="discount_percent" {{ $r->reward_type=='discount_percent' ? 'selected' : '' }}>Diskon (%)</option>
                                        <option value="paket_usaha"      {{ $r->reward_type=='paket_usaha'      ? 'selected' : '' }}>Paket Usaha</option>
                                        <option value="free_shipping"    {{ $r->reward_type=='free_shipping'    ? 'selected' : '' }}>Gratis Ongkir</option>
                                        <option value="product"          {{ $r->reward_type=='product'          ? 'selected' : '' }}>Produk</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-600">Nilai Hadiah</label>
                                    <input type="number" step="0.01" name="reward_value" class="form-control" value="{{ $r->reward_value }}" required min="0">
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-600">Maks Penggunaan per Pelanggan <small class="text-muted">(0 = tanpa batas)</small></label>
                                    <input type="number" name="max_uses_per_customer" class="form-control" value="{{ $r->max_uses_per_customer }}" required min="0">
                                </div>
                                <div class="form-group mb-0">
                                    <label class="font-weight-600">Status</label>
                                    <select name="is_active" class="form-control">
                                        <option value="1" {{ $r->is_active ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ !$r->is_active ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary-apms">
                                    <i class="fas fa-save mr-1"></i> Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        @empty
            <div class="col-12">
                <div class="card card-apms">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-gift fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada promo kredit</h5>
                        <p class="text-muted small mb-3">Buat promo pertama untuk meningkatkan loyalitas pelanggan grosir.</p>
                        <button class="btn btn-primary-apms" data-toggle="modal" data-target="#createModal">
                            <i class="fas fa-plus mr-1"></i> Buat Promo Pertama
                        </button>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

</div>

{{-- Create Modal --}}
<div class="modal fade" id="createModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('owner.loyalty.redemption.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-plus mr-2 text-success"></i>Tambah Promo Kredit
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-600">Nama Promo</label>
                        <input type="text" name="name" class="form-control" placeholder="Mis: Diskon 5%" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-600">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Keterangan singkat promo..."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-600">Kredit Diperlukan</label>
                        <input type="number" name="credits_required" class="form-control" placeholder="Contoh: 3000" required min="1">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-600">Tipe Hadiah</label>
                        <select name="reward_type" class="form-control" required>
                            <option value="discount_percent">Diskon (%)</option>
                            <option value="paket_usaha">Paket Usaha</option>
                            <option value="free_shipping">Gratis Ongkir</option>
                            <option value="product">Produk</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-600">Nilai Hadiah</label>
                        <input type="number" step="0.01" name="reward_value" class="form-control"
                               placeholder="Contoh: 5 (untuk diskon 5%)" required min="0">
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-600">Maks Penggunaan per Pelanggan <small class="text-muted">(0 = tanpa batas)</small></label>
                        <input type="number" name="max_uses_per_customer" class="form-control" value="0" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus mr-1"></i> Buat Promo
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
.opacity-60 { opacity: 0.6; }
.font-weight-600 { font-weight: 600; }
</style>
@endpush
