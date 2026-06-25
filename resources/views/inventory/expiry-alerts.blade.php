@extends('layouts.app')
@section('title', 'Alert Kadaluarsa')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0"><i class="fas fa-exclamation-triangle"></i> Alert Kadaluarsa Produk</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventory</a></li>
                    <li class="breadcrumb-item active">Expiry Alerts</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    {{-- Summary --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $critical->count() }}</h3>
                    <p>Kadaluarsa / <= 30 Hari</p>
                </div>
                <div class="icon"><i class="fas fa-skull-crossbones"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $warning->count() }}</h3>
                    <p>31-60 Hari</p>
                </div>
                <div class="icon"><i class="fas fa-hourglass-half"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $notice->count() }}</h3>
                    <p>61-90 Hari</p>
                </div>
                <div class="icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
    </div>

    {{-- Critical / Expired --}}
    <div class="card card-outline card-danger">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-skull-crossbones mr-2"></i>Kadaluarsa / <= 30 Hari</h3></div>
        <div class="card-body p-0">
            @include('inventory._expiry-table', ['items' => $critical, 'color' => 'danger'])
        </div>
    </div>

    {{-- Warning --}}
    <div class="card card-outline card-warning">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-hourglass-half mr-2"></i>31-60 Hari</h3></div>
        <div class="card-body p-0">
            @include('inventory._expiry-table', ['items' => $warning, 'color' => 'warning'])
        </div>
    </div>

    {{-- Notice --}}
    <div class="card card-outline card-info">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-clock mr-2"></i>61-90 Hari</h3></div>
        <div class="card-body p-0">
            @include('inventory._expiry-table', ['items' => $notice, 'color' => 'info'])
        </div>
    </div>
</div>
@endsection
