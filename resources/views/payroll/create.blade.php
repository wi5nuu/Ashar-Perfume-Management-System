@extends('layouts.app')
@section('title', 'Proses Gaji Baru')

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
.card-apms { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(45,48,71,.07); }
.form-section { background: #fff; border-radius: 12px; padding: 1.5rem; margin-bottom: 1rem; border: 1px solid #f0f1f8; }
.section-heading {
    font-size: .78rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .07em; color: var(--primary); margin-bottom: 1.1rem;
    padding-bottom: .55rem; border-bottom: 2px solid rgba(255,107,53,.15);
    display: flex; align-items: center; gap: .5rem;
}
.form-control { border-radius: 8px; border: 1.5px solid #e0e3ef; font-size: .88rem; padding: .55rem .9rem; }
.form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,.12); outline: none; }
label { font-size: .82rem; font-weight: 600; color: #5a5f7d; margin-bottom: .35rem; display: block; }
.btn-primary-apms {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border: none; color: #fff; border-radius: 8px;
    font-weight: 600; font-size: .88rem; padding: .6rem 1.4rem;
    transition: all .2s; box-shadow: 0 3px 10px rgba(255,107,53,.25);
}
.btn-primary-apms:hover { background: linear-gradient(135deg, var(--primary-dark), #c94d22); color: #fff; transform: translateY(-1px); }
.table-modern { border-collapse: separate; border-spacing: 0; width: 100%; }
.table-modern thead th {
    background: #f8f9fc; color: #5a5f7d; font-size: .75rem;
    font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
    padding: .75rem 1rem; border-bottom: 2px solid #eef0f7; border-top: none;
}
.table-modern tbody td { padding: .75rem 1rem; border-bottom: 1px solid #f2f3f8; vertical-align: middle; color: #3d4268; font-size: .86rem; }
.table-modern tbody tr:last-child td { border-bottom: none; }
.table-modern tfoot td { padding: .75rem 1rem; background: #f8f9fc; font-weight: 700; border-top: 2px solid #eef0f7; }
.step-indicator { display: flex; align-items: center; gap: 0; margin-bottom: 2rem; }
.step { display: flex; flex-direction: column; align-items: center; flex: 1; position: relative; }
.step::after { content: ''; position: absolute; top: 16px; left: 50%; width: 100%; height: 2px; background: #e0e3ef; z-index: 0; }
.step:last-child::after { display: none; }
.step-circle {
    width: 32px; height: 32px; border-radius: 50%; z-index: 1;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; font-weight: 700; border: 2px solid #e0e3ef;
    background: #fff; color: #8a8fa8; position: relative;
}
.step.active .step-circle { border-color: var(--primary); background: var(--primary); color: #fff; }
.step.done .step-circle { border-color: #1a7a45; background: #1a7a45; color: #fff; }
.step.active::after { background: linear-gradient(to right, var(--primary), #e0e3ef); }
.step.done::after { background: #1a7a45; }
.step-label { font-size: .72rem; font-weight: 600; color: #8a8fa8; margin-top: .4rem; text-align: center; }
.step.active .step-label { color: var(--primary); }
.step.done .step-label { color: #1a7a45; }
.preview-summary {
    background: linear-gradient(135deg, rgba(255,107,53,.06), rgba(45,48,71,.04));
    border: 1.5px solid rgba(255,107,53,.2); border-radius: 12px; padding: 1.25rem;
}
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
                        <li class="breadcrumb-item"><i class="fas fa-home mr-1"></i> Dashboard</li>
                        <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Penggajian</a></li>
                        <li class="breadcrumb-item active">Proses Gaji Baru</li>
                    </ol>
                </nav>
                <h4 class="mb-0 font-weight-bold" style="font-size:1.35rem;">
                    <i class="fas fa-calculator mr-2" style="color:var(--primary);"></i>Proses Gaji Baru
                </h4>
                <p class="mb-0 mt-1" style="color:rgba(255,255,255,.6);font-size:.82rem;">Pilih periode, departemen, lalu konfirmasi sebelum memproses</p>
            </div>
            <a href="{{ route('payroll.index') }}" class="btn"
               style="border:1.5px solid rgba(255,255,255,.4);color:#fff;border-radius:8px;font-size:.85rem;font-weight:600;padding:.5rem 1.1rem;">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Step Indicator --}}
    <div class="step-indicator px-4 mb-4">
        <div class="step active" id="step1-ind">
            <div class="step-circle">1</div>
            <div class="step-label">Pilih Periode</div>
        </div>
        <div class="step" id="step2-ind">
            <div class="step-circle">2</div>
            <div class="step-label">Preview Gaji</div>
        </div>
        <div class="step" id="step3-ind">
            <div class="step-circle">3</div>
            <div class="step-label">Konfirmasi</div>
        </div>
    </div>

    <form action="{{ route('payroll.generate') }}" method="POST" id="payrollForm">
        @csrf

        {{-- Step 1: Pilih Periode --}}
        <div id="step1">
            <div class="form-section">
                <div class="section-heading"><i class="fas fa-calendar-alt"></i> Pilih Periode Penggajian</div>
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>Bulan <span style="color:#dc3545;">*</span></label>
                        <select name="bulan" id="inputBulan" class="form-control" required>
                            @for($m=1; $m<=12; $m++)
                            <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0,0,0,$m,1)) }}
                            </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Tahun <span style="color:#dc3545;">*</span></label>
                        <select name="tahun" id="inputTahun" class="form-control" required>
                            @for($y=date('Y'); $y>=date('Y')-3; $y--)
                            <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Departemen</label>
                        <select name="department" class="form-control">
                            <option value="">Semua Departemen</option>
                            <option value="operations">Operasional</option>
                            <option value="sales">Penjualan</option>
                            <option value="warehouse">Gudang</option>
                            <option value="finance">Keuangan</option>
                            <option value="hr">SDM</option>
                            <option value="delivery">Pengiriman</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Tipe Karyawan</label>
                        <select name="employee_type" class="form-control">
                            <option value="">Semua Tipe</option>
                            <option value="login">Akses Login</option>
                            <option value="store">Karyawan Toko</option>
                        </select>
                    </div>
                </div>

                {{-- Hidden field required by existing route --}}
                <input type="hidden" name="month" id="hiddenMonth" value="{{ $month ?? date('Y-m') }}">

                <div class="d-flex justify-content-end mt-2">
                    <button type="button" class="btn btn-primary-apms" onclick="goToStep2()">
                        <i class="fas fa-arrow-right mr-1"></i> Preview Gaji
                    </button>
                </div>
            </div>
        </div>

        {{-- Step 2: Preview --}}
        <div id="step2" style="display:none;">
            <div class="card-apms mb-4">
                <div class="card-header bg-white px-4 py-3 d-flex align-items-center justify-content-between" style="border-bottom:1px solid #f2f3f8;">
                    <div>
                        <h6 class="mb-0 font-weight-bold" style="color:var(--secondary);">
                            <i class="fas fa-table mr-1" style="color:var(--primary);"></i> Preview Kalkulasi Gaji
                        </h6>
                        <small class="text-muted" id="previewPeriodLabel">— pilih periode —</small>
                    </div>
                    <span class="badge" style="background:rgba(255,107,53,.1);color:var(--primary);border-radius:20px;font-size:.78rem;font-weight:700;padding:.35em .85em;" id="previewCount">
                        0 Karyawan
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-modern w-100" id="previewTable">
                            <thead>
                                <tr>
                                    <th style="padding-left:1.25rem;">Karyawan</th>
                                    <th>Jabatan</th>
                                    <th>Gaji Pokok</th>
                                    <th>Tunjangan</th>
                                    <th>Potongan</th>
                                    <th style="color:var(--primary);">Gaji Bersih</th>
                                </tr>
                            </thead>
                            <tbody id="previewBody">
                                @foreach($employees as $e)
                                @php
                                    $allowance  = $e->payrollSettings?->allowance ?? 0;
                                    $deduction  = $e->payrollSettings?->deduction ?? 0;
                                    $bersih     = ($e->basic_salary ?? 0) + $allowance - $deduction;
                                    $colors     = ['#FF6B35','#4e73df','#1cc88a','#6f42c1','#e83e8c'];
                                    $color      = $colors[crc32($e->name) % count($colors)];
                                    $initials   = strtoupper(mb_substr($e->nickname ?? $e->name, 0, 1))
                                               . strtoupper(mb_substr(explode(' ',$e->name)[1] ?? '',0,1));
                                @endphp
                                <tr>
                                    <td style="padding-left:1.25rem;">
                                        <div class="d-flex align-items-center" style="gap:.6rem;">
                                            <div style="width:32px;height:32px;border-radius:8px;background:{{ $color }};display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.78rem;color:#fff;flex-shrink:0;">
                                                {{ $initials }}
                                            </div>
                                            <span class="font-weight-bold" style="font-size:.87rem;">{{ $e->nickname ?? $e->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($e->role)
                                        <span style="font-size:.78rem;background:#f3e8ff;color:#6f42c1;padding:.2em .6em;border-radius:20px;font-weight:600;">{{ ucfirst($e->role) }}</span>
                                        @else <span style="color:#c0c0c0;">—</span> @endif
                                    </td>
                                    <td style="font-size:.85rem;">Rp {{ number_format($e->basic_salary ?? 0, 0, ',', '.') }}</td>
                                    <td style="font-size:.85rem;color:#0c7abf;">Rp {{ number_format($allowance, 0, ',', '.') }}</td>
                                    <td style="font-size:.85rem;color:#c0392b;">Rp {{ number_format($deduction, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="font-weight-bold" style="color:#1a7a45;font-size:.88rem;">
                                            Rp {{ number_format($bersih, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" style="padding-left:1.25rem;">TOTAL</td>
                                    <td>Rp {{ number_format($employees->sum('basic_salary'), 0, ',', '.') }}</td>
                                    <td style="color:#0c7abf;">Rp {{ number_format($employees->sum(fn($e) => $e->payrollSettings?->allowance ?? 0), 0, ',', '.') }}</td>
                                    <td style="color:#c0392b;">Rp {{ number_format($employees->sum(fn($e) => $e->payrollSettings?->deduction ?? 0), 0, ',', '.') }}</td>
                                    <td style="color:#1a7a45;">
                                        Rp {{ number_format($employees->sum(fn($e) => ($e->basic_salary ?? 0) + ($e->payrollSettings?->allowance ?? 0) - ($e->payrollSettings?->deduction ?? 0)), 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-2">
                <button type="button" class="btn btn-light" onclick="goToStep1()"
                    style="border-radius:8px;font-weight:600;padding:.6rem 1.4rem;border:1.5px solid #e0e3ef;">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </button>
                <button type="button" class="btn btn-primary-apms" onclick="goToStep3()">
                    <i class="fas fa-check mr-1"></i> Lanjut ke Konfirmasi
                </button>
            </div>
        </div>

        {{-- Step 3: Konfirmasi --}}
        <div id="step3" style="display:none;">
            <div class="form-section">
                <div class="section-heading"><i class="fas fa-shield-alt"></i> Konfirmasi Proses Penggajian</div>

                <div class="preview-summary mb-4">
                    <div class="row text-center" style="row-gap:.75rem;">
                        <div class="col-6 col-md-3">
                            <div style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#8a8fa8;">Periode</div>
                            <div class="font-weight-bold" style="color:var(--secondary);" id="confirmPeriod">—</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#8a8fa8;">Total Karyawan</div>
                            <div class="font-weight-bold" style="color:var(--secondary);" id="confirmCount">—</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#8a8fa8;">Total Gaji Bersih</div>
                            <div class="font-weight-bold" style="color:#1a7a45;" id="confirmTotal">—</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#8a8fa8;">Departemen</div>
                            <div class="font-weight-bold" style="color:var(--secondary);" id="confirmDept">Semua</div>
                        </div>
                    </div>
                </div>

                <div class="alert border-0 rounded-lg" style="background:#fff3e0;border-left:4px solid #e65100!important;">
                    <div class="d-flex align-items-start" style="gap:.75rem;">
                        <i class="fas fa-exclamation-triangle mt-1" style="color:#e65100;"></i>
                        <div>
                            <strong style="color:#bf360c;font-size:.88rem;">Perhatian sebelum memproses:</strong>
                            <ul class="mb-0 mt-1 pl-3" style="font-size:.83rem;color:#5d3a00;">
                                <li>Proses ini akan membuat record gaji untuk semua karyawan di periode yang dipilih.</li>
                                <li>Jika sudah ada record untuk periode yang sama, data akan di-<em>overwrite</em>.</li>
                                <li>Pastikan data absensi sudah lengkap sebelum memproses.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-light" onclick="goToStep2()"
                        style="border-radius:8px;font-weight:600;padding:.6rem 1.4rem;border:1.5px solid #e0e3ef;">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </button>
                    <button type="submit" class="btn btn-primary-apms" id="submitBtn"
                        onclick="this.disabled=true;this.innerHTML='<i class=\'fas fa-spinner fa-spin mr-1\'></i> Memproses...';">
                        <i class="fas fa-play mr-1"></i> Proses Sekarang
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
var monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
var totalBersih = '{{ number_format($employees->sum(fn($e) => ($e->basic_salary ?? 0) + ($e->payrollSettings?->allowance ?? 0) - ($e->payrollSettings?->deduction ?? 0)), 0, ",", ".") }}';
var totalKaryawan = {{ $employees->count() }};

function setStep(n) {
    [1,2,3].forEach(function(i) {
        document.getElementById('step'+i).style.display = i === n ? 'block' : 'none';
        var ind = document.getElementById('step'+i+'-ind');
        ind.className = 'step' + (i < n ? ' done' : (i === n ? ' active' : ''));
    });
}

function goToStep2() {
    var bulan = document.getElementById('inputBulan').value;
    var tahun = document.getElementById('inputTahun').value;
    var label = monthNames[parseInt(bulan)-1] + ' ' + tahun;
    document.getElementById('previewPeriodLabel').textContent = label;
    document.getElementById('previewCount').textContent = totalKaryawan + ' Karyawan';
    document.getElementById('hiddenMonth').value = tahun + '-' + bulan.toString().padStart(2,'0');
    setStep(2);
}

function goToStep1() { setStep(1); }

function goToStep3() {
    var bulan = document.getElementById('inputBulan').value;
    var tahun = document.getElementById('inputTahun').value;
    var dept  = document.querySelector('[name="department"]').value;
    document.getElementById('confirmPeriod').textContent = monthNames[parseInt(bulan)-1] + ' ' + tahun;
    document.getElementById('confirmCount').textContent  = totalKaryawan + ' karyawan';
    document.getElementById('confirmTotal').textContent  = 'Rp ' + totalBersih;
    document.getElementById('confirmDept').textContent   = dept || 'Semua Departemen';
    setStep(3);
}
</script>
@endpush
