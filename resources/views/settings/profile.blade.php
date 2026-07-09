@extends('layouts.app')

@section('title', 'Manajemen Akun & Keamanan')

@section('content')
<div class="container-fluid pt-2">
    <div class="row">
        <!-- Sidebar Profile info -->
        <div class="col-xl-3 col-lg-4 mb-4">
            <div class="card card-apms border-0 shadow-sm overflow-hidden h-100">
                <div class="card-body text-center p-4">
                    <div class="position-relative d-inline-block mb-3">
                        <div class="avatar-circle bg-faint-primary d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2.5rem; border: 4px solid #fff; box-shadow: 0 0 20px rgba(0,0,0,0.05);">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <span class="badge badge-success border border-white position-absolute" style="bottom: 5px; right: 5px; border-radius: 50%; width: 15px; height: 15px; padding: 0;">&nbsp;</span>
                    </div>
                    <h5 class="font-weight-bold mb-1 text-dark-apms">{{ $user->name }}</h5>
                    <p class="text-primary-apms font-weight-bold text-uppercase mb-3" style="font-size: 0.7rem; letter-spacing: 0.05rem;">
                        <i class="fas fa-shield-alt mr-1"></i> {{ $user->role }}
                    </p>
                    
                    <div class="border-top pt-3 text-left">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Status Akun:</span>
                            <span class="badge badge-success-faint text-success px-2 py-1">Aktif & Aman</span>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <span class="text-muted small">Terdaftar:</span>
                            <span class="text-dark small font-weight-bold">{{ $user->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light p-0 border-0">
                    <a href="{{ route('settings.index') }}" class="btn btn-link btn-block text-muted py-3 text-sm font-weight-bold">
                        <i class="fas fa-chevron-left mr-2"></i> Pengaturan Global
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Settings Area -->
        <div class="col-xl-9 col-lg-8">
            <!-- Part 1: Basic Info -->
            <div class="card card-apms border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 font-weight-bold text-dark-apms">
                        <i class="fas fa-user-edit text-primary mr-2"></i> Informasi Personal
                    </h6>
                </div>
                <form action="{{ route('settings.profile.update') }}" method="POST">
                    @csrf
                    <div class="card-body py-2">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control form-control-sm" value="{{ old('name', $user->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted">Alamat Email (Akun)</label>
                                    <input type="email" name="email" class="form-control form-control-sm" value="{{ old('email', $user->email) }}" {{ !$user->isOwner() ? 'readonly' : '' }} required>
                                    @if(!$user->isOwner())
                                    <small class="text-muted">Email hanya dapat diubah oleh Owner</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted">WhatsApp / Telepon</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i class="fab fa-whatsapp"></i></span>
                                        </div>
                                        <input type="text" name="phone" class="form-control form-control-sm border-left-0" value="{{ old('phone', $user->phone) }}" placeholder="6281xxx">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top py-3 text-right">
                        <button type="submit" class="btn btn-primary-apms btn-sm px-4 shadow-sm">
                            <i class="fas fa-check mr-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <div class="row">
                <!-- Part 2: Security & Password -->
                <div class="col-md-7">
                    <div class="card card-apms border-0 shadow-sm mb-4 h-100">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="mb-0 font-weight-bold text-danger">
                                <i class="fas fa-key mr-2"></i> Proteksi & Akses (Update Password)
                            </h6>
                        </div>
                        @if($user->isOwner())
                        <form action="{{ route('settings.password.update') }}" method="POST">
                            @csrf
                            <div class="card-body py-2">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted">Password Saat Ini</label>
                                    <input type="password" name="current_password" class="form-control form-control-sm" required placeholder="Konfirmasi identitas anda">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted">Password Baru</label>
                                    <input type="password" name="password" class="form-control form-control-sm" required placeholder="Minimal 8 karakter">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted">Ulangi Password Baru</label>
                                    <input type="password" name="password_confirmation" class="form-control form-control-sm" required>
                                </div>
                                
                                <div class="alert bg-light border-0 p-2 mb-0 mt-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-shield-alt text-warning mr-3 fa-lg"></i>
                                        <div class="small">
                                            Gunakan kombinasi simbol, angka, dan huruf untuk keamanan maksimal.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top py-3 text-right">
                                <button type="submit" class="btn btn-warning btn-sm px-4 font-weight-bold text-dark shadow-sm">
                                    <i class="fas fa-sync-alt mr-1"></i> Perbarui Kredensial
                                </button>
                            </div>
                        </form>
                        @else
                        <div class="card-body py-4 text-center">
                            <i class="fas fa-lock text-muted fa-3x mb-3 d-block"></i>
                            <h6 class="font-weight-bold text-muted">Password Hanya Dikelola oleh Owner</h6>
                            <p class="small text-muted mb-3">Jika Anda lupa password, kirim permintaan reset ke Owner.</p>
                            <form action="{{ route('settings.password.reset-request') }}" method="POST">
                                @csrf
                                <div class="form-group mb-2">
                                    <input type="text" name="notes" class="form-control form-control-sm" placeholder="Alasan (opsional)" maxlength="500">
                                </div>
                                <button type="submit" class="btn btn-warning btn-sm px-4">
                                    <i class="fas fa-paper-plane mr-1"></i> Minta Reset Password
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Part 3: Activity/Security Status -->
                <div class="col-md-5">
                    <div class="card card-apms border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="mb-0 font-weight-bold text-dark-apms">
                                <i class="fas fa-history mr-2 text-info"></i> Aktivitas Login Terakhir
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($loginActivities as $activity)
                                    @php
                                        $userAgent = $activity->user_agent;
                                        $browser = 'Unknown Browser';
                                        $os = 'Unknown OS';

                                        if (preg_match('/MSIE/i', $userAgent) && !preg_match('/Opera/i', $userAgent)) $browser = 'Internet Explorer';
                                        elseif (preg_match('/Firefox/i', $userAgent)) $browser = 'Firefox';
                                        elseif (preg_match('/Chrome/i', $userAgent)) $browser = 'Chrome';
                                        elseif (preg_match('/Safari/i', $userAgent)) $browser = 'Safari';
                                        elseif (preg_match('/Opera/i', $userAgent)) $browser = 'Opera';

                                        if (preg_match('/windows|win32/i', $userAgent)) $os = 'Windows';
                                        elseif (preg_match('/macintosh|mac os x/i', $userAgent)) $os = 'MacOS';
                                        elseif (preg_match('/linux/i', $userAgent)) $os = 'Linux';
                                        elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) $os = 'iOS';
                                        elseif (preg_match('/android/i', $userAgent)) $os = 'Android';

                                        $isCurrent = $loop->first && $activity->created_at->gt(now()->subMinutes(15)) && $activity->ip_address === request()->ip();
                                    @endphp
                                    <li class="list-group-item px-3 py-2 {{ $loop->last ? 'border-0' : 'border-faint' }}">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle avatar-sm {{ $isCurrent ? 'bg-faint-success text-success' : 'bg-faint-secondary text-muted' }} mr-3">
                                                <i class="fas {{ str_contains(strtolower($userAgent), 'mobile') ? 'fa-mobile-alt' : 'fa-desktop' }}"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0 font-weight-bold text-xs">{{ $isCurrent ? 'Sesi Aktif (Saat Ini)' : 'Login Berhasil' }}</h6>
                                                    <span class="smaller text-muted">{{ $activity->created_at->diffForHumans() }}</span>
                                                </div>
                                                <span class="text-muted smaller">{{ $os }} - {{ $browser }}</span>
                                                <div class="{{ $isCurrent ? 'text-success' : 'text-muted' }} smaller font-weight-bold">
                                                    IP: {{ $activity->ip_address }}
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item px-3 py-4 border-0 text-center text-muted">
                                        <i class="fas fa-info-circle mb-2 d-block fa-2x"></i>
                                        <span class="small">Belum ada riwayat aktivitas login.</span>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="card-footer bg-light-apms border-0 py-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle text-info mr-2"></i>
                                <span class="smaller text-muted">Jika anda mencurigai akses ilegal, segera ganti password anda.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
