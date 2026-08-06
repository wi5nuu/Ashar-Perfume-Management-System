<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enterprise Audit Log Middleware
 *
 * Logs all state-changing requests (POST/PUT/PATCH/DELETE) with:
 * - Authenticated user identity
 * - IP address
 * - HTTP method + URL
 * - Sanitized payload summary (passwords redacted)
 * - Response status code
 *
 * Logs are written to storage/logs/audit.log (daily rotation).
 */
class AuditLogMiddleware
{
    /**
     * Fields that must never appear in audit logs.
     */
    private array $redacted = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'secret',
        'token',
        'api_key',
        'credit_card',
        'cvv',
    ];

    /**
     * Routes excluded from audit logging (high-frequency / low-risk).
     */
    private array $excludedRoutes = [
        'ai.chat',
        'owner.notifications.*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log state-changing methods
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $response;
        }

        // Skip excluded routes
        foreach ($this->excludedRoutes as $pattern) {
            if ($request->routeIs($pattern)) {
                return $response;
            }
        }

        $this->writeAuditLog($request, $response);

        return $response;
    }

    private function writeAuditLog(Request $request, Response $response): void
    {
        $user = auth()->user();

        $context = [
            'user_id'    => $user?->id,
            'user_name'  => $user?->name,
            'user_role'  => $user?->role,
            'ip'         => $request->ip(),
            'method'     => $request->method(),
            'url'        => $request->fullUrl(),
            'route'      => $request->route()?->getName(),
            'status'     => $response->getStatusCode(),
            'payload'    => $this->sanitizePayload($request->except(['_token', '_method'])),
            'user_agent' => substr($request->userAgent() ?? '', 0, 120),
        ];

        $level = $response->getStatusCode() >= 400 ? 'warning' : 'info';

        Log::channel('audit')->{$level}(
            "[AUDIT] {$request->method()} {$request->path()} → {$response->getStatusCode()}",
            $context
        );
    }

    private function sanitizePayload(array $data): array
    {
        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), $this->redacted, true)) {
                $data[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $data[$key] = $this->sanitizePayload($value);
            } elseif (is_string($value) && strlen($value) > 500) {
                // Truncate very long strings to avoid bloating logs
                $data[$key] = substr($value, 0, 500) . '…[truncated]';
            }
        }
        return $data;
    }
}
