@extends('layouts.app')
@section('title', '403 - Akses Ditolak')

@section('content')
<div class="container-fluid pt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div style="font-size: 100px; color: #dc3545;">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h1 class="font-weight-bold" style="font-size: 72px; color: #dc3545;">403</h1>
            <h4 class="text-dark font-weight-bold mb-3">Akses Ditolak</h4>
            <p class="text-muted mb-4">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-4">
                <i class="fas fa-home mr-2"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
