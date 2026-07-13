<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = []; $status = 'healthy';

        try { DB::connection()->getPdo(); $checks['database'] = ['status' => 'up']; }
        catch (\Exception $e) { $status = 'degraded'; $checks['database'] = ['status' => 'down', 'error' => $e->getMessage()]; }

        try { Cache::store('file')->get('health-test'); $checks['cache'] = ['status' => 'up']; }
        catch (\Exception $e) { $checks['cache'] = ['status' => 'down', 'error' => $e->getMessage()]; }

        $checks['app'] = ['status' => 'up', 'env' => app()->environment(), 'debug' => config('app.debug')];

        return response()->json(['status' => $status, 'timestamp' => now()->toIso8601String(), 'checks' => $checks],
            $status === 'healthy' ? 200 : 503);
    }
}
