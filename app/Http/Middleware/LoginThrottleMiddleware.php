<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\Security\ActivityMonitor;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class LoginThrottleMiddleware
{
    public function __construct(protected ActivityMonitor $monitor) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('login') && $request->isMethod('post')) {
            $identifier = $request->input('email') ?? $request->input('username') ?? $request->ip();
            $check = $this->monitor->checkLoginAttempt($identifier, $request->ip());

            if ($check['blocked']) {
                return back()->withErrors([
                    'email' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam 15 menit.',
                ])->onlyInput('email');
            }

            // Uniform check for locked accounts without revealing if account exists
            $email = $request->input('email');
            if ($email) {
                $user = User::where('email', $email)->first();
                if ($user && $user->is_locked && $user->locked_until && now()->lessThan($user->locked_until)) {
                    return back()->withErrors([
                        'email' => 'Silakan coba lagi nanti atau hubungi administrator.',
                    ])->onlyInput('email');
                }
            }
        }

        return $next($request);
    }
}
