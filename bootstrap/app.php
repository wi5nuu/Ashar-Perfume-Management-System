<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            require __DIR__.'/../routes/auth.php';
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'            => \App\Http\Middleware\RoleMiddleware::class,
            'https'           => \App\Http\Middleware\HttpsProtocolMiddleware::class,
            'security'        => \App\Http\Middleware\SecurityHeadersMiddleware::class,
            'throttle.login'  => \App\Http\Middleware\LoginThrottleMiddleware::class,
            'ip.security'     => \App\Http\Middleware\IpSecurityMiddleware::class,
        ]);

        $middleware->appendToGroup('web', \App\Http\Middleware\HttpsProtocolMiddleware::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\SecurityHeadersMiddleware::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\SessionSecurityMiddleware::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\LoginThrottleMiddleware::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\IpSecurityMiddleware::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\IdleTimeoutMiddleware::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\InputSanitizerMiddleware::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\RedirectWholesaleCustomer::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            return response()->view('errors.404', [], 404);
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e) {
            return response()->view('errors.403', [], 403);
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $status = $e->getStatusCode();
            if (in_array($status, [500, 503, 429], true)) {
                return response()->view("errors.{$status}", [], $status);
            }
        });
    })->create();
