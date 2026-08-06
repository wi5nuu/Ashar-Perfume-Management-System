@extends('layouts.app')

@section('title', 'Detail Karyawan - ' . ($employee->nickname ?? $employee->name))

@push('styles')
<style>
:root { --primary: #FF6B35; --primary-dark: #E55A2B; --secondary: #2D3047; }

.page-header-apms {
    background: linear-gradient(135deg, var(--secondary) 0%, #3d4268 100%);
    border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; color: #fff;
}
.page-header-apms .breadcrumb { background: transparent; padding: 0; margin: 0; }
.page-header-apms .breadcrumb-item,
.page-header-apms .breadcrumb-item a { color: rgba(255,255,255,.65); font-size: .82rem; }
.page-header-apms .breadcrumb-item.active { color: rgba(255,255,255,.9); }
.page-header-apms .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.4); }

.profile-hero {
    background: #fff; border-radius: 16px;
    box-shadow: 0 2px 12px rgba(45,48,71,.07);
    padding: 2rem; margin-bottom: 1.5rem;
}
.profile-avatar-lg {
    width: 90px; height: 90px; border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    font-size: 2.2rem; font-weight: 700; color: #fff;
    flex-shrink: 0;
}
.badge-modern {
    padding: .35em .8em; border-radius: 20px; font-size: .75rem;
    font-weight: 600; letter-spacing: .03em; display: inline-flex; align-items: center; gap: 4px;
}
.badge-active   { background: #e8f9f0; color: #1a7a45; }
.badge-inactive { background: #fef0f0; color: #c0392b; }
.badge-login    { background: #e8f0fe; color: #3b5bdb; }
.badge-store    { background: #fff3e0; color: #e65100; }
.badge-role     { background: #f3e8ff; color: #6f42c1; }

.kpi-card {
    border: none; border-radius: 14px; padding: 1.1rem 1.25rem;
    background: #fff; box-shadow: 0 2px 12px rgba(45,48,71,.07);
    transition: transform .18s, box-shadow .18s;
}
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(45,48,71,.12); }
.kpi-card .kpi-icon {
    width: 44px; height: 44px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.15rem; flex-shrink: 0;
}
.kpi-card .kpi-value { font-size: 1.6rem; font-weight: 700; line-height: 1.1; color: var(--secondary); }
.kpi-card .kpi-label { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #8a8fa8; margin-top: .2rem; }

.card-apms { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(45,48,71,.07); }

.profile-tabs .nav-link {
    color: #6b7280; font-weight: 600; font-size: .85rem;
    border: none; border-bottom: 3px solid transparent;
    padding: .75rem 1.25rem; border-radius: 0; background: transparent;
    transition: all .15s;
}
.profile-tabs .nav-link:hover { color: var(--primary); }
.profile-tabs .nav-link.active { color: var(--primary); border-bottom-color: var(--primary); }
.profile-tabs { border-bottom: 2px solid #f2f3f8; }

.info-row { display: flex; align-items: flex-start; padding: .7rem 0; border-bottom: 1px solid #f2f3f8; }
.info-row:last-child { border-bottom: none; }
.info-label { font-size: .78rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #8a8fa8; min-width: 160px; flex-shrink: 0; padding-top: .1rem; }
.info-value { font-size: .88rem; color: #2d3047; font-weight: 500; }

.btn-primary-apms {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border: none; color: #fff; border-radius: 8px;
    font-weight: 600; font-size: .85rem; padding: .5rem 1.1rem;
    transition: all .2s; box-shadow: 0 3px 10px rgba(255,107,53,.25);
}
.btn-primary-apms:hover { background: linear-gradient(135deg, var(--primary-dark), #c94d22); color: #fff; transform: translateY(-1px); }

.table-modern { border-collapse: separate; border-spacing: 0; width: 100%; }
.table-modern thead th {
    background: #f8f9fc; color: #5a5f7d; font-size: .75rem;
    font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
    padding: .85rem 1rem; border-bottom: 2px solid #eef0f7; border-top: none;
}
.table-modern tbody td {
    padding: .85rem 1rem; border-bottom: 1px solid #f2f3f8;
    vertical-align: middle; color: #3d4268; font-size: .88rem;
}
.table-modern tbody tr:last-child td { border-bottom: none; }
.table-modern tbody tr:hover td { background: #fff9f6; }

.empty-state { padding: 3rem 2rem; text-align: center; }
.empty-state .empty-icon { font-size: 2.5rem; color: #d1d5e8; margin-bottom: .75rem; }
</style>

@endpush

@section('content')
<div class="container-fluid pt-2 pb-4">

    {{-- Page Header --}}
    <div class="page-header-apms">
        <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap:.75rem;">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home mr-1"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Karyawan</a></li>
                        <li class="breadcrumb-item active">{{ $employee->nickname ?? $employee->name }}</li>
                    </ol>
                </nav>
                <h4 class="mb-0 font-weight-bold" style="font-size:1.35rem;">
                    <i class="fas fa-id-card mr-2" style="color:var(--primary);"></i>Profil Karyawan
                </h4>
            </div>
            <div class="d-flex flex-wrap" style="gap:.5rem;">
                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary-apms">
                    <i class="fas fa-pen mr-1"></i> Edit Data
                </a>
                <a href="{{ route('employees.index') }}" class="btn"
                   style="border:1.5px solid rgba(255,255,255,.4);color:#fff;border-radius:8px;font-size:.85rem;font-weight:600;padding:.5rem 1.1rem;">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Profile Hero --}}
    <div class="profile-hero">
        @php
            $colors   = ['#FF6B35','#4e73df','#1cc88a','#6f42c1','#e83e8c','#fd7e14'];
            $color    = $colors[abs(crc32($employee->name)) % count($colors)];
            $initials = strtoupper(mb_substr($employee->nickname ?? $employee->name, 0, 1))
                      . strtoupper(mb_substr(explode(' ', $employee->name)[1] ?? '', 0, 1));
        @endphp
        <div class="d-flex flex-wrap align-items-center" style="gap:1.5rem;">
            <div class="profile-avatar-lg" style="background:{{ $color }};">{{ $initials }}</div>
            <div class="flex-fill">
                <div class="d-flex flex-wrap align-items-center" style="gap:.5rem;">
                    <h4 class="mb-0 font-weight-bold" style="color:var(--secondary);">
                        {{ $employee->nickname ?? $employee->name }}
                    </h4>
                    @if($employee->is_store_employee)
                        <span class="badge-modern badge-store"><i class="fas fa-store" style="font-size:.65rem;"></i> Karyawan Toko</span>
                    @else
                        <span class="badge-modern badge-login"><i class="fas fa-user-shield" style="font-size:.65rem;"></i> Akses Login</span>
                    @endif
                    @if($employee->role)
                        <span class="badge-modern badge-role">{{ ucfirst($employee->role) }}</span>
                    @endif
                </div>
                <p class="mb-1 mt-1" style="color:#8a8fa8;font-size:.85rem;">
                    {{ $employee->full_name ?? $employee->name }}
                    @if($employee->employee_id)
                        &nbsp;·&nbsp;<span style="color:var(--primary);font-weight:600;">{{ $employee->employee_id }}</span>
                    @endif
                </p>
                <div class="d-flex flex-wrap" style="gap:1rem;margin-top:.5rem;">
                    @if($employee->email)
                    <span style="font-size:.82rem;color:#6b7280;">
                        <i class="fas fa-envelope mr-1" style="color:var(--primary);"></i>{{ $employee->email }}
                    </span>
                    @endif
                    @if($employee->phone)
                    <span style="font-size:.82rem;color:#6b7280;">
                        <i class="fas fa-phone mr-1" style="color:var(--primary);"></i>{{ $employee->phone }}
                    </span>
                    @endif
                    <span style="font-size:.82rem;color:#6b7280;">
                        <i class="fas fa-building mr-1" style="color:var(--primary);"></i>{{ $employee->branch->name ?? 'Pusat' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row mb-4" style="row-gap:1rem;">
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="kpi-value" style="color:var(--primary);">
                            @php
                                $joined = $employee->created_at ?? now();
                                $months = $joined->diffInMonths(now());
                                $years  = floor($months / 12);
                                $rem    = $months % 12;
                            @endphp
                            {{ $years > 0 ? $years . ' th' : $rem . ' bln' }}
                        </div>
                        <div class="kpi-label">Masa Kerja</div>
                    </div>
                    <div class="kpi-icon" style="background:rgba(255,107,53,.1);color:var(--primary);">
                        <i class="fas fa-briefcase"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="kpi-value" style="color:#1a7a45;font-size:1.2rem;">
                            Rp {{ number_format($employee->payrolls()->whereYear('created_at', now()->year)->sum('total_salary') ?? 0, 0, ',', '.') }}
                        </div>
                        <div class="kpi-label">Total Gaji YTD</div>
                    </div>
                    <div class="kpi-icon" style="background:#e8f9f0;color:#1a7a45;">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="kpi-value" style="color:#3b5bdb;">
                            {{ $employee->attendances()->whereMonth('date', now()->month)->whereYear('date', now()->year)->where('status', 'present')->count() ?? 0 }}
                        </div>
                        <div class="kpi-label">Hadir Bulan Ini</div>
                    </div>
                    <div class="kpi-icon" style="background:#e8f0fe;color:#3b5bdb;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="kpi-value" style="color:#6f42c1;">—</div>
                        <div class="kpi-label">Cuti Tersisa</div>
                    </div>
                    <div class="kpi-icon" style="background:#f3e8ff;color:#6f42c1;">
                        <i class="fas fa-umbrella-beach"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="card card-apms">
        <div class="card-header bg-white px-4 pt-3 pb-0" style="border-bottom:none;">
            <ul class="nav profile-tabs" id="profileTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-info" data-toggle="tab" href="#pane-info" role="tab">
                        <i class="fas fa-id-card mr-1"></i> Info Pribadi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-payroll" data-toggle="tab" href="#pane-payroll" role="tab">
                        <i class="fas fa-money-check-alt mr-1"></i> Riwayat Gaji
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-attendance" data-toggle="tab" href="#pane-attendance" role="tab">
                        <i class="fas fa-clipboard-list mr-1"></i> Kehadiran
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-docs" data-toggle="tab" href="#pane-docs" role="tab">
                        <i class="fas fa-folder-open mr-1"></i> Dokumen
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body tab-content px-4 py-4" id="profileTabContent">

            {{-- Tab: Info Pribadi --}}
            <div class="tab-pane fade show active" id="pane-info" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold mb-3" style="color:var(--secondary);font-size:.82rem;text-transform:uppercase;letter-spacing:.06em;">
                            <i class="fas fa-user mr-1" style="color:var(--primary);"></i> Data Pribadi
                        </h6>
                        <div class="info-row">
                            <span class="info-label">Nama Panggilan</span>
                            <span class="info-value">{{ $employee->nickname ?? '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Nama Lengkap</span>
                            <span class="info-value">{{ $employee->full_name ?? $employee->name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">NIK (KTP)</span>
                            <span class="info-value">{{ $employee->nik ?? '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Jenis Kelamin</span>
                            <span class="info-value">
                                @if($employee->gender === 'male') <i class="fas fa-mars mr-1" style="color:#3b5bdb;"></i> Laki-laki
                                @elseif($employee->gender === 'female') <i class="fas fa-venus mr-1" style="color:#e83e8c;"></i> Perempuan
                                @else — @endif
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tempat / Tgl Lahir</span>
                            <span class="info-value">
                                {{ $employee->place_of_birth ?? '' }}
                                @if($employee->date_of_birth)
                                    {{ $employee->place_of_birth ? ', ' : '' }}
                                    {{ \Carbon\Carbon::parse($employee->date_of_birth)->translatedFormat('d F Y') }}
                                @else — @endif
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Agama</span>
                            <span class="info-value">{{ ucfirst($employee->religion ?? '—') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold mb-3" style="color:var(--secondary);font-size:.82rem;text-transform:uppercase;letter-spacing:.06em;">
                            <i class="fas fa-briefcase mr-1" style="color:var(--primary);"></i> Data Pekerjaan
                        </h6>
                        <div class="info-row">
                            <span class="info-label">ID Karyawan</span>
                            <span class="info-value" style="color:var(--primary);font-weight:600;">{{ $employee->employee_id ?? '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Posisi / Role</span>
                            <span class="info-value">
                                @if($employee->role)
                                    <span class="badge-modern badge-role">{{ ucfirst($employee->role) }}</span>
                                @else — @endif
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Cabang</span>
                            <span class="info-value">{{ $employee->branch->name ?? 'Pusat' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tipe</span>
                            <span class="info-value">
                                @if($employee->is_store_employee)
                                    <span class="badge-modern badge-store">Karyawan Toko</span>
                                @else
                                    <span class="badge-modern badge-login">Akses Login</span>
                                @endif
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Bergabung</span>
                            <span class="info-value">
                                {{ $employee->created_at ? $employee->created_at->translatedFormat('d F Y') : '—' }}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Gaji Pokok</span>
                            <span class="info-value" style="color:#1a7a45;font-weight:600;">
                                @if($employee->basic_salary)
                                    Rp {{ number_format($employee->basic_salary, 0, ',', '.') }}
                                @else — @endif
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email</span>
                            <span class="info-value">{{ $employee->email ?? '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Telepon</span>
                            <span class="info-value">{{ $employee->phone ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab: Riwayat Gaji --}}
            <div class="tab-pane fade" id="pane-payroll" role="tabpanel">
                @php $payrolls = $employee->payrolls()->latest()->take(12)->get(); @endphp
                @if($payrolls->count())
                <div class="table-responsive">
                    <table class="table-modern w-100">
                        <thead>
                            <tr>
                                <th>Periode</th>
                                <th>Gaji Pokok</th>
                                <th>Tunjangan</th>
                                <th>Potongan</th>
                                <th>Total Bersih</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payrolls as $pr)
                            <tr>
                                <td class="font-weight-bold">{{ $pr->month ?? '—' }}</td>
                                <td>Rp {{ number_format($pr->basic_salary ?? 0, 0, ',', '.') }}</td>
                                <td class="text-info">Rp {{ number_format($pr->allowance ?? 0, 0, ',', '.') }}</td>
                                <td class="text-danger">Rp {{ number_format($pr->deduction ?? 0, 0, ',', '.') }}</td>
                                <td class="font-weight-bold" style="color:#1a7a45;">Rp {{ number_format($pr->total_salary ?? 0, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge-modern" style="background:#e8f9f0;color:#1a7a45;">
                                        <i class="fas fa-check-circle" style="font-size:.65rem;"></i> Terhitung
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-money-check-alt"></i></div>
                    <p class="text-muted" style="font-size:.88rem;">Belum ada riwayat penggajian.</p>
                </div>
                @endif
            </div>

            {{-- Tab: Kehadiran --}}
            <div class="tab-pane fade" id="pane-attendance" role="tabpanel">
                @php
                    $attends = $employee->attendances()
                        ->whereMonth('date', now()->month)
                        ->whereYear('date', now()->year)
                        ->orderBy('date', 'desc')->take(31)->get();
                @endphp
                @if($attends->count())
                <div class="table-responsive">
                    <table class="table-modern w-100">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attends as $att)
                            @php
                                $statusMap = [
                                    'present' => ['label'=>'Hadir',   'bg'=>'#e8f9f0','color'=>'#1a7a45','icon'=>'check-circle'],
                                    'late'    => ['label'=>'Terlambat','bg'=>'#fff3e0','color'=>'#e65100','icon'=>'clock'],
                                    'permit'  => ['label'=>'Izin',    'bg'=>'#e8f4fd','color'=>'#0c7abf','icon'=>'info-circle'],
                                    'sick'    => ['label'=>'Sakit',   'bg'=>'#e8f0fe','color'=>'#3b5bdb','icon'=>'heartbeat'],
                                    'absent'  => ['label'=>'Absen',   'bg'=>'#fef0f0','color'=>'#c0392b','icon'=>'times-circle'],
                                ];
                                $s = $statusMap[$att->status ?? 'present'] ?? $statusMap['present'];
                            @endphp
                            <tr>
                                <td class="font-weight-bold">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('D, d M Y') }}</td>
                                <td>{{ $att->check_in ?? '—' }}</td>
                                <td>{{ $att->check_out ?? '—' }}</td>
                                <td>
                                    @if($att->check_in && $att->check_out)
                                        @php
                                            $dur = \Carbon\Carbon::parse($att->check_in)->diff(\Carbon\Carbon::parse($att->check_out));
                                        @endphp
                                        {{ $dur->h }}j {{ $dur->i }}m
                                    @else —
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-modern" style="background:{{ $s['bg'] }};color:{{ $s['color'] }};">
                                        <i class="fas fa-{{ $s['icon'] }}" style="font-size:.65rem;"></i> {{ $s['label'] }}
                                    </span>
                                </td>
                                <td style="color:#8a8fa8;font-size:.82rem;">{{ $att->reason ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-calendar-times"></i></div>
                    <p class="text-muted" style="font-size:.88rem;">Belum ada data kehadiran bulan ini.</p>
                </div>
                @endif
            </div>

            {{-- Tab: Dokumen --}}
            <div class="tab-pane fade" id="pane-docs" role="tabpanel">
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-folder-open"></i></div>
                    <h6 style="color:#3d4268;font-weight:600;">Belum ada dokumen</h6>
                    <p class="text-muted" style="font-size:.88rem;">Fitur manajemen dokumen akan segera tersedia.</p>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
