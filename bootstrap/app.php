<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . "/../routes/web.php",
        api: __DIR__ . "/../routes/api.php",
        commands: __DIR__ . "/../routes/console.php",
        health: "/up",
        then: function () {
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(__DIR__ . '/../routes/finance.php');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            "role" => \Spatie\Permission\Middleware\RoleMiddleware::class,
            "permission" =>
                \Spatie\Permission\Middleware\PermissionMiddleware::class,
            "must.change.pw" => \App\Http\Middleware\EnsureMustChangePw::class,
            "finance.feature" => \App\Http\Middleware\CheckFinanceFeature::class,
            "feature" => \App\Http\Middleware\CheckFeature::class,
            "kantin.device" => \App\Http\Middleware\AuthenticateKantinDevice::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
