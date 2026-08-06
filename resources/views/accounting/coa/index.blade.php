@extends('layouts.app')

@section('title', 'Chart of Accounts')

@push('styles')
<style>
    :root { --primary: #FF6B35; --primary-dark: #E55A2B; --secondary: #2D3047; }
    .page-header-apms { background: linear-gradient(135deg, var(--secondary) 0%, #3d4266 100%); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; }
    .page-header-apms h1 { font-size: 1.5rem; font-weight: 700; margin: 0 0 4px; color: #fff; }
    .page-header-apms .breadcrumb { background: transparent; margin: 0; padding: 0; font-size: 0.8rem; }
    .page-header-apms .breadcrumb-item a { color: rgba(255,255,255,0.7); text-decoration: none; }
    .page-header-apms .breadcrumb-item.active { color: rgba(255,255,255,0.5); }
    .page-header-apms .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.4); }
    .btn-primary-apms { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff !important; border: none; border-radius: 8px; font-weight: 600; font-size: 0.85rem; padding: 0.5rem 1.1rem; box-shadow: 0 3px 10px rgba(255,107,53,0.3); transition: transform 0.15s, box-shadow 0.15s; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; text-decoration: none; }
    .btn-primary-apms:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(255,107,53,0.4); color: #fff !important; text-decoration: none; }
    .filter-card { background: #fff; border-radius: 14px; border: 1px solid #eef0f8; box-shadow: 0 2px 8px rgba(45,48,71,0.05); padding: 1.25rem 1.5rem; margin-bottom: 1.25rem; }
    .filter-card .form-control { border-radius: 8px; border: 1.5px solid #e4e8f0; font-size: 0.85rem; color: var(--secondary); }
    .filter-card .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,0.1); }
    .table-card { background: #fff; border-radius: 14px; border: 1px solid #eef0f8; box-shadow: 0 2px 12px rgba(45,48,71,0.07); overflow: hidden; }
    .table-card .table { margin: 0; font-size: 0.85rem; color: var(--secondary); }
    .table-card .table thead th { background: #f8f9ff; border-bottom: 2px solid #eef0f8; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #8892a4; padding: 0.85rem 1rem; border-top: none; white-space: nowrap; }
    .table-card .table tbody tr { border-bottom: 1px solid #f5f6fb; transition: background 0.15s; }
    .table-card .table tbody tr:last-child { border-bottom: none; }
    .table-card .table tbody tr:hover { background: #fafbff; }
    .table-card .table tbody td { padding: 0.85rem 1rem; vertical-align: middle; border-top: none; }
    .type-group-header { background: linear-gradient(135deg, #f8f9ff, #f0f2ff); }
    .type-group-header td { padding: 0.6rem 1rem; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: var(--secondary); border-bottom: 1px solid #eef0f8 !important; }
    .acct-code-badge { font-size: 0.75rem; font-weight: 700; background: #f0f2f8; color: #667; padding: 2px 8px; border-radius: 5px; font-family: monospace; white-space: nowrap; }
    .acct-name-indent-0 { padding-left: 0; font-weight: 700; color: var(--secondary); }
    .acct-name-indent-1 { padding-left: 1.2rem; }
    .acct-name-indent-2 { padding-left: 2.4rem; }
    .acct-name-indent-3 { padding-left: 3.6rem; }
    .acct-name-indent-0::before { content: ''; }
    .acct-name-indent-1::before { content: '+ '; color: #c0c8d8; font-weight: 400; }
    .acct-name-indent-2::before { content: '  + '; color: #c0c8d8; font-weight: 400; }
    .acct-name-indent-3::before { content: '    + '; color: #c0c8d8; font-weight: 400; }
    .badge-normal-debit  { background: #e3f2fd; color: #1565c0; font-size: 0.7rem; font-weight: 700; padding: 2px 8px; border-radius: 5px; }
    .badge-normal-kredit { background: #e8f5e9; color: #2e7d32; font-size: 0.7rem; font-weight: 700; padding: 2px 8px; border-radius: 5px; }
    .badge-active   { background: #e8f5e9; color: #2e7d32; font-size: 0.7rem; font-weight: 600; padding: 3px 9px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px; }
    .badge-inactive { background: #fce4ec; color: #880e4f; font-size: 0.7rem; font-weight: 600; padding: 3px 9px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px; }
    .badge-active::before   { content: ''; width: 5px; height: 5px; border-radius: 50%; background: #43a047; }
    .badge-inactive::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: #ef5350; }
    .action-btn { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; border: 1.5px solid; transition: all 0.15s; text-decoration: none; cursor: pointer; background: none; }
    .action-btn.edit { border-color: #1976d2; color: #1976d2; }
    .action-btn.edit:hover { background: #1976d2; color: #fff; text-decoration: none; }
    .action-btn.sub { border-color: var(--primary); color: var(--primary); }
    .action-btn.sub:hover { background: var(--primary); color: #fff; text-decoration: none; }
    .action-btn.toggle { border-color: #8892a4; color: #8892a4; }
    .action-btn.toggle:hover { background: #8892a4; color: #fff; text-decoration: none; }
    .type-chip { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 6px; font-size: 0.72rem; font-weight: 600; white-space: nowrap; }
    .type-chip.asset     { background: #e3f2fd; color: #1565c0; }
    .type-chip.liability { background: #fff3e0; color: #e65100; }
    .type-chip.equity    { background: #f3e5f5; color: #6a1b9a; }
    .type-chip.revenue   { background: #e8f5e9; color: #2e7d32; }
    .type-chip.expense   { background: #fce4ec; color: #880e4f; }
    .empty-state { padding: 3rem 1rem; text-align: center; }
    .empty-state .empty-icon { width: 64px; height: 64px; background: #f5f6fb; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem; color: #c0c8d8; }
    .empty-state h6 { font-weight: 600; color: var(--secondary); margin-bottom: 4px; }
    .empty-state p { font-size: 0.83rem; color: #8892a4; margin: 0; }
    .modal-apms .modal-content { border: none; border-radius: 16px; box-shadow: 0 16px 48px rgba(45,48,71,0.2); }
    .modal-apms .modal-header { border-bottom: 1px solid #f0f2f8; padding: 1.1rem 1.5rem; }
    .modal-apms .modal-body { padding: 1.5rem; }
    .modal-apms .modal-footer { border-top: 1px solid #f0f2f8; padding: 1rem 1.5rem; }
    .form-label-sm { font-size: 0.75rem; font-weight: 600; color: #8892a4; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 5px; display: block; }
</style>

@endpush

@section('content')
<div class="container-fluid pb-4">

    {{-- Page Header --}}
    <div class="page-header-apms">
        <div>
            <h1><i class="fas fa-sitemap mr-2" style="color:var(--primary)"></i>Bagan Akun (Chart of Accounts)</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Akuntansi</a></li>
                    <li class="breadcrumb-item active">Bagan Akun</li>
                </ol>
            </nav>
        </div>
        <button class="btn-primary-apms" data-toggle="modal" data-target="#coaModal">
            <i class="fas fa-plus-circle"></i> Tambah Akun Baru
        </button>
    </div>

    {{-- Session Messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;font-size:0.85rem;border:none;background:#e8f5e9;color:#2e7d32;margin-bottom:1.25rem">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    {{-- Filter --}}
    <div class="filter-card">
        <form method="GET">
            <div class="row align-items-end" style="row-gap:0.75rem">
                <div class="col-md-3">
                    <label class="form-label-sm">Tipe Akun</label>
                    <select name="type" class="form-control" onchange="this.form.submit()">
                        <option value="">Semua Tipe</option>
                        @foreach(App\Models\ChartOfAccount::TYPES as $k => $v)
                        <option value="{{ $k }}" {{ request('type') == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label-sm">Cari Akun</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="background:#f8f9ff;border-color:#e4e8f0;border-radius:8px 0 0 8px;border-right:none;">
                                <i class="fas fa-search" style="color:#b0b8c9;font-size:0.8rem"></i>
                            </span>
                        </div>
                        <input type="text" name="search" class="form-control" placeholder="Kode atau nama akun..." value="{{ request('search') }}" style="border-left:none;border-radius:0 8px 8px 0;">
                    </div>
                </div>
                <div class="col-md-3 d-flex" style="gap:0.5rem">
                    <button type="submit" class="btn flex-fill" style="background:var(--primary);color:#fff;border:none;border-radius:8px;font-weight:600;font-size:0.85rem;height:calc(1.5em + 0.7rem + 4px)">
                        <i class="fas fa-filter mr-1"></i>Filter
                    </button>
                    @if(request()->hasAny(['type','search']))
                    <a href="{{ route('accounting.coa.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;height:calc(1.5em + 0.7rem + 4px);padding:0 0.75rem" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- COA Table --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:120px">Kode</th>
                        <th>Nama Akun</th>
                        <th>Tipe</th>
                        <th class="text-center">Saldo Normal</th>
                        <th class="text-center">Level</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width:160px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $typeIcons = ['asset'=>'fa-landmark','liability'=>'fa-hand-holding-usd','equity'=>'fa-coins','revenue'=>'fa-chart-line','expense'=>'fa-receipt'];
                        $typeColors = ['asset'=>'asset','liability'=>'liability','equity'=>'equity','revenue'=>'revenue','expense'=>'expense'];
                        $currentType = null;
                    @endphp
                    @forelse($accounts as $acc)
                    @php
                        $accTypeName = App\Models\ChartOfAccount::TYPES[$acc->type] ?? $acc->type;
                    @endphp
                    @if($currentType !== $acc->type)
                    @php $currentType = $acc->type; @endphp
                    <tr class="type-group-header">
                        <td colspan="7">
                            <i class="fas {{ $typeIcons[$acc->type] ?? 'fa-tag' }} mr-2" style="color:var(--primary)"></i>
                            {{ $accTypeName }}
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td>
                            <span class="acct-code-badge">{{ $acc->code }}</span>
                        </td>
                        <td>
                            <span class="acct-name-indent-{{ min($acc->level - 1, 3) }}">
                                {{ $acc->name }}
                            </span>
                            @if($acc->description)
                            <div style="font-size:0.72rem;color:#b0b8c9;margin-top:2px;padding-left:{{ min(($acc->level-1)*1.2, 3.6) }}rem">{{ Str::limit($acc->description, 60) }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="type-chip {{ $typeColors[$acc->type] ?? '' }}">
                                <i class="fas {{ $typeIcons[$acc->type] ?? 'fa-tag' }}"></i>
                                {{ $accTypeName }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if(strtolower($acc->normal_balance) === 'debit')
                                <span class="badge-normal-debit">Debit</span>
                            @else
                                <span class="badge-normal-kredit">Kredit</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span style="font-size:0.82rem;font-weight:600;color:#8892a4">L{{ $acc->level }}</span>
                        </td>
                        <td class="text-center">
                            @if($acc->is_active)
                                <span class="badge-active">Aktif</span>
                            @else
                                <span class="badge-inactive">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center" style="gap:4px">
                                <button class="action-btn sub" title="Tambah Sub-Akun"
                                    onclick="setParent({{ $acc->id }}, '{{ $acc->code }}', '{{ addslashes($acc->name) }}')"
                                    data-toggle="modal" data-target="#coaModal">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <a href="{{ route('accounting.coa.edit', $acc->id) }}" class="action-btn edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-sitemap"></i></div>
                                <h6>Belum Ada Akun</h6>
                                <p>Tambahkan akun pertama untuk memulai bagan akun Anda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($accounts->hasPages())
        <div class="px-4 py-3" style="border-top:1px solid #f0f2f8">
            {{ $accounts->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>

{{-- Add Account Modal --}}
<div class="modal fade modal-apms" id="coaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('accounting.coa.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-plus-circle mr-2" style="color:var(--primary)"></i>
                    <span id="modalTitle">Tambah Akun Baru</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" onclick="resetModal()">&times;</button>
            </div>
            <div class="modal-body">
                {{-- Parent indicator --}}
                <div id="parentIndicator" class="d-none mb-3 p-2" style="background:#fff3e0;border-radius:8px;border:1px solid #ffe082;font-size:0.82rem;color:#e65100">
                    <i class="fas fa-sitemap mr-2"></i>
                    Sub-akun dari: <strong id="parentLabel"></strong>
                </div>
                <input type="hidden" name="parent_id" id="parentIdInput" value="">

                <div class="row" style="row-gap:0.75rem">
                    <div class="col-md-5">
                        <label class="form-label-sm">Kode Akun <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" required placeholder="mis. 1-1001">
                    </div>
                    <div class="col-md-7">
                        <label class="form-label-sm">Nama Akun <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="mis. Kas Besar">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-sm">Tipe Akun <span class="text-danger">*</span></label>
                        <select name="type" class="form-control" required id="typeSelect" onchange="setNormalBalance()">
                            <option value="">-- Pilih Tipe --</option>
                            @foreach(App\Models\ChartOfAccount::TYPES as $k => $v)
                            <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-sm">Saldo Normal</label>
                        <select name="normal_balance" class="form-control" id="normalBalanceSelect">
                            <option value="debit">Debit</option>
                            <option value="credit">Kredit</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label-sm">Induk Akun</label>
                        <select name="parent_id" id="parentSelect" class="form-control">
                            <option value="">-- Tidak Ada (Akun Induk) --</option>
                            @foreach(App\Models\ChartOfAccount::active()->orderBy('code')->get() as $p)
                            <option value="{{ $p->id }}">{{ $p->code }} &mdash; {{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label-sm">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Opsional � keterangan singkat akun ini"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-secondary" style="border-radius:8px;font-size:0.85rem" data-dismiss="modal" onclick="resetModal()">Batal</button>
                <button type="submit" class="btn-primary-apms">
                    <i class="fas fa-save"></i> Simpan Akun
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function setParent(id, code, name) {
    document.getElementById('parentIdInput').value = id;
    document.getElementById('parentSelect').value = id;
    document.getElementById('parentLabel').textContent = code + ' � ' + name;
    document.getElementById('parentIndicator').classList.remove('d-none');
    document.getElementById('modalTitle').textContent = 'Tambah Sub-Akun';
}

function resetModal() {
    document.getElementById('parentIdInput').value = '';
    document.getElementById('parentSelect').value = '';
    document.getElementById('parentIndicator').classList.add('d-none');
    document.getElementById('modalTitle').textContent = 'Tambah Akun Baru';
}

function setNormalBalance() {
    var type = document.getElementById('typeSelect').value;
    var nb   = document.getElementById('normalBalanceSelect');
    if (type === 'asset' || type === 'expense') {
        nb.value = 'debit';
    } else if (type === 'liability' || type === 'equity' || type === 'revenue') {
        nb.value = 'credit';
    }
}

// Reset modal on close
document.getElementById('coaModal').addEventListener('hidden.bs.modal', resetModal);
</script>
@endsection