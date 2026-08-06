<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password — APMS</title>
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

        .auth-wrapper { min-height: 100vh; display: flex; }

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
        .left-info-box {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 14px;
            padding: 1.5rem;
            max-width: 280px;
            width: 100%;
        }
        .left-info-box h5 {
            color: #fff;
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .left-info-step {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 0.75rem;
        }
        .left-info-step:last-child { margin-bottom: 0; }
        .step-num {
            width: 22px; height: 22px;
            border-radius: 50%;
            background: rgba(255,255,255,0.25);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .step-text {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.85);
            line-height: 1.4;
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
            max-width: 440px;
            animation: fade-in-up 0.5s ease both;
        }
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .auth-card-header {
            text-align: center;
            margin-bottom: 1.75rem;
        }
        .auth-card-header .page-tag {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 6px;
        }
        .auth-card-header h2 {
            font-size: 1.65rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 6px;
        }
        .auth-card-header p {
            font-size: 0.85rem;
            color: #8892a4;
            line-height: 1.5;
        }
        .icon-header {
            width: 60px; height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255,107,53,0.12), rgba(229,90,43,0.08));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        .icon-header i {
            font-size: 1.6rem;
            color: var(--primary);
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

        .btn-submit-apms {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 14px rgba(255,107,53,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-submit-apms:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(255,107,53,0.45);
        }

        /* Status panels */
        .status-panel {
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
            text-align: center;
        }
        .status-panel.success {
            background: linear-gradient(135deg, #e8f5e9, #f1f8e9);
            border: 1px solid #a5d6a7;
        }
        .status-panel.warning {
            background: linear-gradient(135deg, #fff8e1, #fffde7);
            border: 1px solid #ffe082;
        }
        .status-panel.info {
            background: linear-gradient(135deg, #e3f2fd, #e8eaf6);
            border: 1px solid #90caf9;
        }
        .status-panel.danger {
            background: linear-gradient(135deg, #fce4ec, #ffeee8);
            border: 1px solid #ef9a9a;
        }
        .status-panel .status-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            display: block;
        }
        .status-panel h6 {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
        }
        .status-panel p {
            font-size: 0.82rem;
            margin-bottom: 0;
            line-height: 1.5;
        }

        .password-reveal-box {
            background: #fffde7;
            border: 2px dashed #f9a825;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
            text-align: center;
        }
        .password-reveal-box .pw-label {
            font-size: 0.72rem;
            color: #795548;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .password-reveal-box .pw-value {
            font-size: 1.4rem;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            color: #1a237e;
            letter-spacing: 3px;
            background: #fff;
            border-radius: 6px;
            padding: 0.5rem 1rem;
            display: inline-block;
            margin-bottom: 8px;
            border: 1px solid #e8d44d;
        }
        .password-reveal-box .pw-warning {
            font-size: 0.75rem;
            color: #c62828;
            font-weight: 600;
        }

        .timer-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(211,47,47,0.1);
            color: #d32f2f;
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 0.78rem;
            font-weight: 600;
            margin-top: 6px;
        }

        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 1.25rem;
            font-size: 0.85rem;
            color: #8892a4;
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-link i { color: var(--primary); }
        .back-link:hover { color: var(--primary); text-decoration: none; }

        .auth-copyright {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid #eef0f5;
            font-size: 0.7rem;
            color: #b0b8c9;
        }

        @media (max-width: 768px) {
            .auth-left { display: none; }
            .auth-right { padding: 1.5rem 1rem; background: linear-gradient(160deg, var(--primary) 0%, var(--primary-dark) 35%, #f7f8fc 35%); }
            .auth-card { padding: 2rem 1.5rem; }
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

        <div class="left-info-box">
            <h5><i class="fas fa-shield-alt mr-2"></i>Cara Reset Password</h5>
            <div class="left-info-step">
                <div class="step-num">1</div>
                <div class="step-text">Masukkan alamat email akun Anda</div>
            </div>
            <div class="left-info-step">
                <div class="step-num">2</div>
                <div class="step-text">Owner akan menerima notifikasi permintaan</div>
            </div>
            <div class="left-info-step">
                <div class="step-num">3</div>
                <div class="step-text">Jika Owner tidak merespon, sistem auto-reset pukul 09:00–21:00</div>
            </div>
            <div class="left-info-step">
                <div class="step-num">4</div>
                <div class="step-text">Segera ganti password setelah login</div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Form --}}
    <div class="auth-right">
        <div class="auth-card">
            <div class="auth-card-header">
                <div class="icon-header">
                    <i class="fas fa-key"></i>
                </div>
                <div class="page-tag">Keamanan Akun</div>
                <h2>Lupa Password</h2>
                <p>Masukkan email Anda untuk memulai proses reset password</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success" style="border-radius:10px;font-size:0.85rem;">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" style="border-radius:10px;font-size:0.85rem;">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    @foreach ($errors->all() as $error){{ $error }}@endforeach
                </div>
            @endif

            {{-- STATUS: Approved / Auto-Approved --}}
            @if ($statusType === 'approved' || $statusType === 'auto_approved')
                @php $oncePw = session('_once_pw'); @endphp
                <div class="status-panel success">
                    <span class="status-icon"><i class="fas fa-check-circle" style="color:#2e7d32"></i></span>
                    <h6 style="color:#2e7d32">Password Berhasil Direset</h6>
                    <p style="color:#388e3c">
                        @if ($statusType === 'auto_approved')
                            Sistem telah otomatis mereset password Anda karena Owner belum merespon.
                        @else
                            Owner telah menyetujui dan mereset password Anda.
                        @endif
                    </p>
                </div>

                @if ($oncePw)
                <div class="password-reveal-box">
                    <div class="pw-label"><i class="fas fa-lock mr-1"></i>Password Baru Anda (tampil SEKALI)</div>
                    <div class="pw-value">{{ $oncePw }}</div>
                    <div class="pw-warning">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Salin dan simpan sekarang! Tidak akan ditampilkan lagi setelah halaman dimuat ulang.
                    </div>
                </div>
                @else
                <div class="alert alert-success" style="border-radius:10px;font-size:0.84rem;">
                    <i class="fas fa-check-circle mr-2"></i>Password telah direset. Silakan login dengan password baru.
                </div>
                @endif

                <a href="{{ route('login') }}" class="btn-submit-apms" style="text-decoration:none;margin-top:0.5rem;">
                    <i class="fas fa-sign-in-alt"></i> Login Sekarang
                </a>
                <p class="text-center mt-2 mb-0" style="font-size:0.75rem;color:#e57373;">
                    <i class="fas fa-exclamation-triangle mr-1"></i>Segera ganti password setelah login.
                </p>

            {{-- STATUS: Outside hours --}}
            @elseif ($statusType === 'outside_hours')
                <div class="status-panel danger">
                    <span class="status-icon"><i class="fas fa-clock" style="color:#c62828"></i></span>
                    <h6 style="color:#c62828">Di Luar Jam Operasional</h6>
                    <p style="color:#b71c1c">
                        Auto-reset hanya tersedia pukul <strong>09:00–21:00</strong>.<br>
                        Silakan coba kembali setelah pukul 09:00 pagi.
                    </p>
                </div>

            {{-- STATUS: Pending --}}
            @elseif ($statusType === 'pending')
                <div class="status-panel warning">
                    <span class="status-icon"><i class="fas fa-hourglass-half" style="color:#f57f17"></i></span>
                    <h6 style="color:#e65100">Permintaan Masih Diproses</h6>
                    <p style="color:#bf360c">Permintaan reset password Anda masih menunggu persetujuan Owner.</p>
                    @if ($withinHours && $remainingMinutes > 0)
                        <div class="timer-badge">
                            <i class="fas fa-clock"></i>
                            Auto-reset dalam {{ $remainingMinutes }} menit
                        </div>
                    @elseif (!$withinHours)
                        <p class="mt-2 mb-0" style="font-size:0.75rem;color:#d32f2f;font-weight:600;">
                            Auto-reset hanya pukul 09:00–21:00
                        </p>
                    @endif
                </div>
                <p class="text-center" style="font-size:0.82rem;color:#8892a4;">
                    Silakan cek kembali nanti atau hubungi Owner secara langsung.
                </p>

            {{-- STATUS: Created --}}
            @elseif ($statusType === 'created')
                <div class="status-panel info">
                    <span class="status-icon"><i class="fas fa-paper-plane" style="color:#1565c0"></i></span>
                    <h6 style="color:#0d47a1">Permintaan Dikirim</h6>
                    <p style="color:#1565c0">Permintaan reset password telah dikirimkan ke Owner.</p>
                    @if ($withinHours)
                        <div class="timer-badge">
                            <i class="fas fa-clock"></i>
                            Auto-reset dalam {{ $remainingMinutes }} menit jika Owner tidak merespon
                        </div>
                    @else
                        <p class="mt-2 mb-0" style="font-size:0.75rem;color:#d32f2f;font-weight:600;">
                            Auto-reset hanya pukul 09:00–21:00
                        </p>
                    @endif
                </div>
                <p class="text-center" style="font-size:0.82rem;color:#8892a4;">
                    Cek kembali nanti dengan memasukkan email yang sama.
                </p>
            @endif

            {{-- Form — always show unless already approved --}}
            @if ($statusType !== 'approved' && $statusType !== 'auto_approved')
                @if ($statusType !== 'created' && $statusType !== 'pending')
                <p class="text-center mb-3" style="font-size:0.82rem;color:#8892a4;">
                    <i class="fas fa-info-circle mr-1" style="color:var(--primary)"></i>
                    Auto-reset (jika Owner tidak merespon) tersedia pukul <strong>09:00–21:00</strong>.
                </p>
                @endif

                <form action="{{ route('password.custom-forgot.store') }}" method="POST">
                    @csrf
                    <div class="form-group mb-4">
                        <label class="form-label-apms" for="email">Alamat Email</label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email"
                                   placeholder="nama@domain.com"
                                   value="{{ old('email') }}"
                                   required autofocus>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit-apms">
                        <i class="fas fa-paper-plane"></i>
                        {{ $statusType === 'pending' || $statusType === 'created' ? 'Cek Status' : 'Kirim Permintaan' }}
                    </button>
                </form>
            @endif

            <a href="{{ route('login') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Kembali ke Halaman Login
            </a>

            <div class="auth-copyright">
                &copy; {{ date('Y') }} Ashar Parfum Management System.
            </div>
        </div>
    </div>

</div>
</body>
</html>
