<x-guest-layout>
    <div class="auth-card">
        <div class="auth-header">
            <span class="auth-logo">
                <img src="{{ asset('favicon-512x512.png') }}" alt="APMS Logo" style="height: 45px; width: auto; object-fit: contain; margin-right: 5px;"> 
                APMS
            </span>
            <p class="auth-subtitle">Lupa Password</p>
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

            @if ($statusType === 'approved' || $statusType === 'auto_approved')
                <div class="text-center mb-3">
                    <i class="fas fa-check-circle fa-2x mb-1 d-block" style="color:#28a745"></i>
                    <h6 class="font-weight-bold" style="color:#28a745">Password Berhasil Direset</h6>
                    <p class="small mb-2">
                        @if ($statusType === 'auto_approved')
                            Owner belum merespon, sistem telah otomatis mereset password Anda.
                        @else
                            Owner telah mereset password Anda.
                        @endif
                    </p>
                    <div style="background:#fff3cd;padding:10px;border-radius:4px;border:1px solid #ffc107">
                        <p class="small text-muted mb-1">Password baru Anda (hanya ditampilkan SEKALI):</p>
                        <h5 class="font-weight-bold mb-0" style="letter-spacing:2px;font-family:monospace;color:#222">
                            {{ $password }}
                        </h5>
                        <p class="small text-danger mt-2 mb-0 font-weight-bold">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Salin dan simpan password ini sekarang! Halaman ini tidak akan menampilkannya lagi setelah dimuat ulang.
                        </p>
                    </div>
                    <a href="{{ route('login') }}" class="btn btn-sm btn-success btn-block mt-2 font-weight-bold" style="border-radius:4px">
                        <i class="fas fa-sign-in-alt mr-1"></i> Login Sekarang
                    </a>
                    <p class="small text-muted mt-2 mb-0">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Segera ganti password setelah login.
                    </p>
                </div>

            @elseif ($statusType === 'outside_hours')
                <div class="text-center mb-3" style="background:#fce4ec;padding:12px;border-radius:4px;">
                    <i class="fas fa-clock fa-lg mb-1 d-block" style="color:#d32f2f"></i>
                    <h6 class="font-weight-bold" style="color:#d32f2f">Di Luar Jam Operasional</h6>
                    <p class="mb-0 small">
                        Auto-reset hanya tersedia pukul <strong>09:00 - 21:00</strong>.<br>
                        Silakan cek kembali setelah jam 09:00 pagi.
                    </p>
                </div>

            @elseif ($statusType === 'pending')
                <div class="text-center mb-3" style="background:#fff3e0;padding:12px;border-radius:4px;">
                    <i class="fas fa-clock fa-lg mb-1 d-block" style="color:#e65100"></i>
                    <h6 class="font-weight-bold" style="color:#e65100">Permintaan Masih Diproses</h6>
                    <p class="small mb-1">Permintaan reset password Anda masih menunggu.</p>
                    @if ($withinHours && $remainingMinutes > 0)
                    <p class="small mb-0 font-weight-bold">
                        Sistem akan otomatis mereset dalam <span style="color:#d32f2f">{{ $remainingMinutes }} menit</span> lagi jika Owner belum merespon.
                    </p>
                    @elseif (!$withinHours)
                    <p class="small mb-0 font-weight-bold" style="color:#d32f2f">
                        Auto-reset hanya tersedia pukul 09:00 - 21:00.
                    </p>
                    @endif
                </div>
                <p class="text-center small text-muted mt-1">
                    Silakan cek kembali nanti atau hubungi Owner.
                </p>

            @elseif ($statusType === 'created')
                <div class="text-center mb-3" style="background:#e3f2fd;padding:12px;border-radius:4px;">
                    <i class="fas fa-paper-plane fa-lg mb-1 d-block" style="color:#1565c0"></i>
                    <h6 class="font-weight-bold" style="color:#1565c0">Permintaan Dikirim</h6>
                    <p class="small mb-1">Permintaan reset password telah dikirim ke Owner.</p>
                    @if ($withinHours)
                    <p class="small mb-0 font-weight-bold">
                        Jika tidak direspon Owner, sistem akan otomatis mereset dalam <span style="color:#d32f2f">{{ $remainingMinutes }} menit</span>.
                    </p>
                    @else
                    <p class="small mb-0 font-weight-bold" style="color:#d32f2f">
                        Auto-reset hanya tersedia pukul 09:00 - 21:00.
                    </p>
                    @endif
                </div>
                <p class="text-center small text-muted mt-1">
                    Silakan cek kembali nanti dengan memasukkan email yang sama.
                </p>
            @endif

            {{-- Form — always show unless already approved --}}
            @if ($statusType !== 'approved' && $statusType !== 'auto_approved')
            <p class="text-muted text-center mb-3 small">
                Masukkan email akun Anda. Auto-reset (jika Owner tidak merespon) hanya tersedia pukul <strong>09:00 - 21:00</strong>.
            </p>

            <form action="{{ route('password.custom-forgot.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <div class="input-group-apms">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email Address" value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <button type="submit" class="btn btn-auth">
                    <i class="fas fa-paper-plane mr-1"></i> Kirim / Cek Status
                </button>
            </form>
            @endif

            <div class="auth-footer">
                <a href="{{ route('login') }}" class="auth-link font-weight-bold" style="color: var(--primary-color);">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
                </a>
            </div>

            <div class="mt-3 pt-2 text-center" style="border-top: 1px solid #e0e0e0;">
                <p class="mb-0" style="font-size:0.7rem;color:#999;">
                    &copy; {{ date('Y') }} Ashar Parfum Management System.
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
