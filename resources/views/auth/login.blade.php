<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — APMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        :root {
            --primary: #FF6B35;
            --primary-dark: #E55A2B;
            --secondary: #2D3047;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
        }

        /* ── LEFT PANEL ── */
        .auth-left {
            width: 45%;
            background: linear-gradient(145deg, var(--primary) 0%, var(--primary-dark) 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 2.5rem;
            position: relative;
            overflow: hidden;
        }
        .auth-left::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,0.07);
        }
        .auth-left::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }
        .brand-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 2rem;
            animation: pulse-logo 3s ease-in-out infinite;
        }
        @keyframes pulse-logo {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.04); }
        }
        .brand-logo img {
            width: 72px; height: 72px;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.25));
        }
        .brand-logo-text {
            font-size: 2.8rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: 2px;
            line-height: 1;
        }
        .brand-tagline {
            font-size: 1.05rem;
            color: rgba(255,255,255,0.88);
            text-align: center;
            font-weight: 500;
            line-height: 1.5;
            margin-bottom: 3rem;
            max-width: 280px;
        }
        .parfum-illustration {
            display: flex;
            gap: 1.2rem;
            align-items: flex-end;
            margin-bottom: 2.5rem;
        }
        .parfum-bottle {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            opacity: 0.85;
        }
        .parfum-bottle i {
            font-size: 2.2rem;
            color: #fff;
        }
        .parfum-bottle.large i { font-size: 3rem; opacity: 1; }
        .parfum-bottle span {
            font-size: 0.65rem;
            color: rgba(255,255,255,0.7);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .left-stats {
            display: flex;
            gap: 1.5rem;
        }
        .left-stat {
            text-align: center;
        }
        .left-stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            line-height: 1;
        }
        .left-stat-label {
            font-size: 0.68rem;
            color: rgba(255,255,255,0.7);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 3px;
        }

        /* ── RIGHT PANEL ── */
        .auth-right {
            flex: 1;
            background: #f7f8fc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .auth-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(45,48,71,0.12);
            padding: 2.5rem 2.25rem;
            width: 100%;
            max-width: 420px;
            animation: fade-in-up 0.5s ease both;
        }
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .auth-card-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-card-header .welcome {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 6px;
        }
        .auth-card-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 6px;
        }
        .auth-card-header p {
            font-size: 0.85rem;
            color: #8892a4;
        }

        .form-label-apms {
            font-size: 0.78rem;
            font-weight: 600;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            display: block;
        }
        .input-icon-wrap {
            position: relative;
        }
        .input-icon-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #b0b8c9;
            font-size: 0.9rem;
            pointer-events: none;
        }
        .input-icon-wrap input {
            width: 100%;
            padding: 0.7rem 0.9rem 0.7rem 2.6rem;
            border: 1.5px solid #e4e8f0;
            border-radius: 10px;
            font-size: 0.92rem;
            color: #2D3047;
            background: #f9fafc;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            outline: none;
        }
        .input-icon-wrap input:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(255,107,53,0.12);
        }
        .input-icon-wrap .toggle-pw {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #b0b8c9;
            cursor: pointer;
            padding: 2px 4px;
            font-size: 0.88rem;
            transition: color 0.2s;
        }
        .input-icon-wrap .toggle-pw:hover { color: var(--primary); }

        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s, opacity 0.15s;
            box-shadow: 0 4px 14px rgba(255,107,53,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(255,107,53,0.45);
            opacity: 0.95;
        }
        .btn-login:active {
            transform: translateY(0);
        }

        .alert-apms {
            border-radius: 10px;
            font-size: 0.85rem;
            padding: 0.7rem 1rem;
            margin-bottom: 1.25rem;
        }

        .divider-text {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 1.25rem 0;
            color: #c5cdd8;
            font-size: 0.75rem;
        }
        .divider-text::before, .divider-text::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #eef0f5;
        }

        .auth-footer-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }
        .custom-check-label {
            font-size: 0.83rem;
            color: #667;
            cursor: pointer;
            user-select: none;
        }
        .custom-check-label input[type="checkbox"] {
            accent-color: var(--primary);
            margin-right: 6px;
        }
        .link-forgot {
            font-size: 0.83rem;
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }
        .link-forgot:hover { color: var(--primary-dark); text-decoration: underline; }

        .auth-copyright {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.7rem;
            color: #b0b8c9;
        }
        .auth-copyright a { color: #8892a4; text-decoration: none; font-weight: 500; }
        .auth-copyright a:hover { color: var(--primary); }

        /* ── MOBILE ── */
        @media (max-width: 768px) {
            .auth-left { display: none; }
            .auth-right { padding: 1.5rem 1rem; background: linear-gradient(160deg, var(--primary) 0%, var(--primary-dark) 35%, #f7f8fc 35%); }
            .auth-card {
                padding: 2rem 1.5rem;
                box-shadow: 0 12px 48px rgba(45,48,71,0.16);
            }
        }
    </style>
</head>
<body>
<div class="auth-wrapper">

    {{-- LEFT: Branding --}}
    <div class="auth-left">
        <div class="brand-logo">
            <img src="{{ asset('favicon-512x512.png') }}" alt="APMS Logo">
            <span class="brand-logo-text">APMS</span>
        </div>
        <p class="brand-tagline">Sistem Manajemen Distribusi Parfum Terbaik</p>

        <div class="parfum-illustration">
            <div class="parfum-bottle">
                <i class="fas fa-spray-can"></i>
                <span>Retail</span>
            </div>
            <div class="parfum-bottle large">
                <i class="fas fa-wind"></i>
                <span>Premium</span>
            </div>
            <div class="parfum-bottle">
                <i class="fas fa-flask"></i>
                <span>Grosir</span>
            </div>
        </div>

        <div class="left-stats">
            <div class="left-stat">
                <div class="left-stat-value">500+</div>
                <div class="left-stat-label">Produk</div>
            </div>
            <div class="left-stat">
                <div class="left-stat-value" style="color:rgba(255,255,255,0.4)">|</div>
                <div class="left-stat-label">&nbsp;</div>
            </div>
            <div class="left-stat">
                <div class="left-stat-value">99%</div>
                <div class="left-stat-label">Uptime</div>
            </div>
            <div class="left-stat">
                <div class="left-stat-value" style="color:rgba(255,255,255,0.4)">|</div>
                <div class="left-stat-label">&nbsp;</div>
            </div>
            <div class="left-stat">
                <div class="left-stat-value">Real-time</div>
                <div class="left-stat-label">Laporan</div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Form --}}
    <div class="auth-right">
        <div class="auth-card">
            <div class="auth-card-header">
                <div class="welcome">Selamat Datang</div>
                <h2>Masuk ke APMS</h2>
                <p>Ashar Parfum Management System</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success alert-apms">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-apms">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    @foreach ($errors->all() as $error){{ $error }}@endforeach
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" autocomplete="off">
                @csrf

                <div class="form-group mb-4">
                    <label class="form-label-apms" for="email">Alamat Email</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email"
                               placeholder="nama@domain.com"
                               value="{{ old('email') }}"
                               required autofocus autocomplete="username">
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label-apms" for="password">Kata Sandi</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password"
                               placeholder="Masukkan kata sandi"
                               required autocomplete="current-password">
                        <button type="button" class="toggle-pw" onclick="togglePw()">
                            <i class="fas fa-eye" id="pw-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="auth-footer-links">
                    <label class="custom-check-label">
                        <input type="checkbox" name="remember" id="remember_me">
                        Ingat Saya
                    </label>
                    @if (Route::has('password.custom-forgot'))
                        <a href="{{ route('password.custom-forgot') }}" class="link-forgot">Lupa Password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-login">
                    Masuk <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <div class="auth-copyright">
                <p class="mb-1">
                    Dengan melanjutkan, Anda menyetujui
                    <a href="#">Syarat & Ketentuan</a> serta <a href="#">Kebijakan Privasi</a> kami.
                </p>
                <p class="mb-0">&copy; {{ date('Y') }} Ashar Parfum Management System.</p>
            </div>
        </div>
    </div>

</div>

<script>
function togglePw() {
    var inp = document.getElementById('password');
    var eye = document.getElementById('pw-eye');
    if (inp.type === 'password') {
        inp.type = 'text';
        eye.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        inp.type = 'password';
        eye.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
</body>
</html>
