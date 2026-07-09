@extends('layouts.app')

@section('title', 'Attendance Center')

@section('content')
<div class="container-fluid">
    <!-- Top Stats & Filter Bar -->
    <div class="row mb-3">
        <div class="col-12 col-mobile-tight">
            <div class="card card-apms card-mobile-flush border-0 shadow-sm">
                <div class="card-body py-2 d-flex flex-wrap align-items-center justify-content-between">
                    <form action="{{ route('attendances.index') }}" method="GET" class="form-inline flex-fill">
                        <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                            <label class="smaller font-weight-bold text-muted text-uppercase mb-0">
                                <i class="fas fa-filter mr-1 text-primary-apms"></i> Periode:
                            </label>
                            <select name="month" class="form-control form-control-sm border-0 bg-light font-weight-bold">
                                @for($m=1; $m<=12; $m++)
                                    <option value="{{ $m }}" {{ (request('month', date('n')) == $m) ? 'selected' : '' }}>
                                        {{ date('M', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endfor
                            </select>
                            <select name="year" class="form-control form-control-sm border-0 bg-light font-weight-bold">
                                @for($y=date('Y'); $y>=date('Y')-2; $y--)
                                    <option value="{{ $y }}" {{ (request('year', date('Y')) == $y) ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                            <button type="submit" class="btn btn-primary-apms btn-sm px-3 shadow-sm">
                                <i class="fas fa-sync-alt mr-1"></i> UPDATE
                            </button>
                        </div>
                    </form>
                    <div class="d-none d-md-flex align-items-center h-100 border-left pl-3 ml-3">
                        <div class="text-right">
                            <div class="smaller text-muted text-uppercase font-weight-bold">Total Hari Ini</div>
                            <div class="font-weight-bold text-primary-apms">{{ $attendances->where('date', \Carbon\Carbon::today())->count() }} <span class="smaller font-weight-normal text-muted">Karyawan</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left: Quick Absen Form -->
        <div class="col-lg-4 col-md-5 col-mobile-tight mb-3">
            <div class="card card-apms card-mobile-flush border-0 shadow-sm sticky-top" style="top: 20px; z-index: 10;">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 font-weight-bold text-dark">
                        <i class="fas fa-user-check mr-2 text-primary-apms"></i> Pencatatan Kehadiran
                    </h6>
                    <p class="text-muted smaller mb-0">Input absensi manual karyawan.</p>
                </div>
                <div class="card-body pt-0">
                    <form action="{{ route('attendances.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="manual" value="1">
                        
                        <div class="form-group mb-3">
                            <label class="smaller font-weight-bold text-muted text-uppercase">Pilih Karyawan</label>
                            <select name="cashier_names" id="cashier_names" class="form-control form-control-sm @error('cashier_names') is-invalid @enderror select2-apms" required>
                                <option value="">-- Cari Nama --</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->name }}" data-name="{{ $employee->full_name ?? $employee->name }}" data-role="{{ $employee->role }}">
                                        {{ $employee->full_name ?? $employee->name }} ({{ strtoupper($employee->role) }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="name" id="hidden_name">
                            <input type="hidden" name="role" id="hidden_role">
                        </div>

                        <div class="form-group mb-3">
                            <label class="smaller font-weight-bold text-muted text-uppercase">Status</label>
                            <div class="d-flex justify-content-between p-2 bg-light rounded shadow-inner" style="gap: 5px;">
                                <div class="flex-fill text-center">
                                    <input type="radio" name="status" id="s_hadir" value="present" class="d-none" checked onclick="toggleReason(false)">
                                    <label for="s_hadir" class="btn btn-block btn-sm status-label py-2 mb-0" data-status="present">HADIR</label>
                                </div>
                                <div class="flex-fill text-center">
                                    <input type="radio" name="status" id="s_sakit" value="sick" class="d-none" onclick="toggleReason(true)">
                                    <label for="s_sakit" class="btn btn-block btn-sm status-label py-2 mb-0" data-status="sick">SAKIT</label>
                                </div>
                                <div class="flex-fill text-center">
                                    <input type="radio" name="status" id="s_izin" value="permit" class="d-none" onclick="toggleReason(true)">
                                    <label for="s_izin" class="btn btn-block btn-sm status-label py-2 mb-0" data-status="permit">IZIN</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3 d-none" id="reason_container">
                            <label class="smaller font-weight-bold text-muted text-uppercase">Keterangan / Alasan</label>
                            <textarea name="reason" id="reason" class="form-control form-control-sm" rows="2" placeholder="Tuliskan alasan..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary-apms btn-block shadow-sm py-2 font-weight-bold mt-2">
                            <i class="fas fa-save mr-2"></i> SIMPAN ABSENSI
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right: History Log -->
        <div class="col-lg-8 col-md-7 col-mobile-tight">
            <div class="card card-apms card-mobile-flush border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-dark">
                        <i class="fas fa-list-ul mr-2 text-primary-apms"></i> Log Kehadiran Karyawan
                    </h6>
                    <div class="badge badge-faint-primary px-2 py-1 smaller font-weight-normal">
                        Result: {{ $attendances->total() }} Records
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-compact mb-0 v-align-middle">
                            <thead class="bg-light">
                                <tr class="text-nowrap smaller">
                                    <th class="pl-3">TANGGAL / JAM</th>
                                    <th>KARYAWAN</th>
                                    <th class="text-center">STATUS</th>
                                    <th class="text-right pr-3">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                <tr>
                                    <td class="pl-3">
                                        <div class="font-weight-bold text-dark" style="font-size: 0.85rem;">{{ $attendance->date ? $attendance->date->format('d/m/Y') : '-' }}</div>
                                        <div class="smaller text-muted d-flex align-items-center">
                                            @if($attendance->status == 'present')
                                                <i class="fas fa-clock mr-1 text-success"></i> {{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '-' }}
                                                @if($attendance->time_out)
                                                    <span class="mx-1">-</span> <i class="fas fa-sign-out-alt mr-1 text-danger"></i> {{ \Carbon\Carbon::parse($attendance->time_out)->format('H:i') }}
                                                @endif
                                            @else
                                                <span class="text-uppercase">OFF DAY</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle avatar-xs mr-2 bg-faint-primary font-weight-bold smaller">
                                                {{ substr($attendance->employee_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-weight-bold text-dark text-truncate" style="max-width: 140px; font-size: 0.8rem;">{{ $attendance->employee_name }}</div>
                                                @php
                                                    $roleClass = 'bg-faint-secondary';
                                                    if($attendance->role == 'admin') $roleClass = 'bg-faint-indigo text-indigo';
                                                    elseif($attendance->role == 'supervisor') $roleClass = 'bg-faint-teal text-teal';
                                                    elseif($attendance->role == 'cashier') $roleClass = 'bg-faint-primary text-primary';
                                                    elseif($attendance->role == 'packing') $roleClass = 'bg-faint-warning text-warning';
                                                @endphp
                                                <span class="badge {{ $roleClass }} px-1 py-0 smaller" style="font-size: 10px;">{{ strtoupper($attendance->role) }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($attendance->status == 'present')
                                            <span class="badge badge-success shadow-none px-2" style="font-size: 10px;">HADIR</span>
                                        @elseif($attendance->status == 'sick')
                                            <span class="badge badge-warning shadow-none px-2 text-white" style="font-size: 10px;">SAKIT</span>
                                        @elseif($attendance->status == 'permit')
                                            <span class="badge badge-info shadow-none px-2" style="font-size: 10px;">IZIN</span>
                                        @else
                                            <span class="badge badge-danger shadow-none px-2" style="font-size: 10px;">ALPA</span>
                                        @endif
                                        
                                        @if($attendance->reason)
                                            <div class="smaller text-muted mt-1 font-italic truncate-text" style="max-width: 120px;" title="{{ $attendance->reason }}">"{{ $attendance->reason }}"</div>
                                        @endif
                                    </td>
                                    <td class="text-right pr-3">
                                        @if($attendance->status == 'present' && !$attendance->time_out && $attendance->date->isToday())
                                            <form action="{{ route('attendances.checkout', $attendance->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger px-2 border-2 font-weight-bold">
                                                    OUT <i class="fas fa-sign-out-alt ml-1"></i>
                                                </button>
                                            </form>
                                        @elseif($attendance->time_out)
                                            <i class="fas fa-check-double text-success smaller" title="Shift Selesai"></i>
                                        @else
                                            <span class="smaller text-muted">--</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i class="fas fa-calendar-times transition-3 fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Tidak ada data absensi untuk periode ini.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($attendances->hasPages())
                    <div class="px-3 py-2 bg-light border-top">
                        <div class="float-right smaller">
                            {{ $attendances->appends(request()->all())->links() }}
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .status-label {
        border: 2px solid transparent;
        background: white;
        color: #6c757d;
        font-weight: bold;
        transition: all 0.2s;
    }
    input[type="radio"]:checked + .status-label[data-status="present"] {
        border-color: #28a745;
        background: #28a745;
        color: white;
    }
    input[type="radio"]:checked + .status-label[data-status="sick"] {
        border-color: #ffc107;
        background: #ffc107;
        color: white;
    }
    input[type="radio"]:checked + .status-label[data-status="permit"] {
        border-color: #17a2b8;
        background: #17a2b8;
        color: white;
    }
    .v-align-middle td {
        vertical-align: middle !important;
    }
</style>

@push('scripts')
<script>
    function toggleReason(show) {
        const container = document.getElementById('reason_container');
        if (show) {
            container.classList.remove('d-none');
        } else {
            container.classList.add('d-none');
        }
    }

    document.getElementById('cashier_names').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        document.getElementById('hidden_name').value = selectedOption.getAttribute('data-name');
        document.getElementById('hidden_role').value = selectedOption.getAttribute('data-role');
    });
</script>
@endpush
@endsection
