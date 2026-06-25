<x-guest-layout>
    <div class="auth-card">
        <div class="auth-header">
            <span class="auth-logo">
                <i class="fas fa-user-plus"></i>Daftar
            </span>
            <p class="auth-subtitle">Buat Akun Baru APMS</p>
        </div>

        <div class="auth-body">
            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 list-unstyled text-center">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <div class="input-group-apms">
                        <i class="fas fa-user"></i>
                        <input type="text" name="name" placeholder="Nama Lengkap" value="{{ old('name') }}" required autofocus autocomplete="name">
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group-apms">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required autocomplete="username">
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group-apms">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Kata Sandi Baru" required autocomplete="new-password" minlength="10">
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group-apms">
                        <i class="fas fa-check-circle"></i>
                        <input type="password" name="password_confirmation" placeholder="Konfirmasi Kata Sandi" required autocomplete="new-password" minlength="10">
                    </div>
                </div>

                <div class="text-muted mb-4 px-1" style="font-size: 0.75rem; line-height: 1.4;">
                    <i class="fas fa-shield-alt mr-1"></i> Minimal 10 karakter, kombinasi huruf besar, huruf kecil, angka & karakter khusus.
                </div>

                <button type="submit" class="btn btn-auth">
                    Daftar Sekarang <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </form>

            <div class="auth-footer">
                <a href="{{ route('login') }}" class="auth-link">
                    <i class="fas fa-arrow-left mr-1"></i> Sudah punya akun? Masuk
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
