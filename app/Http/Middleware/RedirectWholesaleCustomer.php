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
            if (!str_starts_with($request->path(), 'wholesale-customer')) {
                return redirect()->route('wholesale.customer.dashboard');
            }
        }
        return $next($request);
    }
}
