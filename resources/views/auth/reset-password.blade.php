<x-guest-layout>
    <div class="auth-card">
        <div class="auth-header">
            <span class="auth-logo">
                <i class="fas fa-unlock-alt"></i>Reset
            </span>
            <p class="auth-subtitle">Buat Password Baru</p>
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

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="form-group">
                    <div class="input-group-apms">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="email">
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group-apms">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password Baru" required autocomplete="new-password" minlength="10">
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group-apms">
                        <i class="fas fa-check-circle"></i>
                        <input type="password" name="password_confirmation" placeholder="Konfirmasi Password Baru" required autocomplete="new-password" minlength="10">
                    </div>
                </div>

                <div class="text-muted mb-4 px-1" style="font-size: 0.75rem; line-height: 1.4;">
                    <i class="fas fa-shield-alt mr-1"></i> Minimal 10 karakter, kombinasi huruf besar, huruf kecil, angka & karakter khusus.
                </div>

                <button type="submit" class="btn btn-auth">
                    Reset Password <i class="fas fa-sync ml-2"></i>
                </button>
            </form>

            <div class="auth-footer">
                <a href="{{ route('login') }}" class="auth-link">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
