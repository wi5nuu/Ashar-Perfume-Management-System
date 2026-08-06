<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InputSanitizerMiddleware
{
    /**
     * Fields excluded from sanitization (sensitive credential fields).
     */
    private array $except = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'secret',
    ];

    /**
     * Fields that allow rich text / HTML (e.g. description editors).
     */
    private array $allowHtml = [
        'description',
        'notes',
        'body',
        'content',
        'message',
    ];

    /**
     * SQL injection pattern detection (heuristic — Eloquent parameterized
     * queries are the primary defense; this is a defence-in-depth layer).
     */
    private array $sqlPatterns = [
        '/(\bUNION\b.*\bSELECT\b)/i',
        '/(\bSELECT\b.*\bFROM\b)/i',
        '/(\bDROP\b.*\bTABLE\b)/i',
        '/(\bINSERT\b.*\bINTO\b)/i',
        '/(\bDELETE\b.*\bFROM\b)/i',
        '/(\bUPDATE\b.*\bSET\b)/i',
        '/(\bEXEC\b|\bEXECUTE\b)/i',
        '/(--|\#|\/\*|\*\/)/i',
        '/\bOR\b\s+[\'\"]?[\w]+[\'\"]?\s*=\s*[\'\"]?[\w]+[\'\"]?/i',
        '/\bAND\b\s+[\'\"]?[\w]+[\'\"]?\s*=\s*[\'\"]?[\w]+[\'\"]?/i',
    ];

    /**
     * Path traversal patterns.
     */
    private array $pathPatterns = [
        '/\.\.\//',
        '/\.\.\\\\/',
        '/%2e%2e%2f/i',
        '/%2e%2e\//i',
    ];

    public function handle(Request $request, Closure $next)
    {
        // Skip GET/HEAD — no body to sanitize
        if ($request->isMethod('get') || $request->isMethod('head')) {
            return $next($request);
        }

        $input = $request->all();

        if ($this->detectSqlInjection($input, $request)) {
            Log::warning('SQL injection pattern detected', [
                'ip'     => $request->ip(),
                'url'    => $request->fullUrl(),
                'method' => $request->method(),
                'user'   => auth()->id(),
            ]);
            // Don't block — log and sanitize. Blocking is handled by Eloquent bindings.
        }

        $sanitized = $this->sanitize($input);
        $request->replace($sanitized);

        return $next($request);
    }

    private function sanitize(array $input): array
    {
        foreach ($input as $key => $value) {
            if (in_array($key, $this->except, true)) {
                continue;
            }

            if (is_array($value)) {
                $input[$key] = $this->sanitize($value);
            } elseif (is_string($value)) {
                $input[$key] = $this->cleanString($value, $key);
            }
        }
        return $input;
    }

    private function cleanString(string $value, string $key): string
    {
        // Remove null bytes and control characters
        $value = str_replace(["\0", "\x00", "\x1A", "\r"], '', $value);

        // Remove path traversal sequences
        foreach ($this->pathPatterns as $pattern) {
            $value = preg_replace($pattern, '', $value) ?? $value;
        }

        // Preserve HTML only for explicitly allowed fields
        if (in_array($key, $this->allowHtml, true)) {
            // strip_tags with allowed tags still passes through tag attributes (e.g. <a href="javascript:...">) —
            // use a whitelist approach that strips all attributes except safe ones.
            $value = strip_tags($value, '<p><br><b><i><u><ul><ol><li><strong><em><a>');
            // Remove event handlers and javascript: URIs from allowed tags
            $value = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $value);
            $value = preg_replace('/(<a[^>]*)\s+href\s*=\s*["\']?\s*javascript:[^"\'> ]*/i', '$1', $value);
            return $value ?? '';
        }

        return strip_tags($value);
    }

    private function detectSqlInjection(array $input, Request $request): bool
    {
        $flat = $this->flattenInput($input);
        foreach ($flat as $key => $value) {
            if (in_array($key, $this->except, true)) {
                continue;
            }
            if (!is_string($value)) {
                continue;
            }
            foreach ($this->sqlPatterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    return true;
                }
            }
        }
        return false;
    }

    private function flattenInput(array $input, string $prefix = ''): array
    {
        $result = [];
        foreach ($input as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : $key;
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenInput($value, $fullKey));
            } else {
                $result[$fullKey] = $value;
            }
        }
        return $result;
    }
}
