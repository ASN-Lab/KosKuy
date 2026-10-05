<?php

/*
|--------------------------------------------------------------------------
| Route smoke test
|--------------------------------------------------------------------------
| Dispatches requests straight through the HTTP kernel so every route's
| controller + Blade templates are exercised without binding a socket.
|
| Usage: php storage/app/smoke-routes.php
*/

use App\Models\Category;
use App\Models\KosListing;
use App\Models\ThriftProduct;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

$app = require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$failures = [];
$checks = 0;

/**
 * @return array{status: int, body: string, cookies: array<string, string>}
 */
function dispatch(string $method, string $uri, array $params = [], array $cookies = [], array $server = [], ?string $content = null): array
{
    global $app;

    $kernel = $app->make(Kernel::class);

    $request = Request::create($uri, $method, $params, $cookies, [], array_merge([
        'HTTP_ACCEPT' => 'text/html',
    ], $server), $content);

    $response = $kernel->handle($request);

    $set = [];

    foreach ($response->headers->getCookies() as $cookie) {
        $set[$cookie->getName()] = $cookie->getValue();
    }

    return ['status' => $response->getStatusCode(), 'body' => (string) $response->getContent(), 'cookies' => $set];
}

/**
 * Pull the CSRF token out of a rendered form so POSTs are not rejected.
 */
function csrfToken(string $html): string
{
    if (preg_match('/name="_token"\s+value="([a-zA-Z0-9]+)"/', $html, $m)) {
        return $m[1];
    }

    if (preg_match('/name="csrf-token"\s+content="([a-zA-Z0-9]+)"/', $html, $m)) {
        return $m[1];
    }

    return '';
}

function check(string $label, string $method, string $uri, array $params = [], array $expect = [], array $server = [], ?string $content = null): void
{
    global $failures, $checks;

    $checks++;

    $result = dispatch($method, $uri, $params, [], $server, $content);
    $status = $result['status'];

    $expected = $expect ?: [200];

    $ok = in_array($status, $expected, true);

    $detail = '';

    if (! $ok) {
        $detail = 'HTTP ' . $status;
    } else {
        // Surface Blade/PHP failures that still returned a 200-ish page.
        foreach (['Undefined variable', 'Undefined array key', 'ErrorException', 'Whoops', 'Non-static method'] as $needle) {
            if (str_contains($result['body'], $needle)) {
                $ok = false;
                $detail = 'page contains "' . $needle . '"';
                break;
            }
        }
    }

    if ($ok) {
        echo "  OK   {$label} [{$status}]\n";
    } else {
        echo "  FAIL {$label} [{$status}] {$detail}\n";
        $failures[] = $label . ' ' . $detail;
    }
}

echo "Seeding fixtures if needed...\n";

require_once __DIR__.'/smoke-fixtures.php';

[$user, $created] = smoke_fixtures();

$kos = KosListing::first();
$thrift = ThriftProduct::first();

echo "\nPublic routes:\n";

check('home', 'GET', '/');
check('kos index', 'GET', '/kos');
check('kos index + search', 'GET', '/kos?search=Contoh');
check('kos index + gender', 'GET', '/kos?gender_type=putri');
check('kos index + price', 'GET', '/kos?min_price=500000&max_price=2000000');
check('kos index + facilities', 'GET', '/kos?facilities[]=WiFi');
check('kos index + sort', 'GET', '/kos?sort=termurah');
check('kos index + combined', 'GET', '/kos?search=Contoh&gender_type=putri&min_price=500000&sort=termurah&facilities[]=WiFi');
check('kos show', 'GET', '/kos/' . $kos->id);
check('kos show 404', 'GET', '/kos/999999', [], [404]);
check('thrift index', 'GET', '/thrift');
check('thrift index + search', 'GET', '/thrift?search=Barang');
check('thrift index + category', 'GET', '/thrift?category_id=' . Category::first()->id);
check('thrift index + available', 'GET', '/thrift?available=1&min_price=10000');
check('thrift show', 'GET', '/thrift/' . $thrift->id);
check('thrift show 404', 'GET', '/thrift/999999', [], [404]);
check('flyer', 'GET', '/flyer');
check('login page', 'GET', '/login');
check('register page', 'GET', '/register');
check('dashboard redirects', 'GET', '/dashboard', [], [302]);

