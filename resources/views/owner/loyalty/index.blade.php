@php use App\Services\WholesaleLoyaltyService; @endphp
@php $rankNames = WholesaleLoyaltyService::RANK_NAMES; @endphp
@extends('layouts.app')
@section('title', 'Loyalty Pelanggan Grosir')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-star mr-2"></i>Loyalty Pelanggan Grosir</h1>
                    <p class="mb-0 mt-1" style="color:rgba(255,255,255,.65);font-size:.82rem;">
                        Program loyalitas &amp; ranking pelanggan wholesale
                    </p>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active">Loyalty</li>
                    </ol>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('owner.loyalty.redemptions') }}" class="btn btn-primary-apms btn-sm">
                        <i class="fas fa-gift mr-1"></i> Promo Kredit
                    </a>
                    <a href="{{ route('owner.loyalty.history') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-history mr-1"></i> Histori Kredit
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <x-alert />

    {{-- Rank Legend --}}
    <div class="card card-apms mb-4">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap align-items-center" style="gap: 10px;">
                <small class="text-muted font-weight-bold">Tingkatan Rank:</small>
                @foreach($rankNames as $rank)
                    @php
                        $badgeStyle = match($rank) {
                            'Regular'  => 'background:#6c757d; color:#fff;',
                            'Bronze'   => 'background:#CD7F32; color:#fff;',
                            'Silver'   => 'background:#9E9E9E; color:#fff;',
                            'Gold'     => 'background:#FFC107; color:#212529;',
                            'Platinum' => 'background:#17a2b8; color:#fff;',
                            default    => 'background:var(--primary); color:#fff;'
                        };
                    @endphp
                    <span class="badge px-3 py-2" style="{{ $badgeStyle }} font-size:0.75rem; border-radius:20px;">
                        {{ $rank }}
                        @if($loop->last)
                            <i class="fas fa-crown ml-1"></i>
                        @endif
                    </span>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card card-apms">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>Pelanggan</th>
                            <th>Total Belanja</th>
                            <th>Rank</th>
                            <th class="text-right">Kredit Tersedia</th>
                            <th class="text-right">Kredit Total</th>
                            <th class="text-right">Poin Emas</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $c)
                            @php
                                $available = $c->total_credits_earned - $c->total_credits_spent;
                                $rankStyle = match($c->loyalty_rank ?? 'Regular') {
                                    'Bronze'   => 'background:#CD7F32; color:#fff;',
                                    'Silver'   => 'background:#9E9E9E; color:#fff;',
                                    'Gold'     => 'background:#FFC107; color:#212529;',
                                    'Platinum' => 'background:#17a2b8; color:#fff;',
                                    default    => 'background:#6c757d; color:#fff;'
                                };
                            @endphp
                            <tr>
                                <td>
                                    <span class="font-weight-bold">{{ $c->name }}</span>
                                    <small class="text-muted d-block">{{ $c->phone ?? '-' }}</small>
                                </td>
                                <td>
                                    {{ $c->lifetime_spend > 0
                                        ? 'Rp ' . number_format($c->lifetime_spend, 0, ',', '.')
                                        : '-' }}
                                </td>
                                <td>
                                    <span class="badge px-2 py-1" style="{{ $rankStyle }} font-size:0.75rem; border-radius:12px;">
                                        {{ $c->loyalty_rank ?? 'Regular' }}
                                        @if(($c->loyalty_rank ?? 'Regular') === ($topRank ?? null))
                                            <i class="fas fa-crown ml-1 text-warning"></i>
                                        @endif
                                    </span>
                                </td>
                                <td class="text-right font-weight-bold text-success">
                                    {{ number_format($available, 0, ',', '.') }}
                                </td>
                                <td class="text-right text-muted">
                                    {{ number_format($c->total_credits_earned, 0, ',', '.') }}
                                </td>
                                <td class="text-right">
                                    {{ $c->gold_points > 0
                                        ? number_format($c->gold_points, 0, ',', '.')
                                        : '-' }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('owner.loyalty.show', $c->id) }}"
                                       class="btn btn-sm btn-outline-primary" title="Detail Loyalty">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted">Belum ada pelanggan grosir</h6>
                                        <small class="text-muted">Data loyalty akan muncul setelah ada transaksi grosir.</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($customers->hasPages())
        <div class="card-footer bg-white d-flex justify-content-center py-3">
            {{ $customers->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('styles')
<style>
.empty-state { padding: 1rem; }
.table td, .table th { vertical-align: middle; }
</style>
@endpush
