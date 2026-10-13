<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        // A logout POST can arrive with a token whose session is already gone —
        // a double-tap on mobile, or a page left open past the session lifetime.
        // The user is effectively logged out already, so send them home instead
        // of the raw "419 Page Expired" screen.
        //
        // Note: by the time render() callbacks run, the handler has already
        // mapped Illuminate\Session\TokenMismatchException to a Symfony
        // HttpException(419) (see Handler::prepareException), so a callback
        // typed on TokenMismatchException would never match. Type on
        // HttpException and filter on the status instead.
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            if ($request->isMethod('post') && ($request->routeIs('logout') || $request->is('logout'))) {
                return redirect()
                    ->route('home')
                    ->with('success', 'Kamu sudah keluar. Sampai jumpa lagi.');
            }

            return null;
        });
    })->create();
