<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SessionSecurityMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->is_locked && $user->locked_until && now()->lessThan($user->locked_until)) {
                Auth::logoutCurrentDevice();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->withErrors(['email' => 'Sesi Anda telah diakhiri karena akun dikunci.']);
            }

            if ($user->requires_password_change) {
                if (!$request->routeIs('password.change*') && !$request->routeIs('logout')) {
                    return redirect()->route('password.change.form')
                        ->with('warning', 'Anda harus mengubah kata sandi sebelum melanjutkan.');
                }
            }

            if (config('security.two_factor.enforced', false)) {
                if (!$user->two_factor_secret && !$request->routeIs('admin.security.two-factor*') && !$request->routeIs('logout') && !$request->routeIs('password.change*')) {
                    return redirect()->route('admin.security.two-factor')
                        ->with('warning', 'Anda harus mengaktifkan Autentikasi Dua Faktor (2FA) sebelum melanjutkan.');
                }
            }

            if ($user->last_login_ip && $user->last_login_ip !== $request->ip()) {
                Log::warning("IP address changed for user {$user->id} from {$user->last_login_ip} to {$request->ip()}");
            }

            session(['last_activity' => now()->timestamp]);
        }

        return $next($request);
    }
}
