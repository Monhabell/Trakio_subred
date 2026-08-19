<?php

use App\Http\Middleware\ExcludeFromCsrfMiddleware;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RedirectIfSessionExpired;
use App\Http\Middleware\UserDataMiddleware;
use App\Http\Middleware\DisableCache;
use App\Http\Middleware\AdminGesiMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(UserDataMiddleware::class);
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(RedirectIfAuthenticated::class);
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(RedirectIfSessionExpired::class);
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(ExcludeFromCsrfMiddleware::class);
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(AdminGesiMiddleware::class);
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(DisableCache::class);
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustHosts(at: ['laravel.test']);
    })

    // ->withMiddleware(function (Middleware $middleware) {
    //     $middleware->validateCsrfTokens(except: [
    //         'https://www.trakio.pro/areas',
    //         'https://www.trakio.pro/areas/*',
    //         'http://127.0.0.1:8000/api/guardar-colegio',
    //     ]);
    // })

    ->withMiddleware(function (Middleware $middleware) {
    $middleware->validateCsrfTokens(except: [
        'areas',
        'areas/*',
        'api/guardar-colegio',
        'validator-builder/mp/webhook',
    ]);
})
   
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);
    })


    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();