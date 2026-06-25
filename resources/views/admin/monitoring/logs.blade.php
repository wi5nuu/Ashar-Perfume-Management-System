@extends('layouts.app')
@section('title', 'Log Viewer - APMS')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="font-weight-bold mb-0"><i class="fas fa-file-alt mr-2 text-primary"></i>Log Aplikasi</h5>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible rounded-lg">{{ session('success') }}<button class="close" data-dismiss="alert">&times;</button></div>
    @endif

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center py-3" style="border-radius: 16px;">
                <div class="text-danger font-weight-bold h4 mb-0">{{ $stats['errors'] }}</div>
                <small class="text-muted">Error</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center py-3" style="border-radius: 16px;">
                <div class="text-warning font-weight-bold h4 mb-0">{{ $stats['warnings'] }}</div>
                <small class="text-muted">Warning</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center py-3" style="border-radius: 16px;">
                <div class="text-info font-weight-bold h4 mb-0">{{ $stats['info'] }}</div>
                <small class="text-muted">Info</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center py-3" style="border-radius: 16px;">
                <div class="text-primary font-weight-bold h4 mb-0">{{ $stats['error_rate'] }}%</div>
                <small class="text-muted">Error Rate</small>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 16px;">
        <div class="card-body">
            <form method="GET" class="form-inline mb-3">
                <select name="level" class="form-control mr-2 mb-2">
                    <option value="">Semua Level</option>
                    @foreach($levels as $l)
                        <option value="{{ $l }}" @if($level === $l) selected @endif>{{ $l }}</option>
                    @endforeach
                </select>
                <input type="text" name="search" class="form-control mr-2 mb-2" placeholder="Cari pesan..." value="{{ $search ?? '' }}" style="min-width: 200px;">
                <button type="submit" class="btn btn-primary mb-2"><i class="fas fa-search mr-1"></i>Filter</button>
                @if($level || $search)
                    <a href="{{ route('admin.monitoring.logs') }}" class="btn btn-secondary mb-2 ml-1"><i class="fas fa-times mr-1"></i>Reset</a>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0" style="width: 180px;">Waktu</th>
                            <th class="border-0" style="width: 100px;">Level</th>
                            <th class="border-0">Pesan</th>
                            <th class="border-0" style="width: 120px;">File</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs['items'] as $log)
                            <tr>
                                <td class="align-middle text-nowrap small">{{ $log['timestamp'] }}</td>
                                <td class="align-middle">
                                    @php
                                        $badge = match($log['level']) {
                                            'ERROR' => 'danger',
                                            'CRITICAL' => 'danger',
                                            'ALERT' => 'danger',
                                            'EMERGENCY' => 'danger',
                                            'WARNING' => 'warning',
                                            'NOTICE' => 'info',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $badge }} px-2 py-1">{{ $log['level'] }}</span>
                                </td>
                                <td class="align-middle small" style="max-width: 500px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log['message'] }}">{{ $log['message'] }}</td>
                                <td class="align-middle small text-muted">{{ $log['file'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-5">Tidak ada log ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs['last_page'] > 1)
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">Menampilkan {{ count($logs['items']) }} dari {{ $logs['total'] }} entri</small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            @for($i = 1; $i <= $logs['last_page']; $i++)
                                <li class="page-item {{ $logs['page'] == $i ? 'active' : '' }}">
                                    <a class="page-link" href="{{ route('admin.monitoring.logs', array_filter(['page' => $i, 'level' => $level, 'search' => $search])) }}">{{ $i }}</a>
                                </li>
                            @endfor
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
