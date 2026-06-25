@extends('layouts.app')
@section('title', 'Pengaturan Sistem - APMS')

@section('content')
<div class="container-fluid pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="font-weight-bold text-dark"><i class="fas fa-cog mr-2 text-primary"></i>Pengaturan Sistem</h3>
            <p class="text-muted">Konfigurasi infrastruktur dan backup data terpusat</p>
        </div>
    </div>

    <div class="row">
        <!-- Infrastructure Info -->
        <div class="col-md-6">
            <div class="card card-apms shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom-0 py-3 font-weight-bold">
                    <i class="fas fa-server mr-2"></i>Informasi Infrastruktur
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <tr><td>Nama Aplikasi</td><td class="text-right font-weight-bold">APMS - Automatic Parfume Management System</td></tr>
                        <tr><td>Versi Laravel</td><td class="text-right font-weight-bold">{{ app()->version() }}</td></tr>
                        <tr><td>Environment</td><td class="text-right font-weight-bold text-uppercase">{{ config('app.env') }}</td></tr>
                        <tr><td>Database Driver</td><td class="text-right font-weight-bold">{{ config('database.default') }}</td></tr>
                        <tr><td>PHP Version</td><td class="text-right font-weight-bold">{{ PHP_VERSION }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Backup & Restore -->
        <div class="col-md-6">
            <div class="card card-apms shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom-0 py-3 font-weight-bold">
                    <i class="fas fa-database mr-2"></i>Data Management
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="font-weight-bold">Backup Database</h6>
                            <small class="text-muted">Amankan seluruh data transaksi dan inventaris</small>
                        </div>
                        <form method="POST" action="{{ route('settings.backup') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary"><i class="fas fa-download mr-1"></i> Unduh Backup</button>
                        </form>
                    </div>
                    <hr>
                    <h6 class="font-weight-bold">Restore Database</h6>
                    <form method="POST" action="{{ route('settings.restore') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="custom-file mb-2">
                            <input type="file" name="backup_file" class="custom-file-input" id="backupFile" accept=".sql,.txt">
                            <label class="custom-file-label" for="backupFile">Pilih file backup (.sql)...</label>
                        </div>
                        <button type="submit" class="btn btn-outline-danger btn-block"><i class="fas fa-upload mr-1"></i> Jalankan Restore</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
