@extends('layouts.app')
@section('title', 'Izin Role: ' . $role->name . ' - APMS')

@push('styles')
<style>
:root {
    --primary: #FF6B35;
    --primary-dark: #E55A2B;
    --secondary: #2D3047;
}
.page-header-apms {
    background: linear-gradient(135deg, var(--secondary) 0%, #3d4266 100%);
    border-radius: 16px;
    padding: 24px 28px;
    color: #fff;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
}
.page-header-apms::before {
    content: '';
    position: absolute;
    top: -30px; right: -30px;
    width: 130px; height: 130px;
    background: rgba(255,107,53,0.15);
    border-radius: 50%;
}
.breadcrumb-apms { background: transparent; padding: 0; margin: 0; }
.breadcrumb-apms .breadcrumb-item a { color: rgba(255,255,255,0.65); text-decoration: none; font-size: 0.82rem; }
.breadcrumb-apms .breadcrumb-item.active { color: rgba(255,255,255,0.9); font-size: 0.82rem; }
.breadcrumb-apms .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.4); }

.card-modern {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 14px rgba(0,0,0,0.07);
    border: 1px solid #f0f0f0;
    overflow: hidden;
}
.card-modern .card-header-modern {
    padding: 16px 22px;
    border-bottom: 1px solid #f0f0f0;
    background: #fafafa;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.module-section {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #eef0f4;
    margin-bottom: 16px;
    overflow: hidden;
}
.module-header {
    padding: 12px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    user-select: none;
    transition: background .15s;
}
.module-header:hover { background: #f8f9fc; }
.module-title {
    font-weight: 700;
    font-size: 0.88rem;
    color: var(--secondary);
    display: flex;
    align-items: center;
    gap: 8px;
}
.module-icon {
    width: 30px; height: 30px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.8rem;
}
.module-body { padding: 14px 18px 18px; border-top: 1px solid #f0f0f0; }

/* Toggle Switch */
.perm-toggle-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 9px 12px;
    border-radius: 8px;
    background: #f8f9fc;
    margin-bottom: 8px;
    transition: background .15s;
}
.perm-toggle-wrap:hover { background: #f0f3ff; }
.perm-toggle-wrap.is-checked { background: rgba(255,107,53,0.06); }
.perm-info { flex: 1; min-width: 0; margin-right: 12px; }
.perm-name { font-size: 0.83rem; font-weight: 600; color: #333; }
.perm-desc { font-size: 0.72rem; color: #999; margin-top: 1px; }
.toggle-switch { position: relative; display: inline-block; width: 40px; height: 22px; flex-shrink: 0; }
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-slider {
    position: absolute; cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background: #ccc;
    border-radius: 22px;
    transition: .2s;
}
.toggle-slider:before {
    position: absolute; content: "";
    height: 16px; width: 16px;
    left: 3px; bottom: 3px;
    background: white;
    border-radius: 50%;
    transition: .2s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.toggle-switch input:checked + .toggle-slider { background: var(--primary); }
.toggle-switch input:checked + .toggle-slider:before { transform: translateX(18px); }
.toggle-switch input:disabled + .toggle-slider { background: #b0b0b0; cursor: not-allowed; opacity: 0.6; }

.btn-primary-apms {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border: none; color: #fff; border-radius: 8px;
    padding: 8px 20px; font-weight: 600; font-size: 0.85rem;
    transition: all .18s;
}
.btn-primary-apms:hover { background: linear-gradient(135deg, var(--primary-dark), #c94d22); color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(255,107,53,0.35); }

.user-avatar-sm {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 700; font-size: 0.78rem;
    flex-shrink: 0;
}
.role-badge-large {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.8rem; font-weight: 600;
    background: rgba(255,107,53,0.12);
    color: var(--primary);
    border: 1px solid rgba(255,107,53,0.25);
}
.save-sticky {
    position: sticky;
    bottom: 20px;
    z-index: 100;
    pointer-events: none;
}
.save-sticky-inner {
    pointer-events: all;
    display: flex;
    justify-content: flex-end;
}
.save-bar {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.15);
    padding: 12px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    border: 1px solid #f0f0f0;
}
</style>

@endpush

@section('content')
<div class="container-fluid pt-3">

    {{-- Page Header --}}
    <div class="page-header-apms">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-apms">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.rbac.index') }}">Roles & Permissions</a></li>
                <li class="breadcrumb-item active">{{ $role->name }}</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center justify-content-between mt-2">
            <div class="d-flex align-items-center" style="gap:14px;">
                <div>
                    <h4 class="mb-1 font-weight-bold" style="font-size:1.3rem;">
                        <i class="fas fa-key mr-2" style="color:var(--primary);"></i>Edit Permissions
                    </h4>
                    <div class="d-flex align-items-center" style="gap:10px;">
                        <span class="role-badge-large">
                            <i class="fas fa-user-shield"></i> {{ $role->name }}
                        </span>
                        <span style="color:rgba(255,255,255,0.6); font-size:0.82rem;">
                            {{ $role->permissions->count() }} permissions aktif
                        </span>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.rbac.index') }}" class="btn btn-sm d-none d-md-flex align-items-center"
               style="background:rgba(255,255,255,0.15); color:#fff; border-radius:8px; border:1px solid rgba(255,255,255,0.25); font-size:0.83rem; font-weight:600;">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert border-0 mb-4" style="background:rgba(40,167,69,0.1); color:#155724; border-radius:10px; border-left:4px solid #28a745 !important;">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.rbac.sync-permissions', $role) }}" id="permissionsForm">
        @csrf
        <div class="row">
            {{-- Permissions Panel --}}
            <div class="col-lg-8 mb-4">
                <div class="card-modern">
                    <div class="card-header-modern">
                        <div>
                            <span class="font-weight-bold" style="color:var(--secondary); font-size:0.95rem;">
                                <i class="fas fa-key mr-2" style="color:var(--primary);"></i>Daftar Permissions
                            </span>
                            <small class="d-block text-muted mt-1" style="font-size:0.75rem;">Aktifkan/nonaktifkan permission per modul</small>
                        </div>
                        <div class="d-flex align-items-center" style="gap:8px;">
                            <button type="button" id="selectAll" class="btn btn-sm btn-outline-secondary" style="border-radius:7px; font-size:0.78rem;">
                                <i class="fas fa-check-double mr-1"></i>Pilih Semua
                            </button>
                            <button type="button" id="deselectAll" class="btn btn-sm btn-outline-secondary" style="border-radius:7px; font-size:0.78rem;">
                                <i class="fas fa-times mr-1"></i>Hapus Semua
                            </button>
                        </div>
                    </div>
                    <div style="padding:18px;">
                        @php
                            $moduleIcons = [
                                'Transaksi'  => ['icon' => 'fa-receipt',      'color' => '#28a745'],
                                'Produk'     => ['icon' => 'fa-box',          'color' => '#007bff'],
                                'Pelanggan'  => ['icon' => 'fa-users',        'color' => '#6f42c1'],
                                'Keuangan'   => ['icon' => 'fa-money-bill',   'color' => '#d4a00a'],
                                'Laporan'    => ['icon' => 'fa-chart-bar',    'color' => '#FF6B35'],
                                'Pengguna'   => ['icon' => 'fa-user-cog',     'color' => '#dc3545'],
                                'Cabang'     => ['icon' => 'fa-building',     'color' => '#17a2b8'],
                                'Stok'       => ['icon' => 'fa-warehouse',    'color' => '#20c997'],
                                'Sistem'     => ['icon' => 'fa-cog',          'color' => '#6c757d'],
                            ];
                        @endphp

                        @foreach($permissions as $group => $perms)
                        @php
                            $modCfg = $moduleIcons[$group] ?? ['icon' => 'fa-circle', 'color' => '#6c757d'];
                            $checkedCount = collect($perms)->filter(fn($p) => $role->permissions->contains($p['id']))->count();
                        @endphp
                        <div class="module-section">
                            <div class="module-header" onclick="toggleModule('module_{{ Str::slug($group) }}')">
                                <div class="module-title">
                                    <div class="module-icon" style="background: {{ $modCfg['color'] }}18; color: {{ $modCfg['color'] }};">
                                        <i class="fas {{ $modCfg['icon'] }}"></i>
                                    </div>
                                    {{ $group }}
                                    <span class="badge" style="background: {{ $modCfg['color'] }}18; color: {{ $modCfg['color'] }}; border-radius:10px; font-size:0.7rem; padding:2px 8px;">
                                        {{ $checkedCount }}/{{ count($perms) }}
                                    </span>
                                </div>
                                <i class="fas fa-chevron-down text-muted module-chevron" style="font-size:0.75rem; transition:.2s;" id="chev_{{ Str::slug($group) }}"></i>
                            </div>
                            <div class="module-body" id="module_{{ Str::slug($group) }}">
                                <div class="row">
                                    @foreach($perms as $perm)
                                    @php $isChecked = $role->permissions->contains($perm['id']); @endphp
                                    <div class="col-md-6 mb-1">
                                        <div class="perm-toggle-wrap {{ $isChecked ? 'is-checked' : '' }}" id="wrap_{{ $perm['id'] }}">
                                            <div class="perm-info">
                                                <div class="perm-name">{{ $perm['name'] }}</div>
                                                @if(!empty($perm['description']))
                                                <div class="perm-desc">{{ $perm['description'] }}</div>
                                                @endif
                                            </div>
                                            <label class="toggle-switch" title="{{ $perm['name'] }}">
                                                <input type="checkbox"
                                                    name="permissions[]"
                                                    value="{{ $perm['id'] }}"
                                                    {{ $isChecked ? 'checked' : '' }}
                                                    {{ ($role->is_system ?? false) && $role->slug === 'owner' ? 'disabled' : '' }}
                                                    onchange="updateWrap(this)">
                                                <span class="toggle-slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Users with this role --}}
            <div class="col-lg-4 mb-4">
                <div class="card-modern mb-3">
                    <div class="card-header-modern">
                        <span class="font-weight-bold" style="color:var(--secondary); font-size:0.9rem;">
                            <i class="fas fa-users mr-2" style="color:var(--primary);"></i>Pengguna dengan Role Ini
                        </span>
                        <a href="{{ route('admin.rbac.users', $role) }}" class="btn btn-sm btn-outline-secondary" style="border-radius:7px; font-size:0.75rem;">
                            Lihat Semua
                        </a>
                    </div>
                    <div style="padding:14px;">
                        @forelse($role->users->take(8) as $user)
                        <div class="d-flex align-items-center mb-3">
                            <div class="user-avatar-sm mr-2">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                            <div style="min-width:0;">
                                <div class="font-weight-600" style="font-size:0.83rem; color:#333; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $user->name }}</div>
                                <div class="text-muted" style="font-size:0.72rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $user->email }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <i class="fas fa-users text-muted" style="font-size:2rem; opacity:.3;"></i>
                            <p class="text-muted mt-2 mb-0" style="font-size:0.82rem;">Belum ada pengguna</p>
                        </div>
                        @endforelse
                        @if($role->users->count() > 8)
                        <div class="text-center mt-1">
                            <small class="text-muted">+{{ $role->users->count() - 8 }} pengguna lainnya</small>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Role Info Card --}}
                <div class="card-modern">
                    <div class="card-header-modern">
                        <span class="font-weight-bold" style="color:var(--secondary); font-size:0.9rem;">
                            <i class="fas fa-info-circle mr-2" style="color:var(--primary);"></i>Info Role
                        </span>
                    </div>
                    <div style="padding:14px;">
                        <div class="d-flex justify-content-between mb-2" style="font-size:0.82rem;">
                            <span class="text-muted">Nama</span>
                            <span class="font-weight-600">{{ $role->name }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2" style="font-size:0.82rem;">
                            <span class="text-muted">Slug</span>
                            <code style="font-size:0.75rem; background:#f0f0f0; padding:1px 6px; border-radius:4px;">{{ $role->slug }}</code>
                        </div>
                        <div class="d-flex justify-content-between mb-2" style="font-size:0.82rem;">
                            <span class="text-muted">Total Permissions</span>
                            <span class="font-weight-700" style="color:var(--primary);" id="permCountDisplay">{{ $role->permissions->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between" style="font-size:0.82rem;">
                            <span class="text-muted">Tipe</span>
                            <span>{{ ($role->is_system ?? false) ? '<span class="badge badge-secondary" style="border-radius:6px;">System</span>' : '<span class="badge badge-light" style="border-radius:6px;">Custom</span>' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sticky Save Bar --}}
        <div class="save-sticky">
            <div class="save-sticky-inner">
                <div class="save-bar">
                    <span class="text-muted" style="font-size:0.82rem;">
                        <i class="fas fa-info-circle mr-1"></i>
                        <span id="changesIndicator">Perubahan belum disimpan</span>
                    </span>
                    <a href="{{ route('admin.rbac.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px; font-size:0.83rem;">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary-apms btn-sm" id="saveBtn">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>

</div>

@push('scripts')
<script>
function toggleModule(id) {
    var el = document.getElementById(id);
    var slug = id.replace('module_', '');
    var chev = document.getElementById('chev_' + slug);
    if (el.style.display === 'none') {
        el.style.display = '';
        if (chev) chev.style.transform = 'rotate(0deg)';
    } else {
        el.style.display = 'none';
        if (chev) chev.style.transform = 'rotate(-90deg)';
    }
}

function updateWrap(input) {
    var wrap = document.getElementById('wrap_' + input.value);
    if (wrap) {
        if (input.checked) wrap.classList.add('is-checked');
        else wrap.classList.remove('is-checked');
    }
    updateCount();
}

function updateCount() {
    var checked = document.querySelectorAll('input[name="permissions[]"]:checked').length;
    var el = document.getElementById('permCountDisplay');
    if (el) el.textContent = checked;
    var ind = document.getElementById('changesIndicator');
    if (ind) ind.textContent = checked + ' permission dipilih';
}

document.getElementById('selectAll').addEventListener('click', function() {
    document.querySelectorAll('input[name="permissions[]"]:not(:disabled)').forEach(function(cb) {
        cb.checked = true;
        var wrap = document.getElementById('wrap_' + cb.value);
        if (wrap) wrap.classList.add('is-checked');
    });
    updateCount();
});

document.getElementById('deselectAll').addEventListener('click', function() {
    document.querySelectorAll('input[name="permissions[]"]:not(:disabled)').forEach(function(cb) {
        cb.checked = false;
        var wrap = document.getElementById('wrap_' + cb.value);
        if (wrap) wrap.classList.remove('is-checked');
    });
    updateCount();
});

// Init count
updateCount();
</script>
@endpush
@endsection
