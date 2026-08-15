@extends('layouts.app')

@section('title', 'Edit Akun')

@push('styles')
<style>
    :root { --primary:#FF6B35; --primary-dark:#E55A2B; --secondary:#2D3047; }
    .page-header-bar { background:#fff; border-radius:14px; padding:1.2rem 1.6rem; margin-bottom:1.5rem; box-shadow:0 2px 12px rgba(0,0,0,.06); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.8rem; }
    .page-header-bar h4 { font-weight:700; color:var(--secondary); margin:0; font-size:1.15rem; display:flex; align-items:center; gap:.5rem; }
    .page-header-bar h4 i { color:var(--primary); }
    .form-card { background:#fff; border-radius:14px; box-shadow:0 2px 14px rgba(0,0,0,.07); border:1px solid rgba(0,0,0,.04); overflow:hidden; max-width:760px; }
    .form-card-header { padding:1rem 1.5rem; border-bottom:1px solid #f5f5f5; background:linear-gradient(90deg,#fafafa,#fff); }
    .form-card-header h5 { font-size:.95rem; font-weight:700; color:var(--secondary); margin:0; }
    .form-card-body { padding:1.4rem 1.5rem; }
    .form-label-custom { font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#888; margin-bottom:.35rem; display:block; }
    .form-control-custom { width:100%; border-radius:9px; border:1.5px solid #e8e8e8; padding:.52rem .85rem; font-size:.88rem; color:var(--secondary); }
    .form-control-custom:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(255,107,53,.12); }
    .btn-primary-apms { background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:#fff !important; border:none; border-radius:8px; font-weight:600; font-size:.85rem; padding:.5rem 1.1rem; display:inline-flex; align-items:center; gap:6px; text-decoration:none; }
    .btn-primary-apms:hover { color:#fff !important; opacity:.92; text-decoration:none; }
    .acct-badge { background:#f0f2f8; color:#556; font-family:monospace; font-size:.8rem; font-weight:700; padding:3px 9px; border-radius:6px; }
</style>
@endpush

@section('content')
<div class="container-fluid pb-4">

    <div class="page-header-bar">
        <h4><i class="fas fa-edit"></i> Edit Akun: <span class="acct-badge">{{ $account->code }}</span></h4>
        <div class="d-flex" style="gap:.5rem">
            <a href="{{ route('accounting.coa.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;font-size:.84rem">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger" style="border-radius:10px;font-size:.85rem">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="form-card">
        <div class="form-card-header">
            <h5><i class="fas fa-sitemap" style="color:var(--primary)"></i> Informasi Akun</h5>
        </div>
        <form method="POST" action="{{ route('accounting.coa.update', $account->id) }}">
            @csrf
            @method('PUT')
            <div class="form-card-body">
                <div class="row" style="row-gap:1rem">
                    <div class="col-md-6">
                        <label class="form-label-custom">Nama Akun <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control-custom" value="{{ old('name', $account->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Induk Akun</label>
                        <select name="parent_id" class="form-control-custom">
                            <option value="">-- Tidak Ada (Akun Induk) --</option>
                            @foreach($accounts as $p)
                            <option value="{{ $p->id }}" {{ $account->parent_id == $p->id ? 'selected' : '' }}>
                                {{ $p->code }} &mdash; {{ $p->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Status Posting</label>
                        <select name="is_posting" class="form-control-custom">
                            <option value="1" {{ $account->is_posting ? 'selected' : '' }}>Akun Posting</option>
                            <option value="0" {{ !$account->is_posting ? 'selected' : '' }}>Akun Header / Non-Posting</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Aktif</label>
                        <select name="is_active" class="form-control-custom">
                            <option value="1" {{ $account->is_active ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ !$account->is_active ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Klasifikasi</label>
                        <div class="d-flex" style="gap:1rem;padding-top:.45rem">
                            <label style="font-size:.85rem;font-weight:600;color:var(--secondary)">
                                <input type="checkbox" name="is_cash" value="1" {{ $account->is_cash ? 'checked' : '' }} style="margin-right:4px"> Kas
                            </label>
                            <label style="font-size:.85rem;font-weight:600;color:var(--secondary)">
                                <input type="checkbox" name="is_bank" value="1" {{ $account->is_bank ? 'checked' : '' }} style="margin-right:4px"> Bank
                            </label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Deskripsi</label>
                        <textarea name="description" class="form-control-custom" rows="3">{{ old('description', $account->description) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 d-flex justify-content-between" style="border-top:1px solid #f5f5f5">
                <form method="POST" action="{{ route('accounting.coa.destroy', $account->id) }}"
                      onsubmit="return confirm('Hapus akun {{ $account->code }}? Akun dengan riwayat jurnal tidak dapat dihapus.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger" style="border-radius:8px;font-size:.84rem">
                        <i class="fas fa-trash mr-1"></i> Hapus Akun
                    </button>
                </form>
                <button type="submit" class="btn-primary-apms"><i class="fas fa-save"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>

</div>
@endsection
