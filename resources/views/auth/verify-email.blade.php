<x-guest-layout>
    <div class="auth-card">
        <div class="auth-header">
            <span class="auth-logo">
                <i class="fas fa-envelope"></i>Verifikasi
            </span>
            <p class="auth-subtitle">Verifikasi Alamat Email</p>
        </div>

        <div class="auth-body">
            <div class="mb-4 text-muted text-center px-2" style="font-size: 0.85rem;">
                Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi email Anda dengan mengklik link yang telah kami kirimkan.
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success mb-4 text-center">
                    Link verifikasi baru telah dikirim ke email Anda.
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-auth mb-3">
                    Kirim Ulang Email Verifikasi <i class="fas fa-paper-plane ml-2"></i>
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-auth" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%); box-shadow: 0 10px 20px rgba(108,117,125,0.2);">
                    Keluar <i class="fas fa-sign-out-alt ml-2"></i>
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
