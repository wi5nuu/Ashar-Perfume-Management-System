@extends('layouts.app')
@section('title', 'Integritas Data - APMS')

@section('content')
<div class="container-fluid pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-dark"><i class="fas fa-check-double mr-2 text-success"></i>Pemeriksaan Integritas Data</h4>
            <p class="text-muted">Deteksi anomali dan manipulasi data dalam sistem</p>
        </div>
        <a href="{{ route('admin.security.overview') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-5">
                    <div style="font-size: 80px; color: {{ $score >= 80 ? '#28a745' : ($score >= 50 ? '#ffc107' : '#dc3545') }};">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h1 class="font-weight-bold" style="font-size: 72px; color: {{ $score >= 80 ? '#28a745' : ($score >= 50 ? '#ffc107' : '#dc3545') }};">
                        {{ $score }}%
                    </h1>
                    <h5 class="text-dark font-weight-bold">Integrity Score</h5>
                    <p class="text-muted">Skor ini menggambarkan kesehatan integritas data sistem</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 font-weight-bold">
                    <i class="fas fa-exclamation-circle mr-1"></i> Anomali Terdeteksi
                </div>
                <div class="card-body">
                    @if(count($anomalies) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($anomalies as $anomaly)
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                                    {{ $anomaly }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="text-muted mb-0">Tidak ada anomali yang terdeteksi. Data dalam kondisi baik.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
