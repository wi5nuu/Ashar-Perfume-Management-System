@extends('layouts.app')
@section('title', 'Backup Database - APMS')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="font-weight-bold mb-0"><i class="fas fa-database mr-2 text-primary"></i>Backup Database</h5>
            <small class="text-muted">Backup lengkap seluruh data sistem APMS</small>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible rounded-lg border-0 shadow-sm">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible rounded-lg border-0 shadow-sm">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            <button class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- 3 Kartu Format Backup --}}
    <div class="row mb-4">

        {{-- SQL --}}
        <div class="col-12 col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:16px; border-left: 4px solid #2563eb !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width:48px;height:48px;border-radius:12px;background:rgba(37,99,235,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-database" style="color:#2563eb;font-size:1.3rem;"></i>
                        </div>
                        <div class="ml-3">
                            <h6 class="font-weight-bold mb-0">SQL Dump</h6>
                            <small class="text-muted">Format standar MySQL</small>
                        </div>
                    </div>
                    <ul class="list-unstyled small text-muted mb-3">
                        <li><i class="fas fa-check text-success mr-1"></i> Seluruh struktur tabel + data</li>
                        <li><i class="fas fa-check text-success mr-1"></i> Stored procedures & routines</li>
                        <li><i class="fas fa-check text-success mr-1"></i> Bisa di-restore ke MySQL</li>
                        <li><i class="fas fa-check text-success mr-1"></i> Single transaction (konsisten)</li>
                    </ul>
                    <form id="form-backup-sql" action="{{ route('admin.monitoring.backup.create') }}" method="POST">
                        @csrf
                        <button type="submit" id="btn-backup-sql" class="btn btn-primary btn-block" style="border-radius:10px;">
                            <i class="fas fa-download mr-1"></i> Backup SQL
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- CSV --}}
        <div class="col-12 col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:16px; border-left: 4px solid #1a7a45 !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width:48px;height:48px;border-radius:12px;background:rgba(26,122,69,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-file-csv" style="color:#1a7a45;font-size:1.3rem;"></i>
                        </div>
                        <div class="ml-3">
                            <h6 class="font-weight-bold mb-0">CSV (ZIP)</h6>
                            <small class="text-muted">Satu file per tabel</small>
                        </div>
                    </div>
                    <ul class="list-unstyled small text-muted mb-3">
                        <li><i class="fas fa-check text-success mr-1"></i> Seluruh tabel dalam 1 ZIP</li>
                        <li><i class="fas fa-check text-success mr-1"></i> Bisa dibuka di Excel/Sheets</li>
                        <li><i class="fas fa-check text-success mr-1"></i> UTF-8 BOM (karakter aman)</li>
                        <li><i class="fas fa-check text-success mr-1"></i> Ringan & portabel</li>
                    </ul>
                    <form id="form-backup-csv" action="{{ route('admin.monitoring.backup.create-csv') }}" method="POST">
                        @csrf
                        <button type="submit" id="btn-backup-csv" class="btn btn-success btn-block"
                            style="border-radius:10px; background:#1a7a45; border-color:#1a7a45;">
                            <i class="fas fa-download mr-1"></i> Backup CSV
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- XLSX --}}
        <div class="col-12 col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:16px; border-left: 4px solid #d97706 !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width:48px;height:48px;border-radius:12px;background:rgba(217,119,6,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-file-excel" style="color:#d97706;font-size:1.3rem;"></i>
                        </div>
                        <div class="ml-3">
                            <h6 class="font-weight-bold mb-0">Excel XLSX</h6>
                            <small class="text-muted">Satu sheet per tabel</small>
                        </div>
                    </div>
                    <ul class="list-unstyled small text-muted mb-3">
                        <li><i class="fas fa-check text-success mr-1"></i> Seluruh tabel dalam 1 file</li>
                        <li><i class="fas fa-check text-success mr-1"></i> Header bold + warna</li>
                        <li><i class="fas fa-check text-success mr-1"></i> Freeze baris header</li>
                        <li><i class="fas fa-check text-success mr-1"></i> Auto-size kolom</li>
                    </ul>
                    <form id="form-backup-xlsx" action="{{ route('admin.monitoring.backup.create-xlsx') }}" method="POST">
                        @csrf
                        <button type="submit" id="btn-backup-xlsx" class="btn btn-warning btn-block"
                            style="border-radius:10px; color:#fff; background:#d97706; border-color:#d97706;">
                            <i class="fas fa-download mr-1"></i> Backup XLSX
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    {{-- Info banner --}}
    <div class="alert border rounded-lg shadow-sm mb-4" style="background:#f0f7ff; border-color:#bfdbfe !important; color:#1e3a5f;">
        <i class="fas fa-info-circle mr-2" style="color:#2563eb;"></i>
        <strong>Catatan:</strong> Backup CSV dan XLSX memerlukan waktu lebih lama tergantung jumlah data.
        Jangan tutup halaman ini saat proses berjalan. SQL Dump paling cepat dan direkomendasikan untuk restore.
    </div>

    {{-- Daftar Backup --}}
    <div class="card shadow-sm border-0" style="border-radius: 16px;">
        <div class="card-header border-0 bg-white py-3 px-4" style="border-radius:16px 16px 0 0;">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="font-weight-bold mb-0">
                    <i class="fas fa-history mr-2 text-secondary"></i>Riwayat Backup
                </h6>
                <span class="badge badge-secondary">{{ count($backups) }} file</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 pl-4">File</th>
                            <th class="border-0">Format</th>
                            <th class="border-0">Ukuran</th>
                            <th class="border-0">Tanggal</th>
                            <th class="border-0">Enkripsi</th>
                            <th class="border-0 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                            @php
                                $fname = $backup['filename'];
                                if (str_contains($fname, '-xlsx-'))      { $fmt = 'XLSX'; $fmtColor = 'warning'; $fmtIcon = 'fa-file-excel'; }
                                elseif (str_contains($fname, '-csv-'))   { $fmt = 'CSV';  $fmtColor = 'success'; $fmtIcon = 'fa-file-csv'; }
                                else                                      { $fmt = 'SQL';  $fmtColor = 'primary'; $fmtIcon = 'fa-database'; }
                                $sizeMb = round($backup['size'] / 1024 / 1024, 2);
                                $sizeKb = round($backup['size'] / 1024, 1);
                                $displaySize = $sizeMb >= 1 ? "{$sizeMb} MB" : "{$sizeKb} KB";
                            @endphp
                            <tr>
                                <td class="align-middle pl-4">
                                    <code style="font-size:0.8rem;">{{ $fname }}</code>
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-{{ $fmtColor }} px-2 py-1">
                                        <i class="fas {{ $fmtIcon }} mr-1"></i>{{ $fmt }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="text-muted small">{{ $displaySize }}</span>
                                </td>
                                <td class="align-middle">
                                    <span class="small">{{ $backup['created_at'] }}</span>
                                </td>
                                <td class="align-middle">
                                    @if($backup['is_encrypted'])
                                        <span class="badge badge-success px-2 py-1">
                                            <i class="fas fa-lock mr-1"></i>AES-256
                                        </span>
                                    @else
                                        <span class="badge badge-light text-muted px-2 py-1">
                                            <i class="fas fa-unlock mr-1"></i>Tidak
                                        </span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <a href="{{ route('admin.monitoring.backup.download', strtr(base64_encode($backup['filename']), '+/=', '-_~')) }}"
                                        class="btn btn-sm btn-outline-primary mr-1" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <form action="{{ route('admin.monitoring.backup.delete', strtr(base64_encode($backup['filename']), '+/=', '-_~')) }}"
                                        method="POST" class="d-inline delete-backup-form">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="display_name" value="{{ e($backup['filename']) }}">
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-backup" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fas fa-database fa-2x mb-3 d-block text-muted"></i>
                                    Belum ada backup. Klik salah satu tombol di atas untuk membuat backup pertama.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(count($backups) > 0)
        <div class="card-footer border-0 bg-white px-4 py-3" style="border-radius: 0 0 16px 16px;">
            <small class="text-muted">
                <i class="fas fa-info-circle mr-1"></i>
                Backup lama otomatis dihapus sesuai kebijakan retensi (7 harian, 4 mingguan, 3 bulanan).
            </small>
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Tombol Backup (SQL / CSV / XLSX) ──────────────────────────────────────
    // Pakai submit event di form agar tidak ada race condition dengan form.submit()
    var backupForms = [
        { formId: 'form-backup-sql',  btnId: 'btn-backup-sql',  label: 'Memproses SQL...' },
        { formId: 'form-backup-csv',  btnId: 'btn-backup-csv',  label: 'Memproses CSV...' },
        { formId: 'form-backup-xlsx', btnId: 'btn-backup-xlsx', label: 'Memproses XLSX...' },
    ];

    backupForms.forEach(function (item) {
        var form = document.getElementById(item.formId);
        var btn  = document.getElementById(item.btnId);
        if (!form || !btn) return;

        form.addEventListener('submit', function () {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>' + item.label;
        });
    });

    // ── Tombol Hapus Backup ───────────────────────────────────────────────────
    document.querySelectorAll('.btn-delete-backup').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var form = btn.closest('.delete-backup-form');
            var rawName = form.querySelector('input[name=display_name]').value;
            // Sanitasi untuk tampilan di HTML — hindari XSS di SweetAlert .html
            var safeName = document.createElement('span');
            safeName.textContent = rawName;
            var escapedName = safeName.innerHTML;

            Swal.fire({
                title: 'Hapus Backup?',
                html: 'File <code>' + escapedName + '</code> akan dihapus permanen.<br><small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

});
</script>
@endpush
