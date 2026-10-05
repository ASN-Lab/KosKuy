<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
| Presentation helpers for the Blade storefront (rupiah formatting, WhatsApp
| deep links, facility icon mapping, and so on). Loaded here rather than through
| composer's `files` autoloader so the helpers are available to every entry
| point — web, artisan, queue workers, and tests — without an extra
| `composer dump-autoload` step. Every function is guarded, so re-declaring is
| harmless.
*/
require_once __DIR__.'/../app/Support/helpers.php';

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
