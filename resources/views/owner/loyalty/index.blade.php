@php use App\Services\WholesaleLoyaltyService; @endphp
@php $rankNames = WholesaleLoyaltyService::RANK_NAMES; @endphp
@extends('layouts.app')
@section('title', 'Loyalty Pelanggan Grosir')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h4 class="font-weight-bold"><i class="fas fa-star text-warning mr-2"></i> Loyalty Pelanggan Grosir</h4>
        <div>
            <a href="{{ route('owner.loyalty.redemptions') }}" class="btn btn-sm btn-primary-apms"><i class="fas fa-gift mr-1"></i> Promo Kredit</a>
            <a href="{{ route('owner.loyalty.history') }}" class="btn btn-sm btn-info"><i class="fas fa-history mr-1"></i> Histori Kredit</a>
        </div>
    </div>

    {{-- Rank Legend --}}
    <div class="card card-body py-2 px-3 mb-3">
        <div class="d-flex flex-wrap gap-3 align-items-center" style="gap: 12px;">
            <small class="text-muted font-weight-bold mr-2">Rank:</small>
            @foreach($rankNames as $rank)
                @php
                    $badge = match($rank) {
                        'Regular' => 'secondary',
                        'Bronze' => 'secondary',
                        'Silver' => 'secondary',
                        'Gold' => 'warning',
                        'Platinum' => 'info',
                        default => 'primary'
                    };
                @endphp
                <span class="badge badge-{{ $badge }} p-2" style="font-size:0.7rem;">
                    {{ $rank }}
                    @if($loop->last)
                        <i class="fas fa-infinity ml-1"></i>
                    @endif
                </span>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Pelanggan</th>
                            <th>Total Belanja</th>
                            <th>Rank</th>
                            <th>Kredit <small>(tersedia)</small></th>
                            <th>Kredit <small>(total)</small></th>
                            <th>Poin Emas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $c)
                            @php
                                $available = $c->total_credits_earned - $c->total_credits_spent;
                                $rankBadge = match($c->loyalty_rank) {
                                    'Bronze' => 'badge-secondary',
                                    'Silver' => 'badge-secondary',
                                    'Gold' => 'badge-warning',
                                    'Platinum' => 'badge-info',
                                    default => 'badge-secondary'
                                };
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $c->name }}</strong>
                                    <br><small class="text-muted">{{ $c->phone ?? '-' }}</small>
                                </td>
                                <td>{{ $c->lifetime_spend > 0 ? 'Rp ' . number_format($c->lifetime_spend, 0, ',', '.') : '-' }}</td>
                                <td>
                                    <span class="badge {{ $rankBadge }} p-2">
                                        {{ $c->loyalty_rank ?? 'Regular' }}
                                        @if(($c->loyalty_rank ?? 'Regular') === $topRank)
                                            <i class="fas fa-crown text-warning ml-1"></i>
                                        @endif
                                    </span>
                                </td>
                                <td class="font-weight-bold text-success">{{ number_format($available, 0, ',', '.') }}</td>
                                <td>{{ number_format($c->total_credits_earned, 0, ',', '.') }}</td>
                                <td>{{ $c->gold_points > 0 ? number_format($c->gold_points, 0, ',', '.') : '-' }}</td>
                                <td>
                                    <a href="{{ route('owner.loyalty.show', $c->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada pelanggan grosir.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center">{{ $customers->links() }}</div>
</div>
@endsection

<style>
    .badge-bronze { background-color: #CD7F32; color: white; }
    .gap-3 { gap: 12px; }
    .table td, .table th { vertical-align: middle; }
</style>
