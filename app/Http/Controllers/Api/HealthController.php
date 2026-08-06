<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [];
        $status = 'healthy';

        // Database
        try {
            DB::connection()->getPdo();
            $checks['database'] = ['status' => 'up'];
        } catch (\Exception $e) {
            $status = 'degraded';
            $checks['database'] = [
                'status' => 'down',
                'error'  => app()->isProduction() ? 'Connection failed.' : $e->getMessage(),
            ];
        }

        // Cache
        try {
            $key = 'health-check-' . now()->timestamp;
            Cache::store('file')->put($key, true, 10);
            Cache::store('file')->forget($key);
            $checks['cache'] = ['status' => 'up'];
        } catch (\Exception $e) {
            $status = 'degraded';
            $checks['cache'] = [
                'status' => 'down',
                'error'  => app()->isProduction() ? 'Cache unavailable.' : $e->getMessage(),
            ];
        }

        // Storage (writable check)
        try {
            Storage::disk('local')->put('health-check.tmp', 'ok');
            Storage::disk('local')->delete('health-check.tmp');
            $checks['storage'] = ['status' => 'up'];
        } catch (\Exception $e) {
            $status = 'degraded';
            $checks['storage'] = [
                'status' => 'down',
                'error'  => app()->isProduction() ? 'Storage unavailable.' : $e->getMessage(),
            ];
        }

        // Application info — never expose debug: true in production
        $checks['app'] = [
            'status' => 'up',
            'env'    => app()->environment(),
            'debug'  => app()->isProduction() ? false : config('app.debug'),
        ];

        $httpCode = $status === 'healthy' ? 200 : 503;

        return response()->json([
            'status'    => $status,
            'timestamp' => now()->toIso8601String(),
            'checks'    => $checks,
        ], $httpCode);
    }
}
