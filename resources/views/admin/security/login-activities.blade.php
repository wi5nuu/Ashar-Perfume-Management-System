@extends('layouts.app')
@section('title', 'Aktivitas Login - APMS')

@section('content')
<div class="container-fluid pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-dark"><i class="fas fa-sign-in-alt mr-2 text-primary"></i>Aktivitas Login</h4>
            <p class="text-muted">Riwayat percobaan login pengguna</p>
        </div>
        <a href="{{ route('admin.security.overview') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
    </div>

    <div class="card card-apms shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <form method="GET" class="form-inline">
                <div class="form-group mr-2 mb-2">
                    <input type="text" name="user_id" class="form-control form-control-sm" placeholder="User ID..." value="{{ request('user_id') }}">
                </div>
                <div class="form-group mr-2 mb-2">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="form-group mr-2 mb-2">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="form-check mr-2 mb-2">
                    <input type="checkbox" class="form-check-input" name="suspicious" value="1" id="suspicious" {{ request('suspicious') ? 'checked' : '' }}>
                    <label class="form-check-label" for="suspicious">Mencurigakan</label>
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
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                        <tr>
                            <td><small>{{ $activity->created_at->format('d/m/Y H:i') }}</small></td>
                            <td class="font-weight-bold">{{ $activity->user->name ?? 'Unknown' }}</td>
                            <td><code>{{ $activity->ip_address }}</code></td>
                            <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <small title="{{ $activity->user_agent }}">{{ $activity->user_agent }}</small>
                            </td>
                            <td>
                                @if($activity->is_suspicious)
                                    <span class="badge badge-danger">Mencurigakan</span>
                                @else
                                    <span class="badge badge-success">Normal</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada aktivitas login</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($activities->hasPages())
        <div class="card-footer bg-white">{{ $activities->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>
@endsection