echo "\nAPI routes:\n";

$json = ['HTTP_ACCEPT' => 'application/json'];
$apiCases = [
    ['api categories', '/api/v1/categories', [200], 'success'],
    ['api kos', '/api/v1/kos', [200], 'Daftar kos berhasil diambil'],
    ['api kos + filters', '/api/v1/kos?gender_type=putri&min_price=500000', [200], 'Daftar kos berhasil diambil'],
    ['api kos detail', '/api/v1/kos/' . $kos->id, [200], 'Detail kos berhasil diambil'],
    ['api kos detail 404', '/api/v1/kos/999999', [404], 'Kos tidak ditemukan'],
    ['api thrift', '/api/v1/thrift', [200], 'Daftar produk thrift berhasil diambil'],
    ['api thrift + search', '/api/v1/thrift?search=a', [200], 'Daftar produk thrift berhasil diambil'],
    ['api thrift detail', '/api/v1/thrift/' . $thrift->id, [200], 'Detail produk thrift berhasil diambil'],
    ['api thrift detail 404', '/api/v1/thrift/999999', [404], 'Produk thrift tidak ditemukan'],
];

foreach ($apiCases as [$label, $uri, $expect, $needle]) {
    $result = dispatch('GET', $uri, [], [], $json);

    $ok = in_array($result['status'], $expect, true) && str_contains($result['body'], $needle);

    $checks++;
    echo $ok
        ? "  OK   {$label} [{$result['status']}]\n"
        : "  FAIL {$label} [{$result['status']}] missing \"{$needle}\" in: " . substr($result['body'], 0, 90) . "\n";

    if (! $ok) {
        $failures[] = $label;
    }
}

/**
 * Re-read the session cookie + CSRF token after a state-changing request.
 * Laravel rotates the session on login/logout, so a token captured earlier
 * is legitimately invalid by the next POST.
 *
 * @return array{cookies: array<string, string>, token: string}
 */
function refreshSession(array $cookies, string $uri = '/'): array
{
    $page = dispatch('GET', $uri, [], $cookies);

    return ['cookies' => array_merge($cookies, $page['cookies']), 'token' => csrfToken($page['body'])];
}

echo "\nAuth flow:\n";

$loginPage = dispatch('GET', '/login');
$cookies = $loginPage['cookies'];
$token = csrfToken($loginPage['body']);

if ($token === '') {
    echo "  FAIL could not find CSRF token on /login\n";
    $failures[] = 'csrf token missing';
}

// Wrong password must bounce back with an error, never authenticate.
$badLogin = dispatch('POST', '/login', [
    '_token' => $token,
    'email' => 'smoke@koskuy.test',
    'password' => 'wrong-password',
], $cookies);

$checks++;

if ($badLogin['status'] === 302 && ! Auth::check()) {
    echo "  OK   login rejects bad password [302]\n";
} else {
    echo "  FAIL login rejects bad password [{$badLogin['status']}] authed: " . (Auth::check() ? 'yes' : 'no') . "\n";
    $failures[] = 'login rejects bad password';
}

$session = refreshSession($cookies, '/login');
$cookies = $session['cookies'];
$token = $session['token'];

$login = dispatch('POST', '/login', [
    '_token' => $token,
    'email' => 'smoke@koskuy.test',
    'password' => 'password123',
], $cookies);

if ($login['status'] === 302) {
    echo "  OK   login post [302]\n";
    $checks++;
} else {
    echo "  FAIL login post [{$login['status']}]\n";
    $failures[] = 'login post';
}

