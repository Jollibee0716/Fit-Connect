<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        $middleware->appendToGroup('web', \App\Http\Middleware\SetAuditUser::class);

        // Xendit calls this server-to-server and cannot send a CSRF token.
        // The controller authenticates it with the x-callback-token header instead.
        $middleware->validateCsrfTokens(except: ['webhooks/xendit']);

        // Behind a host/proxy/Cloudflare, trust forwarded headers so https:// URLs are generated.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();