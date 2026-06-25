<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpsProtocolMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->secure() || app()->environment('production')) {
            if (!$request->secure() && app()->environment('production')) {
                return redirect()->secure($request->getRequestUri(), 301);
            }

            $response = $next($request);
        }

        $response = $next($request);

        return $response;
    }
}
