<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IdleTimeoutMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $timeout = config('session.lifetime', 30);
            $lastActivity = session('last_activity');
            $now = now()->timestamp;

            if ($lastActivity && ($now - $lastActivity) > ($timeout * 60)) {
                Auth::logout();
                session()->flush();
                return redirect()->route('login')->with('status', 'Sesi Anda telah berakhir karena tidak ada aktivitas silakan masuk lagi.');
            }

            session(['last_activity' => $now]);
        }

        return $next($request);
    }
}
