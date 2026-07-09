@extends('layouts.app')
@section('title', 'Backup Database - APMS')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="font-weight-bold mb-0"><i class="fas fa-database mr-2 text-primary"></i>Backup Database</h5>
        <form action="{{ route('admin.monitoring.backup.create') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary-apms" onclick="this.disabled=true; this.innerHTML='<span class=\'spinner-border spinner-border-sm mr-1\'></span>Memproses...'; this.form.submit();">
                <i class="fas fa-plus mr-1"></i> Backup Sekarang
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible rounded-lg">{{ session('success') }}<button class="close" data-dismiss="alert">&times;</button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible rounded-lg">{{ session('error') }}<button class="close" data-dismiss="alert">&times;</button></div>
    @endif

    <div class="card shadow-sm border-0" style="border-radius: 16px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">File</th>
                            <th class="border-0">Ukuran</th>
                            <th class="border-0">Tanggal</th>
                            <th class="border-0">Enkripsi</th>
                            <th class="border-0">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                            <tr>
                                <td class="align-middle"><code>{{ $backup['filename'] }}</code></td>
                                <td class="align-middle">{{ round($backup['size'] / 1024 / 1024, 2) }} MB</td>
                                <td class="align-middle">{{ $backup['created_at'] }}</td>
                                <td class="align-middle">
                                    @if($backup['is_encrypted'])
                                        <span class="badge badge-success px-3 py-1">AES-256-CBC</span>
                                    @else
                                        <span class="badge badge-secondary px-3 py-1">Tidak</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <a href="{{ route('admin.monitoring.backup.download', base64_encode($backup['filename'])) }}" class="btn btn-sm btn-outline-primary mr-1" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <form action="{{ route('admin.monitoring.backup.delete', base64_encode($backup['filename'])) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus backup ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-5">Belum ada backup. Klik "Backup Sekarang" untuk membuat backup pertama.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
