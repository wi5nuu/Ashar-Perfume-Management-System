<x-guest-layout>
    <div class="auth-card">
        <div class="auth-header">
            <span class="auth-logo">
                <i class="fas fa-shield-alt"></i>Konfirmasi
            </span>
            <p class="auth-subtitle">Konfirmasi Password</p>
        </div>

        <div class="auth-body">
            <div class="mb-4 text-muted text-center px-2" style="font-size: 0.85rem;">
                Ini adalah area aman aplikasi. Silakan konfirmasi password Anda sebelum melanjutkan.
            </div>

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 list-unstyled text-center">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="form-group">
                    <div class="input-group-apms">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Kata Sandi Saat Ini" required autocomplete="current-password">
                    </div>
                </div>

                <button type="submit" class="btn btn-auth">
                    Konfirmasi <i class="fas fa-check ml-2"></i>
                </button>
            </form>

            <div class="auth-footer">
                <a href="{{ route('login') }}" class="auth-link">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
