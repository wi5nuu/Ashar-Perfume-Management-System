<?php

namespace App\Http\Middleware;

use App\Models\IpBlacklist;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class IpSecurityMiddleware
{
    private const MAX_ANONYMOUS_REQUESTS = 200;
    private const BLOCK_MINUTES = 60;

    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        if (IpBlacklist::isBlocked($ip)) {
            Log::warning("BLOCKED IP attempted access: {$ip}");
            return response()->view('errors.429', [], 429);
        }

        if (app()->environment('production') && config('security.maintenance.strict_admin_ip', false)) {
            $ipWhitelist = config('security.maintenance.ip_whitelist', ['127.0.0.1']);
            if ($request->is('admin/*') && !in_array($ip, $ipWhitelist)) {
                return response()->view('errors.403', [], 403);
            }
        }

        $cacheKey = "request_count:{$ip}";
        $count = (int) Cache::remember($cacheKey, now()->addMinute(), fn () => 0);
        $count++;
        Cache::put($cacheKey, $count, now()->addMinute());

        if ($count > self::MAX_ANONYMOUS_REQUESTS) {
            IpBlacklist::block($ip, 'rate_limit_exceeded', self::BLOCK_MINUTES);
            Log::warning("IP AUTO-BLOCKED for rate limit: {$ip} ({$count} requests/min)");
            return response()->view('errors.429', [], 429);
        }

        return $next($request);
    }
}