$dashboard = dispatch('GET', '/dashboard', [], array_merge($cookies, $login['cookies']));

if ($dashboard['status'] === 200 && ! str_contains($dashboard['body'], 'Undefined variable')) {
    echo "  OK   dashboard authenticated [200]\n";
    $checks++;
} else {
    echo "  FAIL dashboard authenticated [{$dashboard['status']}]\n";
    $failures[] = 'dashboard authenticated';
}

$session = refreshSession(array_merge($cookies, $login['cookies']), '/dashboard');
$logout = dispatch('POST', '/logout', ['_token' => $session['token']], $session['cookies']);

if ($logout['status'] === 302) {
    echo "  OK   logout post [302]\n";
    $checks++;
} else {
    echo "  FAIL logout post [{$logout['status']}]\n";
    $failures[] = 'logout post';
}

Auth::logout();

$afterLogout = dispatch('GET', '/dashboard', [], array_merge($session['cookies'], $logout['cookies']));

if ($afterLogout['status'] === 302) {
    echo "  OK   dashboard protected after logout [302]\n";
    $checks++;
} else {
    echo "  FAIL dashboard still open after logout [{$afterLogout['status']}]\n";
    $failures[] = 'dashboard protected after logout';
}

echo "\nRegister flow:\n";

$registerPage = dispatch('GET', '/register');
$registerCookies = $registerPage['cookies'];
$registerToken = csrfToken($registerPage['body']);

$email = 'smoke+' . time() . '@koskuy.test';

$register = dispatch('POST', '/register', [
    '_token' => $registerToken,
    'name' => 'Pengguna Uji',
    'email' => $email,
    'phone' => '081298765432',
    'role' => 'student',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'terms' => '1',
], $registerCookies);

if ($register['status'] === 302 && App\Models\User::where('email', $email)->exists()) {
    echo "  OK   register post [302] user created\n";
    $checks++;
} else {
    echo "  FAIL register post [{$register['status']}] user created: "
        . (App\Models\User::where('email', $email)->exists() ? 'yes' : 'no') . "\n";
    $failures[] = 'register post';
}

// Registering again in the same browser must be refused on the duplicate email.
$regSession = refreshSession($registerCookies, '/');

$duplicate = dispatch('POST', '/register', [
    '_token' => $regSession['token'],
    'name' => 'Ganda',
    'email' => $email,
    'phone' => '081298765432',
    'role' => 'student',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'terms' => '1',
], $regSession['cookies']);

if ($duplicate['status'] === 302 && App\Models\User::where('email', $email)->count() === 1) {
    echo "  OK   register rejects duplicate email [302]\n";
    $checks++;
} else {
    echo "  FAIL register rejects duplicate email [{$duplicate['status']}] count: "
        . App\Models\User::where('email', $email)->count() . "\n";
    $failures[] = 'register duplicate email';
}

// Terms must be explicitly accepted.
$termsEmail = 'noterms+' . time() . '@koskuy.test';

$noTerms = dispatch('POST', '/register', [
    '_token' => $regSession['token'],
    'name' => 'Tanpa Syarat',
    'email' => $termsEmail,
    'phone' => '081298765432',
    'role' => 'student',
    'password' => 'password123',
    'password_confirmation' => 'password123',
], $regSession['cookies']);

if ($noTerms['status'] === 302 && ! App\Models\User::where('email', $termsEmail)->exists()) {
    echo "  OK   register requires terms [302]\n";
    $checks++;
} else {
    echo "  FAIL register requires terms [{$noTerms['status']}] created: "
        . (App\Models\User::where('email', $termsEmail)->exists() ? 'yes' : 'no') . "\n";
    $failures[] = 'register requires terms';
}

// Confirmation must match.
$mismatchEmail = 'mismatch+' . time() . '@koskuy.test';

