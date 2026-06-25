<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login Pelanggan Grosir - AL'ASHAR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --p: #FF6B35; --pd: #e55a2b; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #fff;
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }
        .wrap { width: 100%; max-width: 400px; }
        .brand { text-align: center; margin-bottom: 20px; }
        .brand img { max-width: 120px; height: auto; margin-bottom: 6px; }
        .brand h1 { color: var(--p); font-size: 1.3rem; font-weight: 700; letter-spacing: -0.5px; }
        .brand p { color: #888; font-size: 0.8rem; margin-top: 2px; }
        .card {
            background: #fff; border-radius: 12px; overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
        }
        .card-header {
            background: var(--p); padding: 22px 20px 16px; text-align: center; color: #fff;
        }
        .card-header i { font-size: 2rem; margin-bottom: 4px; }
        .card-header h2 { font-size: 1.1rem; font-weight: 700; }
        .card-header p { font-size: 0.78rem; opacity: 0.85; margin-top: 2px; }
        .card-body { padding: 20px; }
        .alert {
            padding: 10px 14px; border-radius: 8px; font-size: 0.82rem; margin-bottom: 14px;
        }
        .alert-success { background: #e8f5e9; color: #1b5e20; }
        .alert-danger { background: #fce4ec; color: #b71c1c; }
        .form-group { margin-bottom: 14px; }
        .form-group label { font-size: 0.82rem; font-weight: 600; color: #444; margin-bottom: 4px; display: block; }
        .input-wrap {
            display: flex; align-items: center; border: 1px solid #ddd; border-radius: 8px;
            background: #fafafa; transition: border-color 0.2s;
        }
        .input-wrap:focus-within { border-color: var(--p); box-shadow: 0 0 0 3px rgba(255,107,53,0.1); }
        .input-wrap i { width: 42px; text-align: center; color: #999; font-size: 0.9rem; }
        .input-wrap input {
            flex: 1; border: none; background: transparent; padding: 13px 13px 13px 0;
            font-size: 0.95rem; outline: none; color: #222;
        }
        .btn {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 14px; border: none; border-radius: 8px;
            font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: all 0.2s;
        }
        .btn-primary { background: var(--p); color: #fff; }
        .btn-primary:hover { background: var(--pd); }
        .btn-google {
            background: #fff; color: #444; border: 1px solid #ddd;
        }
        .btn-google:hover { background: #f5f5f5; border-color: #ccc; }
        .divider {
            display: flex; align-items: center; text-align: center; margin: 16px 0;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; border-bottom: 1px solid #e0e0e0;
        }
        .divider span { padding: 0 12px; color: #999; font-size: 0.75rem; font-weight: 600; }
        .terms {
            margin-top: 18px; padding-top: 14px; text-align: center;
            border-top: 1px solid #eee; font-size: 0.7rem; color: #999; line-height: 1.5;
        }
        .terms a { color: var(--p); text-decoration: none; font-weight: 600; }
        .terms a:hover { text-decoration: underline; }
        .footer { text-align: center; margin-top: 14px; font-size: 0.7rem; color: #bbb; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="brand">
            <img src="{{ asset('logotoko.png') }}" alt="AL'ASHAR PARFUM">
            <h1>AL'ASHAR PARFUM</h1>
            <p>Portal Pelanggan Grosir</p>
        </div>
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-circle"></i>
                <h2>Login Pelanggan</h2>
                <p>Masuk untuk memantau pesanan grosir Anda</p>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                    </div>
                @endif

                {{-- Google Login --}}
                <a href="{{ route('wholesale.customer.auth.google') }}" class="btn btn-google" style="text-decoration:none">
                    <svg width="18" height="18" viewBox="0 0 48 48"><path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/><path fill="#FF3D00" d="m6.306 14.691 6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/><path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238A11.91 11.91 0 0 1 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/><path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/></svg>
                    Lanjutkan dengan Google
                </a>

                <div class="divider"><span>ATAU</span></div>

                {{-- Email Login --}}
                <form action="{{ route('wholesale.customer.login') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Email</label>
                        <div class="input-wrap">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="contoh@email.com" required autofocus>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-wrap">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" placeholder="Masukkan password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>

                {{-- Terms --}}
                <div class="terms">
                    Dengan melanjutkan, Anda menyetujui<br>
                    <a href="#">Syarat & Ketentuan</a> serta <a href="#">Kebijakan Privasi</a> AL'ASHAR PARFUM.<br>
                    Pesanan grosir bersifat <strong>mengikat secara hukum</strong> sesuai kesepakatan.
                </div>
            </div>
        </div>
        <div class="footer">&copy; {{ date('Y') }} AL'ASHAR PARFUM</div>
    </div>
</body>
</html>
