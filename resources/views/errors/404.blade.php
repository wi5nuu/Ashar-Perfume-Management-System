@extends('layouts.app')
@section('title', '404 - Halaman Tidak Ditemukan')

@section('content')
<div class="container-fluid pt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div style="font-size: 100px; color: #ffc107;">
                <i class="fas fa-map-signs"></i>
            </div>
            <h1 class="font-weight-bold" style="font-size: 72px; color: #ffc107;">404</h1>
            <h4 class="text-dark font-weight-bold mb-3">Halaman Tidak Ditemukan</h4>
            <p class="text-muted mb-4">Halaman yang Anda cari tidak ada atau telah dipindahkan.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-4">
                <i class="fas fa-home mr-2"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
