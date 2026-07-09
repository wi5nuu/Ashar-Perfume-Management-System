<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Lupa Password - AL'ASHAR</title>
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
        .alert-info { background: #e3f2fd; color: #0d47a1; }
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
        .btn-secondary { background: #f5f5f5; color: #444; border: 1px solid #ddd; }
        .btn-secondary:hover { background: #eee; }
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
                <i class="fas fa-key"></i>
                <h2>Lupa Password</h2>
                <p>Masukkan email terdaftar Anda</p>
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

                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-1"></i> Permintaan akan dikirim ke Owner. Anda akan mendapatkan password baru setelah diproses.
                </div>

                <form action="{{ route('wholesale.customer.forgot-password.submit') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Email</label>
                        <div class="input-wrap">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="contoh@email.com" required autofocus>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Kirim Permintaan
                    </button>
                </form>

                <div style="text-align:center;margin-top:14px">
                    <a href="{{ route('wholesale.customer.login') }}" style="color:var(--p);font-size:0.85rem;text-decoration:none">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
                    </a>
                </div>
            </div>
        </div>
        <div class="footer">&copy; {{ date('Y') }} AL'ASHAR PARFUM</div>
    </div>
</body>
</html>