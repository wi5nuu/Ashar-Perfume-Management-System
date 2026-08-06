@extends('layouts.app')
@section('title', 'Kehadiran Karyawan')

@section('content')
{{-- All styles loaded globally from resources/sass/_apms.scss --}}

<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-clipboard-check mr-2"></i>Kehadiran Karyawan</h1>
                    <p class="mb-0 mt-1" style="color:rgba(255,255,255,.65);font-size:.82rem;">
                        Pantau dan kelola absensi seluruh karyawan
                    </p>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active">Kehadiran</li>
                    </ol>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @can('employees.create')
                    <a href="#" class="btn btn-light btn-sm" data-toggle="modal" data-target="#modalCatatKehadiran">
                        <i class="fas fa-plus mr-1"></i> Catat Kehadiran
                    </a>
                    @endcan
                    <a href="#" class="btn btn-success btn-sm" onclick="exportData()">
                        <i class="fas fa-file-excel mr-1"></i> Export
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <x-alert />

    {{-- KPI Cards --}}
    <div class="row mb-3">
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card kpi-green d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:rgba(16,185,129,.12)">
                    <i class="fas fa-user-check" style="color:#10b981"></i>
                </div>
                <div>
                    <div class="kpi-value" id="kpiHadir">--</div>
                    <div class="kpi-label">Hadir Hari Ini</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card kpi-yellow d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:rgba(245,158,11,.12)">
                    <i class="fas fa-clock" style="color:#f59e0b"></i>
                </div>
                <div>
                    <div class="kpi-value" id="kpiTerlambat">--</div>
                    <div class="kpi-label">Terlambat</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card kpi-blue d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:rgba(59,130,246,.12)">
                    <i class="fas fa-file-medical" style="color:#3b82f6"></i>
                </div>
                <div>
                    <div class="kpi-value" id="kpiIzinSakit">--</div>
                    <div class="kpi-label">Izin / Sakit</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card kpi-red d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:rgba(239,68,68,.12)">
                    <i class="fas fa-user-times" style="color:#ef4444"></i>
                </div>
                <div>
                    <div class="kpi-value" id="kpiAbsen">--</div>
                    <div class="kpi-label">Tidak Hadir</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <div class="row align-items-end g-2">
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <label class="mb-1" style="font-size:.78rem;font-weight:600;color:#5a5f7d;text-transform:uppercase;letter-spacing:.05em">Tanggal</label>
                <input type="date" id="filterTanggal" class="form-control form-control-sm"
                       value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <label class="mb-1" style="font-size:.78rem;font-weight:600;color:#5a5f7d;text-transform:uppercase;letter-spacing:.05em">Karyawan</label>
                <input type="text" id="filterKaryawan" class="form-control form-control-sm" placeholder="Cari nama karyawan...">
            </div>
            <div class="col-6 col-md-2 mb-2 mb-md-0">
                <label class="mb-1" style="font-size:.78rem;font-weight:600;color:#5a5f7d;text-transform:uppercase;letter-spacing:.05em">Departemen</label>
                <select id="filterDept" class="form-control form-control-sm">
                    <option value="">Semua</option>
                    <option>Penjualan</option>
                    <option>Gudang</option>
                    <option>Kasir</option>
                    <option>Admin</option>
                    <option>Manajemen</option>
                </select>
            </div>
            <div class="col-6 col-md-2 mb-2 mb-md-0">
                <label class="mb-1" style="font-size:.78rem;font-weight:600;color:#5a5f7d;text-transform:uppercase;letter-spacing:.05em">Status</label>
                <select id="filterStatus" class="form-control form-control-sm">
                    <option value="">Semua Status</option>
                    <option value="hadir">Hadir</option>
                    <option value="terlambat">Terlambat</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="absen">Tidak Hadir</option>
                </select>
            </div>
            <div class="col-12 col-md-2 mb-2 mb-md-0">
                <label class="mb-1 d-block" style="font-size:.78rem;font-weight:600;color:transparent">.</label>
                <button class="btn btn-secondary btn-sm btn-block" onclick="resetFilter()">
                    <i class="fas fa-redo mr-1"></i> Reset
                </button>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card card-apms">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0" id="attendanceTable">
                    <thead>
                        <tr>
                            <th style="width:36px">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Karyawan</th>
                            <th class="d-none d-md-table-cell">Departemen</th>
                            <th>Tanggal</th>
                            <th class="d-none d-sm-table-cell">Jam Masuk</th>
                            <th class="d-none d-sm-table-cell">Jam Keluar</th>
                            <th class="d-none d-md-table-cell">Durasi</th>
                            <th>Status</th>
                            <th class="d-none d-md-table-cell">Keterangan</th>
                            <th style="width:90px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="attendanceBody">
                        @forelse($attendances ?? [] as $att)
                        <tr>
                            <td><input type="checkbox" class="row-check form-check-input" value="{{ $att->id }}"></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @php
                                        $colors = ['#FF6B35','#10b981','#3b82f6','#8b5cf6','#f59e0b','#ef4444'];
                                        $ci = crc32($att->employee->name ?? 'X') % count($colors);
                                    @endphp
                                    <div class="emp-avatar" style="background:{{ $colors[abs($ci)] }}">
                                        {{ strtoupper(substr($att->employee->name ?? 'X', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600;font-size:.88rem">{{ $att->employee->nickname ?? $att->employee->name ?? '-' }}</div>
                                        <div style="font-size:.75rem;color:#8a8fa8">{{ $att->employee->employee_id ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <span style="font-size:.82rem;color:#6b7280">{{ $att->employee->department ?? '-' }}</span>
                            </td>
                            <td style="font-size:.85rem">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</td>
                            <td class="d-none d-sm-table-cell" style="font-size:.85rem;font-weight:500">
                                {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '-' }}
                            </td>
                            <td class="d-none d-sm-table-cell" style="font-size:.85rem;font-weight:500">
                                {{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '-' }}
                            </td>
                            <td class="d-none d-md-table-cell" style="font-size:.82rem;color:#6b7280">
                                @if($att->check_in && $att->check_out)
                                    @php
                                        $dur = \Carbon\Carbon::parse($att->check_in)->diffInMinutes(\Carbon\Carbon::parse($att->check_out));
                                        echo floor($dur/60).'j '.($dur%60).'m';
                                    @endphp
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @php $st = strtolower($att->status ?? 'hadir'); @endphp
                                @if($st === 'hadir')
                                    <span class="badge-modern badge-hadir"><i class="fas fa-check-circle"></i> Hadir</span>
                                @elseif($st === 'terlambat')
                                    <span class="badge-modern badge-terlambat"><i class="fas fa-clock"></i> Terlambat</span>
                                @elseif($st === 'izin')
                                    <span class="badge-modern badge-izin"><i class="fas fa-calendar-check"></i> Izin</span>
                                @elseif($st === 'sakit')
                                    <span class="badge-modern badge-sakit"><i class="fas fa-procedures"></i> Sakit</span>
                                @else
                                    <span class="badge-modern badge-absen"><i class="fas fa-times-circle"></i> Absen</span>
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell" style="font-size:.82rem;color:#6b7280">
                                {{ $att->notes ?? '-' }}
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @can('employees.edit')
                                    <button class="btn btn-sm btn-outline-primary px-2 py-1" title="Edit"
                                            onclick="editKehadiran({{ $att->id }})">
                                        <i class="fas fa-edit" style="font-size:.75rem"></i>
                                    </button>
                                    @endcan
                                    @can('employees.delete')
                                    <button class="btn btn-sm btn-outline-danger px-2 py-1 btn-delete"
                                            data-id="{{ $att->id }}"
                                            data-name="{{ $att->employee->name ?? '' }}"
                                            title="Hapus">
                                        <i class="fas fa-trash" style="font-size:.75rem"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="emptyRow">
                            <td colspan="10">
                                <div class="empty-state">
                                    <div class="empty-icon"><i class="fas fa-calendar-times"></i></div>
                                    <h6 style="color:#3d4268;font-weight:600">Tidak ada data kehadiran</h6>
                                    <p class="text-muted" style="font-size:.88rem">Belum ada catatan kehadiran untuk filter yang dipilih.</p>
                                    @can('employees.create')
                                    <button class="btn btn-primary-apms btn-sm mt-2" data-toggle="modal" data-target="#modalCatatKehadiran">
                                        <i class="fas fa-plus mr-1"></i> Catat Kehadiran
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(isset($attendances) && $attendances->hasPages())
            <div class="d-flex align-items-center justify-content-between px-3 py-2" style="border-top:1px solid #f2f3f8">
                <small class="text-muted">Menampilkan {{ $attendances->firstItem() }}–{{ $attendances->lastItem() }} dari {{ $attendances->total() }} data</small>
                {{ $attendances->links() }}
            </div>
            @endif
        </div>
    </div>

</div>

{{-- Modal Catat Kehadiran --}}
<div class="modal fade" id="modalCatatKehadiran" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:none;border-radius:16px">
            <div class="modal-header" style="background:linear-gradient(135deg,var(--secondary),#3d4268);border-radius:16px 16px 0 0">
                <h6 class="modal-title text-white font-weight-700 mb-0">
                    <i class="fas fa-calendar-plus mr-2"></i>Catat Kehadiran
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('attendances.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label style="font-size:.82rem;font-weight:600;color:#5a5f7d">Karyawan <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-control" required>
                            <option value="">Pilih karyawan...</option>
                            @foreach($employees ?? [] as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->nickname ?? $emp->name }} — {{ $emp->department ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label style="font-size:.82rem;font-weight:600;color:#5a5f7d">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label style="font-size:.82rem;font-weight:600;color:#5a5f7d">Jam Masuk</label>
                                <input type="time" name="check_in" class="form-control">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label style="font-size:.82rem;font-weight:600;color:#5a5f7d">Jam Keluar</label>
                                <input type="time" name="check_out" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label style="font-size:.82rem;font-weight:600;color:#5a5f7d">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="hadir">Hadir</option>
                            <option value="terlambat">Terlambat</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="absen">Tidak Hadir</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label style="font-size:.82rem;font-weight:600;color:#5a5f7d">Keterangan</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f1f8">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-apms btn-sm">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Select all
    $('#selectAll').change(function() {
        $('.row-check').prop('checked', $(this).prop('checked'));
    });

    // Filter by karyawan name
    $('#filterKaryawan').on('keyup', filterTable);
    $('#filterStatus').on('change', filterTable);
    $('#filterDept').on('change', filterTable);

    // Delete confirm
    $(document).on('click', '.btn-delete', function() {
        var id   = $(this).data('id');
        var name = $(this).data('name') || 'karyawan ini';
        Swal.fire({
            title: 'Hapus Kehadiran?',
            html: 'Data kehadiran <strong>' + name + '</strong> akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#E55A2B',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/attendances/' + id,
                    method: 'POST',
                    data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                    success: function() {
                        Swal.fire('Dihapus!', 'Data berhasil dihapus.', 'success')
                            .then(function() { location.reload(); });
                    },
                    error: function() {
                        Swal.fire('Gagal', 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    });

    // Load KPI counts
    loadKPI();
});

function filterTable() {
    var search = $('#filterKaryawan').val().toLowerCase();
    var status = $('#filterStatus').val().toLowerCase();
    var dept   = $('#filterDept').val().toLowerCase();
    $('#attendanceBody tr').each(function() {
        var name   = $(this).find('td:eq(1)').text().toLowerCase();
        var deptTd = $(this).find('td:eq(2)').text().toLowerCase();
        var stBadge = $(this).find('.badge-modern').text().toLowerCase();
        var show = (!search || name.includes(search))
                && (!status || stBadge.includes(status))
                && (!dept   || deptTd.includes(dept));
        $(this).toggle(show);
    });
}

function resetFilter() {
    $('#filterKaryawan').val('');
    $('#filterStatus').val('');
    $('#filterDept').val('');
    $('#filterTanggal').val('{{ date("Y-m-d") }}');
    $('#attendanceBody tr').show();
}

function loadKPI() {
    var hadir = 0, terlambat = 0, izinSakit = 0, absen = 0;
    $('#attendanceBody tr').each(function() {
        var badge = $(this).find('.badge-modern').text().trim().toLowerCase();
        if (badge.includes('hadir') && !badge.includes('terlambat')) hadir++;
        else if (badge.includes('terlambat')) terlambat++;
        else if (badge.includes('izin') || badge.includes('sakit')) izinSakit++;
        else if (badge.includes('absen')) absen++;
    });
    $('#kpiHadir').text(hadir);
    $('#kpiTerlambat').text(terlambat);
    $('#kpiIzinSakit').text(izinSakit);
    $('#kpiAbsen').text(absen);
}

function editKehadiran(id) {
    Swal.fire('Info', 'Fitur edit akan segera tersedia.', 'info');
}

function exportData() {
    Swal.fire({
        title: 'Export Kehadiran',
        html: '<p style="font-size:.9rem">Fitur export kehadiran belum tersedia. Hubungi administrator.</p>',
        icon: 'info',
        confirmButtonText: 'Tutup',
    });
}
</script>
@endpush
