<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectWholesaleCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'wholesale_customer') {
            $path = $request->path();

            // Only allow paths under /wholesale-customer/ (with trailing slash)
            // EXACT match /wholesale-customer alone is also allowed
            // This prevents matching /wholesale-customers (admin route)
            $isWholesalePath = str_starts_with($path, 'wholesale-customer/')
                            || $path === 'wholesale-customer';

            if (!$isWholesalePath) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Halaman tidak ditemukan.'
                    ], 404);
                }

                return redirect()->route('wholesale.customer.dashboard');
            }
        }

        return $next($request);
    }
}
