@extends('layouts.app')
@section('title', 'Histori Kredit Grosir')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="font-weight-bold"><i class="fas fa-history text-info mr-2"></i> Histori Kredit Grosir</h4>
        <a href="{{ route('owner.loyalty.index') }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
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
                                <td><strong>{{ $log->customer->name ?? '-' }}</strong></td>
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
                            <tr><td colspan="6" class="text-center py-3 text-muted">Belum ada transaksi kredit.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center">{{ $logs->links() }}</div>
</div>
@endsection
