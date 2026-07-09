@extends('layouts.app')
@section('title', 'Promo Kredit (Redemption)')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h4 class="font-weight-bold"><i class="fas fa-gift text-primary mr-2"></i> Promo Kredit</h4>
        <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#createModal"><i class="fas fa-plus mr-1"></i> Tambah Promo</button>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif

    <div class="row">
        @forelse($redemptions as $r)
            <div class="col-md-4 col-lg-3 mb-3">
                <div class="card h-100 {{ $r->is_active ? '' : 'border-secondary opacity-50' }}">
                    <div class="card-body text-center py-3">
                        @php
                            $icon = match($r->reward_type) {
                                'discount_percent' => 'fas fa-percent text-primary',
                                'paket_usaha' => 'fas fa-box text-success',
                                'free_shipping' => 'fas fa-truck text-info',
                                'product' => 'fas fa-gift text-warning',
                                default => 'fas fa-star'
                            };
                        @endphp
                        <div class="mb-2"><i class="{{ $icon }} fa-2x"></i></div>
                        <h6 class="font-weight-bold mb-1">{{ $r->name }}</h6>
                        <p class="small text-muted mb-1">{{ $r->description ?? '' }}</p>
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <span class="badge badge-warning p-2 mr-1">
                                <i class="fas fa-coins mr-1"></i>{{ number_format($r->credits_required, 0, ',', '.') }}
                            </span>
                            <span class="badge badge-info p-2">
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
                        <div class="small text-muted">
                            @if($r->max_uses_per_customer > 0)
                                Maks {{ $r->max_uses_per_customer }}x per pelanggan
                            @else
                                Tanpa batas
                            @endif
                            &middot;
                            {!! $r->is_active ? '<span class="text-success">Aktif</span>' : '<span class="text-danger">Nonaktif</span>' !!}
                        </div>
                        <button class="btn btn-sm btn-outline-primary mt-2" data-toggle="modal" data-target="#editModal{{ $r->id }}"><i class="fas fa-edit"></i></button>
                    </div>
                </div>
            </div>

            {{-- Edit Modal --}}
            <div class="modal fade" id="editModal{{ $r->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('owner.loyalty.redemption.update', $r->id) }}">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header py-2"><h6 class="font-weight-bold">Edit Promo</h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                            <div class="modal-body">
                                <div class="form-group"><label>Nama</label><input type="text" name="name" class="form-control" value="{{ $r->name }}" required></div>
                                <div class="form-group"><label>Deskripsi</label><textarea name="description" class="form-control" rows="2">{{ $r->description }}</textarea></div>
                                <div class="form-group"><label>Kredit Diperlukan</label><input type="number" name="credits_required" class="form-control" value="{{ $r->credits_required }}" required></div>
                                <div class="form-group">
                                    <label>Tipe Hadiah</label>
                                    <select name="reward_type" class="form-control" required>
                                        <option value="discount_percent" {{ $r->reward_type=='discount_percent'?'selected':'' }}>Diskon (%)</option>
                                        <option value="paket_usaha" {{ $r->reward_type=='paket_usaha'?'selected':'' }}>Paket Usaha</option>
                                        <option value="free_shipping" {{ $r->reward_type=='free_shipping'?'selected':'' }}>Gratis Ongkir</option>
                                        <option value="product" {{ $r->reward_type=='product'?'selected':'' }}>Produk</option>
                                    </select>
                                </div>
                                <div class="form-group"><label>Nilai Hadiah</label><input type="number" step="0.01" name="reward_value" class="form-control" value="{{ $r->reward_value }}" required></div>
                                <div class="form-group"><label>Maks Penggunaan per Pelanggan (0=tanpa batas)</label><input type="number" name="max_uses_per_customer" class="form-control" value="{{ $r->max_uses_per_customer }}" required></div>
                                <div class="form-group">
                                    <label>Aktif</label>
                                    <select name="is_active" class="form-control">
                                        <option value="1" {{ $r->is_active?'selected':'' }}>Ya</option>
                                        <option value="0" {{ !$r->is_active?'selected':'' }}>Tidak</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer py-2">
                                <button type="submit" class="btn btn-primary-apms btn-sm">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">Belum ada promo kredit. <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#createModal">Buat Promo</button></div>
        @endforelse
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('owner.loyalty.redemption.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header py-2"><h6 class="font-weight-bold">Tambah Promo Kredit</h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                <div class="modal-body">
                    <div class="form-group"><label>Nama Promo</label><input type="text" name="name" class="form-control" placeholder="Mis: Diskon 5%" required></div>
                    <div class="form-group"><label>Deskripsi</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                    <div class="form-group"><label>Kredit Diperlukan</label><input type="number" name="credits_required" class="form-control" placeholder="3000" required></div>
                    <div class="form-group">
                        <label>Tipe Hadiah</label>
                        <select name="reward_type" class="form-control" required>
                            <option value="discount_percent">Diskon (%)</option>
                            <option value="paket_usaha">Paket Usaha</option>
                            <option value="free_shipping">Gratis Ongkir</option>
                            <option value="product">Produk</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Nilai Hadiah</label><input type="number" step="0.01" name="reward_value" class="form-control" placeholder="cth: 5 (untuk diskon 5%)" required></div>
                    <div class="form-group"><label>Maks Penggunaan per Pelanggan (0=tanpa batas)</label><input type="number" name="max_uses_per_customer" class="form-control" value="0" required></div>
                </div>
                <div class="modal-footer py-2">
                    <button type="submit" class="btn btn-success btn-sm">Buat Promo</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

<style>
    .opacity-50 { opacity: 0.6; }
</style>
