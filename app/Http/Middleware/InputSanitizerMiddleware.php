<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InputSanitizerMiddleware
{
    private array $except = ['password', 'password_confirmation', 'current_password'];

    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('get')) {
            return $next($request);
        }

        $input = $request->all();
        $sanitized = $this->sanitize($input);
        $request->replace($sanitized);

        return $next($request);
    }

    private function sanitize(array $input): array
    {
        foreach ($input as $key => $value) {
            if (in_array($key, $this->except, true)) continue;

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
        $value = str_replace(["\0", "\x00", "\x1A", "\r"], '', $value);

        return strip_tags($value);
    }
}
