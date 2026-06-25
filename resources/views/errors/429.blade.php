@extends('layouts.app')
@section('title', '429 - Terlalu Banyak Permintaan')

@section('content')
<div class="container-fluid pt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div style="font-size: 100px; color: #6c757d;">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <h1 class="font-weight-bold" style="font-size: 72px; color: #6c757d;">429</h1>
            <h4 class="text-dark font-weight-bold mb-3">Terlalu Banyak Permintaan</h4>
            <p class="text-muted mb-4">Anda mengirim terlalu banyak permintaan. Silakan coba lagi nanti.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-4">
                <i class="fas fa-home mr-2"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
