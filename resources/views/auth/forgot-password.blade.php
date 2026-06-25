<x-guest-layout>
    <div class="auth-card">
        <div class="auth-header">
            <span class="auth-logo">
                <i class="fas fa-key"></i>Reset
            </span>
            <p class="auth-subtitle">Reset Password Akun</p>
        </div>

        <div class="auth-body">
            <div class="mb-4 text-muted text-center px-2" style="font-size: 0.85rem;">
                Jangan khawatir. Masukkan email terdaftar Anda dan kami akan mengirimkan link untuk membuat password baru.
            </div>

            @if (session('status'))
                <div class="alert alert-success mb-4 text-center">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 list-unstyled text-center">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group">
                    <div class="input-group-apms">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email Terdaftar" value="{{ old('email') }}" required autofocus autocomplete="email">
                    </div>
                </div>

                <button type="submit" class="btn btn-auth mt-2">
                    Kirim Link Reset <i class="fas fa-paper-plane ml-2"></i>
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
