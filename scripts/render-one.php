<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

$app = require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

// Rethrow instead of rendering whoops, so the real error is readable.
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    fn ($app) => new class($app) extends Illuminate\Foundation\Exceptions\Handler
    {
        public function render($request, Throwable $e)
        {
            throw $e;
        }
    }
);

$uri = $argv[1] ?? '/';

$method = $argv[2] ?? 'GET';
$params = $argv[3] ?? null;

// A POST needs a live CSRF token, so prime the session first.
$prime = $kernel->handle(Request::create('/', 'GET'));

$session = [];

foreach ($prime->headers->getCookies() as $cookie) {
    $session[$cookie->getName()] = $cookie->getValue();
}

if ($method === 'POST') {
    preg_match('/name="_token"\s+value="([a-zA-Z0-9]+)"/', (string) $prime->getContent(), $m);

    $params = ['_token' => $m[1] ?? ''];

    foreach (['email' => 3, 'password' => 4] as $field => $position) {
        if (isset($argv[$position])) {
            $params[$field] = $argv[$position];
        }
    }
}

try {
    $response = $kernel->handle(Request::create($uri, $method, $params, $session, [], [
        'HTTP_ACCEPT' => 'text/html',
    ]));

    echo "STATUS: " . $response->getStatusCode() . "\n";
    echo "BYTES: " . strlen((string) $response->getContent()) . "\n";
} catch (Throwable $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
    echo $e->getFile() . ':' . $e->getLine() . "\n\n";

    foreach (array_slice($e->getTrace(), 0, 12) as $frame) {
        echo '  ' . ($frame['file'] ?? '?') . ':' . ($frame['line'] ?? '?') . ' '
            . ($frame['class'] ?? '') . ($frame['type'] ?? '') . ($frame['function'] ?? '') . "\n";
    }

    exit(1);
}