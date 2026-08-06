@extends('layouts.app')
@section('title', 'Pengaturan Sistem')

@push('styles')
<style>
/* ===== APMS Settings Page Styles ===== */
:root {
    --primary: #FF6B35;
    --primary-dark: #E55A2B;
    --secondary: #2D3047;
}

/* Page Header */
.apms-page-header { padding-bottom: 0.5rem; border-bottom: 2px solid #f1f3f6; }
.page-title { font-size: 1.5rem; font-weight: 700; color: var(--secondary); }
.page-subtitle { font-size: 0.82rem; color: #8a94a6; margin-top: 2px; margin-bottom: 0; }
.breadcrumb-apms { background: transparent; padding: 0; margin: 0; font-size: 0.78rem; }
.breadcrumb-apms .breadcrumb-item a { color: var(--primary); }
.breadcrumb-apms .breadcrumb-item.active { color: #8a94a6; }
.section-title { font-size: 0.95rem; font-weight: 700; color: var(--secondary); letter-spacing: 0.02em; }
.sys-stat-card { display:flex; align-items:center; background:#fff; border:1px solid #e9ecef; border-radius:10px; padding:10px 12px; box-shadow:0 1px 4px rgba(0,0,0,.04); }
.sys-stat-icon { width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:1rem; margin-right:10px; flex-shrink:0; }
.sys-stat-label { font-size:0.7rem; text-transform:uppercase; letter-spacing:.05em; color:#8a94a6; font-weight:600; }
.sys-stat-value { font-size:0.85rem; font-weight:700; color:var(--secondary); }
.setting-nav-card { display:block; background:#fff; border:1.5px solid #e9ecef; border-radius:14px; padding:16px 12px 12px; text-align:center; text-decoration:none; transition:all .2s ease; position:relative; overflow:hidden; cursor:pointer; }
.setting-nav-card:hover { border-color:var(--primary); box-shadow:0 4px 20px rgba(255,107,53,.15); transform:translateY(-2px); text-decoration:none; }
.setting-nav-card:hover .setting-nav-arrow { opacity:1; right:8px; }
.setting-nav-icon { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; margin:0 auto 10px; color:#fff; font-size:1.2rem; }
.setting-nav-title { font-size:0.82rem; font-weight:700; color:var(--secondary); margin-bottom:3px; }
.setting-nav-desc { font-size:0.7rem; color:#8a94a6; line-height:1.3; }
.setting-nav-arrow { position:absolute; bottom:8px; right:12px; color:var(--primary); font-size:0.65rem; opacity:0; transition:all .2s; }
.card-apms { border-radius:12px !important; }
.card-header-apms { padding:16px 20px; background:#fff; border-bottom:1px solid #f1f3f6; border-radius:12px 12px 0 0 !important; }
.card-header-icon { width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0; }
.card-header-title { font-size:0.9rem; font-weight:700; color:var(--secondary); }
.table-modern tbody tr { transition:background .15s; }
.table-modern tbody tr:hover { background:#fafbfc; }
.table-modern td { padding:11px 20px; font-size:0.85rem; border-color:#f1f3f6; vertical-align:middle; }
.badge-soft-primary { background:rgba(255,107,53,.1); color:var(--primary); border-radius:6px; padding:3px 8px; font-weight:600; }
.badge-soft-success { background:rgba(16,185,129,.1); color:#10B981; border-radius:6px; padding:3px 8px; font-weight:600; }
.badge-soft-warning { background:rgba(245,158,11,.1); color:#F59E0B; border-radius:6px; padding:3px 8px; font-weight:600; }
.badge-soft-danger  { background:rgba(239,68,68,.1); color:#EF4444; border-radius:6px; padding:3px 8px; font-weight:600; }
.badge-soft-info    { background:rgba(6,182,212,.1); color:#06B6D4; border-radius:6px; padding:3px 8px; font-weight:600; }
.badge-soft-secondary { background:rgba(107,114,128,.1); color:#6B7280; border-radius:6px; padding:3px 8px; font-weight:600; }
.badge-env          { font-size:0.7rem; padding:4px 10px; border-radius:20px; }
.bg-primary-soft  { background:rgba(255,107,53,.1); }
.bg-success-soft  { background:rgba(16,185,129,.1); }
.bg-danger-soft   { background:rgba(239,68,68,.1); }
.bg-warning-soft  { background:rgba(245,158,11,.1); }
.bg-info-soft     { background:rgba(6,182,212,.1); }
.backup-action-card { display:flex; gap:14px; align-items:flex-start; }
.backup-action-icon { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
.backup-action-body { flex:1; }
.custom-file-apms { position:relative; }
.custom-file-input-apms { position:absolute; opacity:0; width:0; height:0; }
.custom-file-label-apms { display:inline-flex; align-items:center; background:#f8f9fa; border:1.5px dashed #dee2e6; border-radius:8px; padding:7px 14px; font-size:0.82rem; color:#6B7280; cursor:pointer; transition:all .2s; width:100%; }
.custom-file-label-apms:hover { border-color:var(--primary); color:var(--primary); background:#fff9f7; }
.divider-apms { height:1px; background:linear-gradient(to right, transparent, #e9ecef, transparent); }
.font-weight-600 { font-weight:600 !important; }
.opacity-50 { opacity:.5; }
@media (max-width: 576px) {
    .setting-nav-card { padding:12px 8px 10px; }
    .setting-nav-icon { width:40px; height:40px; font-size:1rem; }
    .setting-nav-title { font-size:0.75rem; }
    .setting-nav-desc { font-size:0.65rem; }
    .backup-action-card { flex-direction:column; gap:8px; }
}
</style>
@endpush

@section('content')
<div class="container-fluid pt-3 pb-5">

    {{-- Page Header --}}
    <div class="apms-page-header mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-apms mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') ?? '#' }}"><i class="fas fa-home"></i></a></li>
                        <li class="breadcrumb-item active">Pengaturan Sistem</li>
                    </ol>
                </nav>
                <h3 class="page-title mb-0">Pengaturan Sistem</h3>
                <p class="page-subtitle">Konfigurasi, infrastruktur, dan manajemen data terpusat APMS</p>
            </div>
            <div class="d-flex align-items-center mt-2 mt-md-0">
                <span class="badge badge-env badge-{{ config('app.env') === 'production' ? 'danger' : 'warning' }} mr-2">
                    <i class="fas fa-circle mr-1" style="font-size:0.5rem;"></i>
                    {{ strtoupper(config('app.env')) }}
                </span>
                <span class="text-muted small">v{{ app()->version() }}</span>
            </div>
        </div>
    </div>

    {{-- System Status Bar --}}
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-2">
            <div class="sys-stat-card">
                <div class="sys-stat-icon bg-primary-soft"><i class="fas fa-code-branch text-primary"></i></div>
                <div class="sys-stat-body">
                    <div class="sys-stat-label">Laravel</div>
                    <div class="sys-stat-value">{{ app()->version() }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="sys-stat-card">
                <div class="sys-stat-icon bg-success-soft"><i class="fas fa-php text-success"></i></div>
                <div class="sys-stat-body">
                    <div class="sys-stat-label">PHP</div>
                    <div class="sys-stat-value">{{ PHP_VERSION }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="sys-stat-card">
                <div class="sys-stat-icon bg-info-soft"><i class="fas fa-database text-info"></i></div>
                <div class="sys-stat-body">
                    <div class="sys-stat-label">Database</div>
                    <div class="sys-stat-value text-uppercase">{{ config('database.default') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="sys-stat-card">
                <div class="sys-stat-icon bg-warning-soft"><i class="fas fa-clock text-warning"></i></div>
                <div class="sys-stat-body">
                    <div class="sys-stat-label">Timezone</div>
                    <div class="sys-stat-value" style="font-size:0.75rem;">{{ config('app.timezone') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Settings Navigation Grid --}}
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <h5 class="section-title"><i class="fas fa-sliders-h mr-2 text-primary"></i>Kategori Pengaturan</h5>
        </div>

        {{-- Informasi Toko --}}
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="{{ route('settings.index') }}" class="setting-nav-card">
                <div class="setting-nav-icon" style="background: linear-gradient(135deg,#FF6B35,#FF8B5C);">
                    <i class="fas fa-store-alt"></i>
                </div>
                <div class="setting-nav-title">Informasi Toko</div>
                <div class="setting-nav-desc">Nama, alamat, kontak, logo</div>
                <div class="setting-nav-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
        </div>

        {{-- Pengaturan Transaksi --}}
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="#" class="setting-nav-card">
                <div class="setting-nav-icon" style="background: linear-gradient(135deg,#2D3047,#3D4163);">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="setting-nav-title">Transaksi</div>
                <div class="setting-nav-desc">Pajak, diskon, metode bayar</div>
                <div class="setting-nav-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
        </div>

        {{-- Notifikasi --}}
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="{{ route('settings.index') }}" class="setting-nav-card">
                <div class="setting-nav-icon" style="background: linear-gradient(135deg,#F59E0B,#FBBF24);">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="setting-nav-title">Notifikasi</div>
                <div class="setting-nav-desc">Stok, email, alert otomatis</div>
                <div class="setting-nav-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
        </div>

        {{-- Backup & Restore --}}
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="#backup-section" class="setting-nav-card" onclick="document.getElementById('backup-section').scrollIntoView({behavior:'smooth'});return false;">
                <div class="setting-nav-icon" style="background: linear-gradient(135deg,#10B981,#34D399);">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="setting-nav-title">Backup & Restore</div>
                <div class="setting-nav-desc">Backup & pulihkan data</div>
                <div class="setting-nav-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
        </div>

        {{-- Tampilan & Tema --}}
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="#" class="setting-nav-card">
                <div class="setting-nav-icon" style="background: linear-gradient(135deg,#8B5CF6,#A78BFA);">
                    <i class="fas fa-palette"></i>
                </div>
                <div class="setting-nav-title">Tampilan</div>
                <div class="setting-nav-desc">Tema, warna, branding</div>
                <div class="setting-nav-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
        </div>

        {{-- Integrasi --}}
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="#" class="setting-nav-card">
                <div class="setting-nav-icon" style="background: linear-gradient(135deg,#06B6D4,#22D3EE);">
                    <i class="fas fa-plug"></i>
                </div>
                <div class="setting-nav-title">Integrasi</div>
                <div class="setting-nav-desc">QRIS, printer, API</div>
                <div class="setting-nav-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
        </div>
    </div>

    {{-- Main Content Row --}}
    <div class="row">

        {{-- Infrastructure Info --}}
        <div class="col-lg-6 mb-4">
            <div class="card card-apms border-0 shadow-sm h-100">
                <div class="card-header-apms">
                    <div class="d-flex align-items-center">
                        <div class="card-header-icon bg-primary-soft mr-3">
                            <i class="fas fa-server text-primary"></i>
                        </div>
                        <div>
                            <h6 class="card-header-title mb-0">Informasi Infrastruktur</h6>
                            <small class="text-muted">Status sistem & environment saat ini</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-modern mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted" width="45%"><i class="fas fa-tag mr-2 text-primary opacity-50"></i>Nama Aplikasi</td>
                                <td class="font-weight-600 text-dark">APMS - Asghar Grosir Perfume</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-code-branch mr-2 text-primary opacity-50"></i>Versi Laravel</td>
                                <td><span class="badge badge-soft-primary">{{ app()->version() }}</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-leaf mr-2 text-primary opacity-50"></i>Environment</td>
                                <td>
                                    <span class="badge badge-soft-{{ config('app.env') === 'production' ? 'success' : 'warning' }}">
                                        {{ strtoupper(config('app.env')) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-database mr-2 text-primary opacity-50"></i>Database Driver</td>
                                <td><span class="badge badge-soft-info text-uppercase">{{ config('database.default') }}</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fab fa-php mr-2 text-primary opacity-50"></i>PHP Version</td>
                                <td><span class="badge badge-soft-secondary">{{ PHP_VERSION }}</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-clock mr-2 text-primary opacity-50"></i>Server Time</td>
                                <td class="font-weight-600" id="server-time">{{ now()->format('d M Y, H:i:s') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Backup & Restore --}}
        <div class="col-lg-6 mb-4" id="backup-section">
            <div class="card card-apms border-0 shadow-sm h-100">
                <div class="card-header-apms">
                    <div class="d-flex align-items-center">
                        <div class="card-header-icon bg-success-soft mr-3">
                            <i class="fas fa-database text-success"></i>
                        </div>
                        <div>
                            <h6 class="card-header-title mb-0">Backup & Restore Data</h6>
                            <small class="text-muted">Amankan dan pulihkan seluruh data sistem</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    {{-- Backup --}}
                    <div class="backup-action-card mb-3">
                        <div class="backup-action-icon bg-success-soft">
                            <i class="fas fa-cloud-download-alt text-success"></i>
                        </div>
                        <div class="backup-action-body">
                            <div class="font-weight-600 text-dark mb-1">Backup Database</div>
                            <div class="text-muted small mb-3">Unduh salinan lengkap seluruh data transaksi, inventaris, dan konfigurasi sistem.</div>
                            <form method="POST" action="{{ route('settings.backup') }}" id="form-backup">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm px-4" id="btn-backup">
                                    <i class="fas fa-download mr-2"></i>Unduh Backup Sekarang
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="divider-apms my-3"></div>

                    {{-- Restore --}}
                    <div class="backup-action-card">
                        <div class="backup-action-icon bg-danger-soft">
                            <i class="fas fa-cloud-upload-alt text-danger"></i>
                        </div>
                        <div class="backup-action-body">
                            <div class="font-weight-600 text-dark mb-1">Restore Database</div>
                            <div class="text-muted small mb-3">
                                <i class="fas fa-exclamation-triangle text-warning mr-1"></i>
                                Hati-hati: restore akan menimpa data yang ada. Pastikan Anda sudah backup terbaru.
                            </div>
                            <form method="POST" action="{{ route('settings.restore') }}" enctype="multipart/form-data" id="form-restore">
                                @csrf
                                <div class="custom-file-apms mb-2">
                                    <input type="file" name="backup_file" class="custom-file-input-apms" id="backupFile" accept=".sql,.txt">
                                    <label class="custom-file-label-apms" for="backupFile">
                                        <i class="fas fa-folder-open mr-2"></i>
                                        <span id="file-chosen">Pilih file backup (.sql)...</span>
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm px-4" id="btn-restore">
                                    <i class="fas fa-upload mr-2"></i>Jalankan Restore
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>{{-- end row --}}

</div>

@endsection

@push('scripts')
<script>
$(function () {

    // Live clock update
    function updateClock() {
        var now = new Date();
        var pad = n => String(n).padStart(2,'0');
        var months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
        var str = pad(now.getDate()) + ' ' + months[now.getMonth()] + ' ' + now.getFullYear()
                  + ', ' + pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
        $('#server-time').text(str);
    }
    setInterval(updateClock, 1000);

    // Custom file input label
    $('#backupFile').on('change', function () {
        var name = this.files[0] ? this.files[0].name : 'Pilih file backup (.sql)...';
        $('#file-chosen').text(name);
    });

    // Backup confirm
    $('#form-backup').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#btn-backup');
        Swal.fire({
            title: 'Backup Database?',
            text: 'Sistem akan mengunduh file backup database sekarang.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#FF6B35',
            cancelButtonColor: '#6B7280',
            confirmButtonText: '<i class="fas fa-download mr-1"></i> Ya, Backup',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                $btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...').prop('disabled', true);
                this.submit();
            }
        });
    });

    // Restore confirm
    $('#form-restore').on('submit', function (e) {
        e.preventDefault();
        var file = $('#backupFile')[0].files[0];
        if (!file) {
            Swal.fire({ icon:'warning', title:'File Belum Dipilih', text:'Pilih file backup (.sql) terlebih dahulu.', confirmButtonColor:'#FF6B35' });
            return;
        }
        var $btn = $('#btn-restore');
        Swal.fire({
            title: 'Restore Database?',
            html: '<div class="text-danger font-weight-bold mb-2"><i class="fas fa-exclamation-triangle mr-1"></i>PERHATIAN!</div>'
                + 'Proses ini akan <strong>menimpa semua data yang ada</strong>.<br>'
                + 'Pastikan file backup valid dan Anda telah membuat backup terbaru.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: '<i class="fas fa-upload mr-1"></i> Lanjutkan Restore',
            cancelButtonText: 'Batalkan'
        }).then(result => {
            if (result.isConfirmed) {
                $btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Memulihkan...').prop('disabled', true);
                this.submit();
            }
        });
    });

    // Success/Error alerts from session
    @if(session('success'))
        Swal.fire({ icon:'success', title:'Berhasil!', text: @json(session('success')), confirmButtonColor:'#FF6B35', timer:3000, timerProgressBar:true });
    @endif
    @if(session('error'))
        Swal.fire({ icon:'error', title:'Gagal', text: @json(session('error')), confirmButtonColor:'#FF6B35' });
    @endif
});
</script>
@endpush
