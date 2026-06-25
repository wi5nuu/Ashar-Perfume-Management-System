@extends('layouts.app')
@section('title', 'Log Audit - APMS')

@section('content')
<div class="container-fluid pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-dark"><i class="fas fa-clipboard-list mr-2 text-primary"></i>Log Audit</h4>
            <p class="text-muted">Riwayat perubahan data dalam sistem</p>
        </div>
        <a href="{{ route('admin.security.overview') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
    </div>

    <div class="card card-apms shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <form method="GET" class="form-inline">
                <div class="form-group mr-2 mb-2">
                    <select name="action" class="form-control form-control-sm">
                        <option value="">Semua Aksi</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ $action }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mr-2 mb-2">
                    <input type="text" name="model" class="form-control form-control-sm" placeholder="Model..." value="{{ request('model') }}">
                </div>
                <div class="form-group mr-2 mb-2">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="form-group mr-2 mb-2">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <button type="submit" class="btn btn-primary btn-sm mb-2"><i class="fas fa-filter mr-1"></i> Filter</button>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Aksi</th>
                            <th>Target</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td><small>{{ $log->created_at->format('d/m/Y H:i') }}</small></td>
                            <td class="font-weight-bold">{{ $log->user->name ?? 'System' }}</td>
                            <td>
                                @if($log->action === 'created')
                                    <span class="badge badge-success">Created</span>
                                @elseif($log->action === 'updated')
                                    <span class="badge badge-warning">Updated</span>
                                @elseif($log->action === 'deleted')
                                    <span class="badge badge-danger">Deleted</span>
                                @elseif($log->action === 'restored')
                                    <span class="badge badge-info">Restored</span>
                                @else
                                    <span class="badge badge-secondary">{{ $log->action }}</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ class_basename($log->target_model) }} #{{ $log->target_id }}</small>
                            </td>
                            <td><small>{{ $log->ip_address ?? '-' }}</small></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada log audit</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
        <div class="card-footer bg-white">{{ $logs->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>
@endsection
