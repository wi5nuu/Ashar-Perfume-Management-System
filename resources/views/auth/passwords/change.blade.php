@extends('layouts.app')
@section('title', 'Ubah Kata Sandi - APMS')

@section('content')
<div class="container-fluid pt-3">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible rounded-lg">
                    <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('warning') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger rounded-lg mb-4">
                    <ul class="mb-0 list-unstyled">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow-sm border-0" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; background: linear-gradient(135deg, #FF6B35 0%, #E55A2B 100%); border-radius: 16px;">
                            <i class="fas fa-key text-white" style="font-size: 1.5rem;"></i>
                        </div>
                        <h5 class="font-weight-bold mb-1">Ubah Kata Sandi</h5>
                        <p class="text-muted mb-0" style="font-size: 0.85rem;">Pastikan kata sandi baru Anda kuat dan unik</p>
                    </div>

                    <form method="POST" action="{{ route('password.change') }}">
                        @csrf

                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-muted">Kata Sandi Saat Ini</label>
                            <div class="input-group-apms">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="current_password" placeholder="Masukkan kata sandi saat ini" required autofocus>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-muted">Kata Sandi Baru</label>
                            <div class="input-group-apms">
                                <i class="fas fa-key"></i>
                                <input type="password" name="password" placeholder="Min. 10 karakter, huruf besar, kecil, angka & khusus" required minlength="10">
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-muted">Konfirmasi Kata Sandi Baru</label>
                            <div class="input-group-apms">
                                <i class="fas fa-check-circle"></i>
                                <input type="password" name="password_confirmation" placeholder="Ketik ulang kata sandi baru" required minlength="10">
                            </div>
                        </div>

                        <div class="text-muted mb-4" style="font-size: 0.75rem; line-height: 1.4;">
                            <i class="fas fa-shield-alt mr-1"></i> Minimal 10 karakter, kombinasi huruf besar, huruf kecil, angka & karakter khusus. Tidak boleh sama dengan 5 kata sandi terakhir.
                        </div>

                        <button type="submit" class="btn btn-auth">
                            <i class="fas fa-save mr-1"></i> Ubah Kata Sandi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
