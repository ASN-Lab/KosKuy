<?php

namespace Tests\Feature;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Session\TokenMismatchException;
use Tests\TestCase;

/**
 * The branded error pages, and the special case where a logout POST arrives
 * with a stale/absent CSRF token (a double-tap on mobile, or a page left open
 * past the session lifetime). That should quietly land the user back home
 * instead of showing the raw 419 screen.
 */
class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_route_renders_the_branded_404_page(): void
    {
        config(['app.debug' => false]);

        $this->get('/halaman-yang-tidak-ada')
            ->assertNotFound()
            ->assertSee('Halaman tidak ditemukan')
            ->assertSee('Kembali ke beranda');
    }

    public function test_a_stale_logout_token_redirects_home_instead_of_419(): void
    {
        $response = $this->renderHttpException('/logout', 'logout', new TokenMismatchException('CSRF token mismatch.'));

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(route('home'), $response->headers->get('Location'));
    }

    public function test_a_stale_token_elsewhere_still_renders_the_branded_419_page(): void
    {
        config(['app.debug' => false]);

        $response = $this->renderHttpException('/newsletter', 'subscribe', new TokenMismatchException('CSRF token mismatch.'));

        $this->assertSame(419, $response->getStatusCode());
        $this->assertStringContainsString('Sesi kamu sudah berakhir', (string) $response->getContent());
    }

    /**
     * Hand the exception straight to the handler the way the HTTP kernel does,
     * with the request carrying a resolved, named route so `routeIs()` works.
     * CSRF is skipped in the test environment, so a real POST cannot trigger
     * this path.
     */
    private function renderHttpException(string $uri, string $routeName, \Throwable $exception): \Symfony\Component\HttpFoundation\Response
    {
        $route = new Route(['POST'], $uri, fn () => null);
        $route->name($routeName);

        $request = Request::create($uri, 'POST');
        $request->setRouteResolver(fn () => $route);

        return $this->app->make(ExceptionHandler::class)->render($request, $exception);
    }
}
