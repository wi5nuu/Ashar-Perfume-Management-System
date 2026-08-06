<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
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
        $nonce = base64_encode(random_bytes(16));
        View::share('cspNonce', $nonce);

        $response = $next($request);

        // Apply base security headers to all environments
        foreach ($this->headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        // HSTS — production only (local dev has no valid cert)
        if (!app()->environment('local')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // CSP — all environments (report-only in local, enforced in production)
        $cspHeader = app()->environment('local')
            ? 'Content-Security-Policy-Report-Only'
            : 'Content-Security-Policy';
        $response->headers->set($cspHeader, $this->getCspPolicy($nonce));

        // Cache control — prevent sensitive pages from being cached by proxies.
        // Skip for file download responses (Excel/CSV/PDF) — overriding their headers
        // corrupts the file because browsers use Content-Disposition + Cache-Control
        // together to decide whether to buffer or stream the download.
        $contentDisposition = $response->headers->get('Content-Disposition', '');
        $isFileDownload = str_contains($contentDisposition, 'attachment');

        if (!$isFileDownload && ($request->is('admin/*') || $request->is('owner/*') || $request->is('settings/*'))) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        // Remove server fingerprinting headers
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }

    private function getCspPolicy(string $nonce): string
    {
        return "default-src 'self'; "
            . "script-src 'self' 'nonce-{$nonce}' 'strict-dynamic' https://code.jquery.com https://cdn.jsdelivr.net https://cdn.datatables.net https://cdnjs.cloudflare.com; "
            . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; "
            . "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; "
            . "img-src 'self' data: blob:; "
            . "connect-src 'self' https://*.pusher.com wss://*.pusher.com wss://" . config('reverb.host', 'localhost') . ":443; "
            . "frame-src 'none'; "
            . "object-src 'none'; "
            . "base-uri 'self'; "
            . "form-action 'self'";
    }
}