$mismatch = dispatch('POST', '/register', [
    '_token' => $regSession['token'],
    'name' => 'Tidak Cocok',
    'email' => $mismatchEmail,
    'phone' => '081298765432',
    'role' => 'student',
    'password' => 'password123',
    'password_confirmation' => 'password456',
    'terms' => '1',
], $regSession['cookies']);

if ($mismatch['status'] === 302 && ! App\Models\User::where('email', $mismatchEmail)->exists()) {
    echo "  OK   register requires matching confirmation [302]\n";
    $checks++;
} else {
    echo "  FAIL register requires matching confirmation [{$mismatch['status']}]\n";
    $failures[] = 'register confirmation mismatch';
}

// Clean up the throwaway accounts this run created.
App\Models\User::where('email', 'like', 'smoke+%@koskuy.test')->delete();
App\Models\User::where('email', 'like', 'noterms+%@koskuy.test')->delete();
App\Models\User::where('email', 'like', 'mismatch+%@koskuy.test')->delete();

echo "\nNewsletter:\n";

$footerPage = dispatch('GET', '/');
$footerToken = csrfToken($footerPage['body']);
$footerCookies = $footerPage['cookies'];

// NOTE: the session store is a container singleton, so its attributes survive
// between the requests dispatched inside this one PHP process. Any POST that
// exercises CSRF must therefore carry the cookie that minted its token,
// exactly like a real browser does -- otherwise the check passes for the wrong
// reason and proves nothing.
$newsletterCases = [
    ['subscribe ok', 'test@koskuy.test', [302]],
    ['subscribe invalid redirects', 'not-an-email', [302]],
    ['subscribe duplicate ok', 'test@koskuy.test', [302]],
];

foreach ($newsletterCases as [$label, $email, $expect]) {
    $result = dispatch('POST', '/newsletter', [
        '_token' => $footerToken,
        'email' => $email,
    ], $footerCookies);

    $ok = in_array($result['status'], $expect, true);

    $checks++;
    echo $ok
        ? "  OK   {$label} [{$result['status']}]\n"
        : "  FAIL {$label} [{$result['status']}] expected " . implode('/', $expect) . "\n";

    if (! $ok) {
        $failures[] = $label;
    }
}

// A token that no session ever issued must be rejected outright.
$forged = dispatch('POST', '/newsletter', [
    '_token' => str_repeat('z', 40),
    'email' => 'test@koskuy.test',
], $footerCookies);

$checks++;

if ($forged['status'] === 419) {
    echo "  OK   subscribe rejects forged csrf token [419]\n";
} else {
    echo "  FAIL subscribe accepted a forged csrf token [{$forged['status']}]\n";
    $failures[] = 'subscribe csrf rejection';
}

// The same endpoint must answer JSON callers with JSON.
$jsonResult = dispatch('POST', '/newsletter', [], $footerCookies, [
    'HTTP_ACCEPT' => 'application/json',
    'CONTENT_TYPE' => 'application/json',
], json_encode(['_token' => $footerToken, 'email' => 'json@koskuy.test']));

$checks++;

if ($jsonResult['status'] === 200 && str_contains($jsonResult['body'], 'Berhasil')) {
    echo "  OK   subscribe json [200]\n";
} else {
    echo "  FAIL subscribe json [{$jsonResult['status']}] " . substr($jsonResult['body'], 0, 80) . "\n";
    $failures[] = 'subscribe json';
}

$created['newsletter'][] = 'test@koskuy.test';
$created['newsletter'][] = 'json@koskuy.test';

echo "\nTeardown:\n";

$teardown = smoke_teardown($created);

echo $teardown['ok']
    ? "  OK   {$teardown['message']}\n"
    : "  WARN {$teardown['message']}\n";

echo "\n----------------------------------------\n";
echo "Checks: {$checks}\n";

if ($failures) {
    echo "FAILURES (" . count($failures) . "):\n";

    foreach ($failures as $failure) {
        echo "  - {$failure}\n";
    }

    exit(1);
}

echo "ALL GREEN\n";