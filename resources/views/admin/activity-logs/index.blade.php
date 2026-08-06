@extends('layouts.app')
@section('title', 'Log Aktivitas - APMS')

@section('content')
{{-- All styles loaded globally from resources/sass/_apms.scss --}}

<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-history mr-2"></i>Log Aktivitas Sistem</h1>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item active">Log Aktivitas</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <x-alert />
            <small style="opacity:.75">Pantau seluruh aktivitas pengguna secara real-time</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.activity-logs.index', array_merge(request()->query(), ['export' => 'csv'])) }}"
               class="btn btn-success btn-sm font-weight-600">
                <i class="fas fa-file-csv mr-1"></i> Export CSV
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}">
            <div class="row align-items-end g-2">
                <div class="col-12 col-md-3 mb-2 mb-md-0">
                    <label class="mb-1" style="font-size:.75rem;font-weight:600;color:#5a5f7d;text-transform:uppercase;letter-spacing:.05em">Event / Tipe Aksi</label>
                    <select name="event" class="form-control form-control-sm">
                        <option value="">Semua Event</option>
                        @foreach($events as $e)
                        <option value="{{ $e }}" {{ request('event') == $e ? 'selected' : '' }}>{{ ucfirst($e) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 mb-2 mb-md-0">
                    <label class="mb-1" style="font-size:.75rem;font-weight:600;color:#5a5f7d;text-transform:uppercase;letter-spacing:.05em">Modul / Subject</label>
                    <select name="subject_type" class="form-control form-control-sm">
                        <option value="">Semua Modul</option>
                        @foreach($subjectTypes as $s)
                        <option value="{{ $s }}" {{ request('subject_type') == $s ? 'selected' : '' }}>{{ class_basename($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 mb-2 mb-md-0">
                    <label class="mb-1" style="font-size:.75rem;font-weight:600;color:#5a5f7d;text-transform:uppercase;letter-spacing:.05em">Dari Tanggal</label>
                    <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
                </div>
                <div class="col-6 col-md-2 mb-2 mb-md-0">
                    <label class="mb-1" style="font-size:.75rem;font-weight:600;color:#5a5f7d;text-transform:uppercase;letter-spacing:.05em">Sampai Tanggal</label>
                    <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
                </div>
                <div class="col-12 col-md-2 mb-2 mb-md-0">
                    <label class="mb-1 d-block" style="font-size:.75rem;font-weight:600;color:transparent">.</label>
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary-apms btn-sm flex-grow-1">
                            <i class="fas fa-search mr-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="card card-apms">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Event</th>
                            <th class="d-none d-md-table-cell">Modul</th>
                            <th>Deskripsi</th>
                            <th class="d-none d-md-table-cell">IP Address</th>
                            <th style="width:60px">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $l)
                        @php
                            $ev = strtolower($l->event ?? '');
                            $evClass = match(true) {
                                str_contains($ev, 'creat') => 'badge-created',
                                str_contains($ev, 'updat') || str_contains($ev, 'edit') => 'badge-updated',
                                str_contains($ev, 'delet') => 'badge-deleted',
                                str_contains($ev, 'login') || str_contains($ev, 'auth') => 'badge-login',
                                default => 'badge-viewed',
                            };
                            $evIcon = match(true) {
                                str_contains($ev, 'creat') => 'fa-plus-circle',
                                str_contains($ev, 'updat') || str_contains($ev, 'edit') => 'fa-edit',
                                str_contains($ev, 'delet') => 'fa-trash',
                                str_contains($ev, 'login') => 'fa-sign-in-alt',
                                default => 'fa-eye',
                            };
                            $colors = ['#FF6B35','#10b981','#3b82f6','#8b5cf6','#f59e0b'];
                            $ci = abs(crc32($l->causer?->name ?? 'S')) % count($colors);
                        @endphp
                        <tr>
                            <td style="white-space:nowrap;font-size:.82rem">
                                <div style="font-weight:600">{{ $l->created_at->format('d M Y') }}</div>
                                <div style="color:#8a8fa8;font-size:.75rem">{{ $l->created_at->format('H:i:s') }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar" style="background:{{ $colors[$ci] }}">
                                        {{ strtoupper(substr($l->causer?->name ?? 'S', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600;font-size:.85rem">{{ $l->causer?->name ?? 'System' }}</div>
                                        <div style="font-size:.73rem;color:#8a8fa8">{{ $l->causer?->role ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-event {{ $evClass }}">
                                    <i class="fas {{ $evIcon }}"></i>
                                    {{ ucfirst($l->event ?? '-') }}
                                </span>
                            </td>
                            <td class="d-none d-md-table-cell" style="font-size:.82rem">
                                <span style="background:#f1f5f9;padding:3px 8px;border-radius:6px;font-weight:600;color:#475569">
                                    {{ class_basename($l->subject_type ?? '') }}
                                </span>
                                @if($l->subject_id)
                                <span style="color:#94a3b8;font-size:.75rem"> #{{ $l->subject_id }}</span>
                                @endif
                            </td>
                            <td style="max-width:280px">
                                <div style="font-size:.84rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
                                     title="{{ $l->description }}">
                                    {{ Str::limit($l->description, 55) }}
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell" style="font-size:.78rem;color:#8a8fa8;font-family:monospace">
                                {{ $l->properties['ip'] ?? request()->ip() ?? '-' }}
                            </td>
                            <td>
                                <a href="{{ route('admin.activity-logs.show', $l->id) }}"
                                   class="btn btn-sm btn-outline-primary px-2 py-1" title="Lihat Detail">
                                    <i class="fas fa-eye" style="font-size:.75rem"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="text-center py-5">
                                    <i class="fas fa-clipboard-list" style="font-size:2.5rem;color:#d1d5e0"></i>
                                    <p class="mt-3 mb-0 font-weight-600" style="color:#3d4268">Tidak ada log aktivitas</p>
                                    <small class="text-muted">Coba ubah filter atau rentang tanggal</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($logs->hasPages())
            <div class="d-flex align-items-center justify-content-between px-3 py-2" style="border-top:1px solid #f2f3f8">
                <small class="text-muted">
                    Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }} log
                </small>
                {{ $logs->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
