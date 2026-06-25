<x-guest-layout>
    <div class="auth-card">
        <div class="auth-header">
            <span class="auth-logo">
                <img src="{{ asset('favicon-512x512.png') }}" alt="APMS Logo" style="height: 36px; width: auto; object-fit: contain; margin-right: 5px;"> 
                APMS
            </span>
            <p class="auth-subtitle">Ashar Parfum Management System</p>
        </div>

        <div class="auth-body">
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

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <div class="input-group-apms">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email Address" value="{{ old('email') }}" required autofocus autocomplete="username">
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group-apms">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Kata Sandi" required autocomplete="current-password">
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4 px-1">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="remember_me" name="remember">
                        <label class="custom-control-label auth-link" for="remember_me" style="cursor:pointer;">Ingat Saya</label>
                    </div>
                    @if (Route::has('password.custom-forgot'))
                        <a href="{{ route('password.custom-forgot') }}" class="auth-link font-weight-bold">Lupa Password?</a>
                    @endif
                </div>

                <button type="submit" class="btn btn-auth">
                    Masuk <i class="fas fa-arrow-right ml-2"></i>
                </button>
                

            </form>

            <!-- Terms & Copyright -->
            <div class="mt-3 pt-2 text-center" style="border-top: 1px solid #e0e0e0;">
                <p class="mb-1" style="font-size:0.72rem;color:#888;line-height:1.4;">
                    Dengan melanjutkan, Anda menyetujui
                    <a href="#" style="color:#555;font-weight:600;text-decoration:none">Syarat & Ketentuan</a> serta
                    <a href="#" style="color:#555;font-weight:600;text-decoration:none">Kebijakan Privasi</a> kami.
                </p>
                <p class="mb-0" style="font-size:0.68rem;color:#999;">
                    &copy; {{ date('Y') }} Ashar Parfum Management System.
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
