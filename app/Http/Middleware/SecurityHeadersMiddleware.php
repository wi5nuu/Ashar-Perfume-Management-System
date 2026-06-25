<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    private array $headers = [
        'X-Frame-Options' => 'SAMEORIGIN',
        'X-Content-Type-Options' => 'nosniff',
        'X-XSS-Protection' => '1; mode=block',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        foreach ($this->headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        if (!app()->environment('local')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
            $response->headers->set('Content-Security-Policy', $this->getCspPolicy());
        }

        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }

    private function getCspPolicy(): string
    {
        $nonce = base64_encode(random_bytes(16));
        return "default-src 'self'; "
            . "script-src 'self' 'nonce-{$nonce}' https://code.jquery.com https://cdn.jsdelivr.net https://cdn.datatables.net https://cdnjs.cloudflare.com; "
            . "style-src 'self' 'nonce-{$nonce}' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; "
            . "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; "
            . "img-src 'self' data: blob:; "
            . "connect-src 'self' https://*.pusher.com wss://*.pusher.com; "
            . "frame-src 'none'; "
            . "object-src 'none'; "
            . "base-uri 'self'; "
            . "form-action 'self'";
    }
}
