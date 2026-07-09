@extends('layouts.app')
@section('title', 'Autentikasi Dua Faktor - APMS')

@section('content')
<div class="container-fluid pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-dark"><i class="fas fa-qrcode mr-2 text-primary"></i>Autentikasi Dua Faktor</h4>
            <p class="text-muted">Tingkatkan keamanan akun dengan TOTP 2FA</p>
        </div>
        <a href="{{ route('admin.security.overview') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-6">
            @if($enabled)
            <div class="card card-apms shadow-sm border-0">
                <div class="card-header bg-success text-white py-3 font-weight-bold">
                    <i class="fas fa-check-circle mr-1"></i> 2FA Sudah Aktif
                </div>
                <div class="card-body text-center py-4">
                    <i class="fas fa-shield-alt fa-4x text-success mb-3"></i>
                    <p class="mb-3">Autentikasi dua faktor aktif untuk akun <strong>{{ $user->email }}</strong>.</p>
                    <form method="POST" action="{{ route('admin.security.two-factor.disable') }}" onsubmit="return confirm('Nonaktifkan 2FA? Ini akan menurunkan tingkat keamanan akun Anda.')">
                        @csrf
                        <div class="form-group">
                            <input type="password" name="password" class="form-control" placeholder="Masukkan kata sandi untuk konfirmasi" required>
                        </div>
                        <button type="submit" class="btn btn-outline-danger"><i class="fas fa-times mr-1"></i> Nonaktifkan 2FA</button>
                    </form>
                </div>
            </div>
            @else
            <div class="card card-apms shadow-sm border-0">
                <div class="card-header bg-white py-3 font-weight-bold">
                    <i class="fas fa-qrcode mr-2 text-primary"></i> Aktifkan 2FA
                </div>
                <div class="card-body">
                    <p class="text-muted">Scan kode QR berikut dengan aplikasi authenticator (Google Authenticator, Authy, dll):</p>

                    <div class="text-center mb-4">
                        <div id="qrcode" class="d-inline-block p-3 border rounded bg-white">
                            <i class="fas fa-qrcode fa-5x text-muted"></i>
                        </div>
                        <p class="mt-2 mb-0"><button class="btn btn-sm btn-link" onclick="showManualSetup()">Tidak bisa scan? Setup manual</button></p>
                    </div>

                    <div id="manualSetup" class="d-none">
                        <div class="alert alert-info">
                            <strong>Secret Key:</strong>
                            <code id="secretKey" class="d-block mt-2 p-2 bg-light rounded text-center" style="font-size:1.2rem;letter-spacing:2px;">******{{ substr($secret, -4) }}</code>
                            <small class="d-block mt-1">
                                <button type="button" class="btn btn-sm btn-outline-info mt-1" onclick="revealSecret()">Tampilkan Kode Rahasia</button>
                            </small>
                            <small class="d-block mt-1">Masukkan kode ini manual di aplikasi authenticator Anda.</small>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.security.two-factor.confirm') }}">
                        @csrf
                        <div class="form-group">
                            <label>Kode Verifikasi dari Aplikasi Authenticator</label>
                            <input type="text" name="code" class="form-control text-center @error('code') is-invalid @enderror"
                                   placeholder="000000" maxlength="6" inputmode="numeric" pattern="[0-9]*" required>
                            @error('code')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary-apms btn-block py-2">
                            <i class="fas fa-check mr-1"></i> Verifikasi & Aktifkan
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    var secret = @json($secret);
    var email = @json($user->email);
    var issuer = 'APMS';
    var otpauth = 'otpauth://totp/' + encodeURIComponent(issuer) + ':' + encodeURIComponent(email) +
                  '?secret=' + secret + '&issuer=' + encodeURIComponent(issuer) + '&algorithm=SHA1&digits=6&period=30';

    new QRCode(document.getElementById('qrcode'), {
        text: otpauth,
        width: 220,
        height: 220,
        colorDark: '#2D3047',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H,
    });

    function showManualSetup() {
        document.getElementById('manualSetup').classList.toggle('d-none');
    }

    function revealSecret() {
        var el = document.getElementById('secretKey');
        el.textContent = secret;
        el.style.userSelect = 'all';
        setTimeout(function() {
            el.textContent = '******' + secret.slice(-4);
        }, 30000);
    }
</script>
@endpush
