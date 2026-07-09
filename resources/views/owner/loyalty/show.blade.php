@php use App\Services\WholesaleLoyaltyService; @endphp
@extends('layouts.app')
@section('title', 'Detail Loyalty - ' . $customer->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div>
            <a href="{{ route('owner.loyalty.index') }}" class="btn btn-sm btn-outline-secondary mr-2"><i class="fas fa-arrow-left"></i></a>
            <h4 class="font-weight-bold d-inline"><i class="fas fa-star text-warning mr-2"></i> {{ $customer->name }}</h4>
        </div>
    </div>

    {{-- Rank Info Card --}}
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body py-3">
                    <div class="text-muted small">Rank Saat Ini</div>
                    <h3 class="font-weight-bold mb-0">
                        @php
                            $badge = match($rankInfo['current_rank']) {
                                'Regular' => 'secondary',
                                'Bronze' => 'secondary',
                                'Silver' => 'secondary',
                                'Gold' => 'warning',
                                'Platinum' => 'info',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge badge-{{ $badge }}" style="font-size:1rem;">
                            {{ $rankInfo['current_rank'] }}
                            @if($rankInfo['is_top_rank'])
                                <i class="fas fa-crown text-warning ml-1"></i>
                            @endif
                        </span>
                    </h3>
                    <small class="text-muted">{{ $rankInfo['benefits'] }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body py-3">
                    <div class="text-muted small">Kredit Tersedia</div>
                    <h3 class="font-weight-bold text-success mb-0">{{ number_format($rankInfo['available_credits'], 0, ',', '.') }}</h3>
                    <small class="text-muted">dari {{ number_format($customer->total_credits_earned, 0, ',', '.') }} total</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body py-3">
                    <div class="text-muted small">Poin Emas</div>
                    <h3 class="font-weight-bold text-warning mb-0">{{ number_format($rankInfo['gold_points'], 0, ',', '.') }}</h3>
                    <small class="text-muted">min Rp1M per transaksi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body py-3">
                    <div class="text-muted small">Total Belanja</div>
                    <h4 class="font-weight-bold mb-0">Rp {{ number_format($customer->lifetime_spend, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Next Rank Progress --}}
    @if($rankInfo['next_rank'])
    <div class="card mb-3">
        <div class="card-body py-2 px-3">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Rank selanjutnya: <strong>{{ $rankInfo['next_rank']['name'] }}</strong>
                    (min Rp {{ number_format($rankInfo['next_rank']['min_spend'], 0, ',', '.') }})
                </small>
                <small class="font-weight-bold">{{ number_format($rankInfo['progress'], 1) }}%</small>
            </div>
            <div class="progress mt-1" style="height:8px;">
                <div class="progress-bar bg-warning" style="width:{{ $rankInfo['progress'] }}%"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- Manual Credit Adjustment --}}
    <div class="card mb-3">
        <div class="card-header py-2"><strong><i class="fas fa-edit mr-1"></i> Sesuaikan Kredit</strong></div>
        <div class="card-body py-2">
            <form method="POST" action="{{ route('owner.loyalty.adjust', $customer->id) }}" class="form-inline">
                @csrf
                <div class="form-group mr-2 mb-1">
                    <input type="number" name="credits" class="form-control form-control-sm" placeholder="+/- kredit" required style="width:140px;">
                </div>
                <div class="form-group mr-2 mb-1">
                    <input type="text" name="reason" class="form-control form-control-sm" placeholder="Alasan" required style="width:200px;">
                </div>
                <button type="submit" class="btn btn-sm btn-warning mb-1">Simpan</button>
            </form>
            <small class="text-muted">Gunakan angka positif untuk menambah, negatif untuk mengurangi.</small>
        </div>
    </div>

    {{-- Credit Logs --}}
    <div class="card">
        <div class="card-header py-2"><strong><i class="fas fa-history mr-1"></i> Riwayat Kredit</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Kredit</th>
                            <th>Poin Emas</th>
                            <th>Tipe</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            @php
                                $typeLabel = match($log->type) {
                                    'earn' => ['badge-success', 'Earn'],
                                    'spend' => ['badge-danger', 'Spend'],
                                    'gold_earn' => ['badge-warning', 'Gold'],
                                    'rank_up' => ['badge-info', 'Rank Up'],
                                    'admin' => ['badge-secondary', 'Admin'],
                                    default => ['badge-secondary', $log->type]
                                };
                            @endphp
                            <tr>
                                <td style="white-space:nowrap;">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td class="font-weight-bold {{ $log->credits >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $log->credits >= 0 ? '+' : '' }}{{ number_format($log->credits, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if($log->gold_points > 0)
                                        <span class="text-warning">+{{ number_format($log->gold_points, 0, ',', '.') }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td><span class="badge {{ $typeLabel[0] }}">{{ $typeLabel[1] }}</span></td>
                                <td>{{ $log->description ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-3 text-muted">Belum ada riwayat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center">{{ $logs->links() }}</div>
</div>
@endsection
