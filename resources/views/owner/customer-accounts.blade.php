@extends('layouts.app')

@section('title', 'Data Akun Pelanggan')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="font-weight-bold"><i class="fas fa-address-card mr-2"></i> Data Akun Pelanggan</h4>
            <p class="text-muted">Semua akun pelanggan yang memiliki akses login — password tersembunyi, klik ikon mata untuk melihat, <strong>dobel klik</strong> pada password untuk menyalin.</p>
        </div>
    </div>

    <div class="card card-apms border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Tipe</th>
                            <th>Bergabung</th>
                            <th>Terakhir Login</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $a)
                        <tr>
                            <td class="font-weight-bold">{{ $a->name }}</td>
                            <td><code>{{ $a->email }}</code></td>
                            <td>
                                <span class="pw-wrapper" style="position:relative;cursor:pointer;font-family:monospace;user-select:none"
                                      data-password="password123"
                                      ondblclick="copyPassword(this)">
                                    <span class="pw-mask">*****</span>
                                    <span class="pw-text" style="display:none">password123</span>
                                    <i class="pw-eye fas fa-eye ml-1" style="font-size:13px;color:#6c757d;cursor:pointer"
                                       onclick="togglePassword(this)"></i>
                                </span>
                            </td>
                            <td>
                                @if($a->role === 'wholesale_customer')
                                <span class="badge badge-info">Grosir</span>
                                @else
                                <span class="badge badge-secondary">{{ $a->role }}</span>
                                @endif
                            </td>
                            <td><small>{{ $a->created_at->format('d/m/Y') }}</small></td>
                            <td><small>{{ $a->last_login_at ? $a->last_login_at->format('d/m/Y H:i') : '-' }}</small></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada akun pelanggan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(icon) {
    const wrapper = icon.closest('.pw-wrapper');
    const mask = wrapper.querySelector('.pw-mask');
    const text = wrapper.querySelector('.pw-text');
    const isHidden = mask.style.display !== 'none';
    if (isHidden) {
        mask.style.display = 'none';
        text.style.display = 'inline';
        icon.className = 'pw-eye fas fa-eye-slash ml-1';
    } else {
        mask.style.display = 'inline';
        text.style.display = 'none';
        icon.className = 'pw-eye fas fa-eye ml-1';
    }
}

function copyPassword(el) {
    const wrapper = el.closest('.pw-wrapper');
    const pw = wrapper.dataset.password;
    navigator.clipboard.writeText(pw).then(() => {
        Swal.fire({icon:'success', title:'Tersalin!', text:'Password disalin ke clipboard', timer:1500, showConfirmButton:false});
    }).catch(() => {
        prompt('Salin password berikut:', pw);
    });
}
</script>
<style>
.pw-wrapper:hover .pw-eye {
    opacity: 1;
}
.pw-eye {
    opacity: 0.4;
    transition: opacity 0.15s;
}
</style>
@endpush