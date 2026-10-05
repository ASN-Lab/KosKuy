<?php

/*
|--------------------------------------------------------------------------
| Rendered HTML audit
|--------------------------------------------------------------------------
| Renders each public page and looks for markup problems that only show up
| after Blade compiles: uncompiled directives, unescaped Blade echoes, classes
| Tailwind never generated, or assets that 404.
|
| Usage: php storage/app/audit-html.php [uri] [auth]
*/

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

$app = require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

require_once __DIR__.'/smoke-fixtures.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$manifest = json_decode(file_get_contents(__DIR__ . '/../public/build/manifest.json'), true);

$css = '';

foreach ($manifest as $entry) {
    if (str_ends_with($entry['file'] ?? '', '.css')) {
        // Manifest paths are relative to the Vite build directory.
        $css .= file_get_contents(__DIR__ . '/../public/build/' . $entry['file']);
    }
}

echo 'Compiled CSS: ' . number_format(strlen($css)) . " bytes\n";

// Usage: php storage/app/audit-html.php [uri] [auth]
$only = ($argv[1] ?? '') !== '' && str_starts_with($argv[1], '/') ? $argv[1] : null;
$asUser = in_array($argv[2] ?? '', ['auth'], true)
    || in_array($argv[1] ?? '', ['auth'], true);

[$smokeUser, $created] = [null, []];

if ($asUser) {
    // Only provision the account when the dashboard actually needs signing in.
    [$smokeUser, $created] = smoke_fixtures();
}

$kos = App\Models\KosListing::with(['images', 'facilities', 'owner'])->first();
$thrift = App\Models\ThriftProduct::with(['images', 'category', 'seller'])->first();

$uris = $only ? [$only] : array_values(array_filter([
        '/',
        '/kos',
        $kos ? '/kos/' . $kos->id : null,
        '/thrift',
        $thrift ? '/thrift/' . $thrift->id : null,
        '/flyer',
        '/login',
        '/register',
        $asUser ? '/dashboard' : null,
    ]));

$problems = [];

/**
 * `class="…"` must not be matched inside Alpine's `:class="…"` or
 * `:class:list`, hence the lookbehind on `:` and `-`.
 */
const CLASS_ATTR = '/(?<![\w:-])class="([^"]*)"/';

/**
 * Pull every distinct class token out of the rendered HTML.
 */
function classTokens(string $html): array
{
    $tokens = [];

    if (preg_match_all(CLASS_ATTR, $html, $matches)) {
        foreach ($matches[1] as $value) {
            // Drop any Alpine binding expressions left inside the literal value.
            $value = preg_replace('/\{\{.*?\}\}|\{!!.*?!!\}/s', ' ', $value);

            foreach (preg_split('/\s+/', (string) $value) as $token) {
                if ($token === '') {
                    continue;
                }

                // Skip arbitrary values: they appear verbatim in the CSS.
                if (str_contains($token, '[') || str_contains($token, '(')) {
                    continue;
                }

                $tokens[$token] = true;
            }
        }
    }

    return array_keys($tokens);
}

/**
 * Tailwind escapes "/", "." and ":" inside class selectors, so a plain
 * substring search reports false misses. Turn a token into the selector that
 * actually ships (e.g. hover:bg-x -> .hover\:bg-x).
 */
function cssSelector(string $token): string
{
    return '.' . str_replace(['/', '.', ':', '!'], ['\\/', '\\.', '\\:', '\\!'], $token);
}

$session = [];

if ($asUser) {
    // Log in once, then carry the session cookie across every audit request.
    $prime = $kernel->handle(Request::create('/login', 'GET'));

    foreach ($prime->headers->getCookies() as $cookie) {
        $session[$cookie->getName()] = $cookie->getValue();
    }

    preg_match('/name="_token"\s+value="([a-zA-Z0-9]+)"/', (string) $prime->getContent(), $tokenMatch);

    $login = $kernel->handle(Request::create('/login', 'POST', [
        '_token' => $tokenMatch[1] ?? '',
        'email' => $smokeUser->email,
        'password' => 'password123',
    ], $session));

    foreach ($login->headers->getCookies() as $cookie) {
        $session[$cookie->getName()] = $cookie->getValue();
    }

    if ($login->getStatusCode() !== 302) {
        echo 'FAILED to sign in as ' . $smokeUser->email . "\n";
        exit(1);
    }

    echo "Authenticated: {$smokeUser->email}\n";
}

/**
 * @return array{status: int, html: string}
 */
function fetch(string $uri, string $method = 'GET', array $params = []): array
{
    global $kernel, $session;

    $response = $kernel->handle(Request::create($uri, $method, $params, $session));

    return ['status' => $response->getStatusCode(), 'html' => (string) $response->getContent()];
}

// A guest hitting /login or /register while signed in is redirected on
// purpose, so a 302 there is not a defect.
$guestOnlyRoutes = ['/login', '/register'];

foreach ($uris as $uri) {
    $result = fetch($uri);
    $html = $result['html'];
    $before = count($problems);

    $expectedRedirect = $asUser && in_array($uri, $guestOnlyRoutes, true);

    echo "\n=== {$uri} [{$result['status']}] (" . number_format(strlen($html)) . " bytes) ===\n";

    if ($expectedRedirect && $result['status'] === 302) {
        echo "  guest route redirects an authenticated user (expected)\n";
    } elseif ($result['status'] !== 200) {
        echo "  NON-200 RESPONSE\n";
        $problems[] = "{$uri}: status {$result['status']}";

        continue;
    }

    // 1. Blade that never compiled.
    foreach (['{{', '@section', '@foreach', '@if(', '@endif', '@csrf', '@stack'] as $needle) {
        if (str_contains($html, $needle)) {
            echo "  UNCOMPILED BLADE: {$needle}\n";
            $problems[] = "{$uri}: uncompiled {$needle}";
        }
    }

    // 2. Errors swallowed into the page body.
    foreach (['Undefined variable', 'Undefined array key', 'Whoops', 'whoops', 'stack trace'] as $needle) {
        if (stripos($html, $needle) !== false) {
            echo "  ERROR LEAK: {$needle}\n";
            $problems[] = "{$uri}: error leak {$needle}";
        }
    }

    // 3. Duplicate attributes on the same tag (browsers silently keep the first).
    //    :class="…" also contains the literal text class=", so guard with the
    //    same lookbehind the class scanner uses.
    if (preg_match_all('/<(\w+)(?![^>]*?(?<![\w:-])class="[^"]*"[^>]*?(?<![\w:-])class=")[^>]*>/i', $html, $m) && $m[0]) {
        $dupes = [];

        foreach ($m[0] as $tag) {
            if (preg_match_all(CLASS_ATTR, $tag, $found) && count($found[0]) > 1) {
                $dupes[] = $tag;
            }
        }

        if ($dupes) {
            echo '  DUPLICATE class ATTRIBUTE x' . count($dupes) . "\n";

            foreach (array_slice($dupes, 0, 3) as $tag) {
                echo '    ' . substr(preg_replace('/\s+/', ' ', $tag), 0, 220) . "\n";
            }

            $problems[] = "{$uri}: duplicate class attribute";
        }
    }

    // 4. Utility classes that never made it into the compiled stylesheet.
    $missing = array_values(array_filter(
        classTokens($html),
        fn ($token) => ! str_contains($css, cssSelector($token))
    ));

    if ($missing) {
        echo '  MISSING CSS (' . count($missing) . '): ' . implode(', ', $missing) . "\n";
        $problems[] = "{$uri}: missing css " . implode(' ', $missing);
    }

    // 4b. Locate any duplicate-attribute tags so they can be fixed by name.
    //     (Handled in step 3.)

    // 5. Local assets that do not exist on disk.
    if (preg_match_all('/(?:src|href)="(\/[^"]+\.(?:css|js|png|svg|jpg|jpeg|webp|ico|webmanifest))"/', $html, $matches)) {
        foreach (array_unique($matches[1]) as $asset) {
            $path = __DIR__ . '/../public' . $asset;

            if (! file_exists($path)) {
                echo "  MISSING ASSET: {$asset}\n";
                $problems[] = "{$uri}: missing asset {$asset}";
            }
        }
    }

    // 6. Anchors that point at ids which are not on the page.
    if (preg_match_all('/href="#([a-zA-Z][\w\-]*)"/', $html, $matches)) {
        foreach (array_unique($matches[1]) as $anchor) {
            if (! str_contains($html, 'id="' . $anchor . '"')) {
                echo "  DEAD ANCHOR: #{$anchor}\n";
                $problems[] = "{$uri}: dead anchor #{$anchor}";
            }
        }
    }

    // 7. Images without alt text.
    if (preg_match_all('/<img(?![^>]*\balt=)[^>]*>/i', $html, $matches)) {
        echo '  IMG WITHOUT ALT x' . count($matches[0]) . "\n";
        $problems[] = "{$uri}: img without alt";
    }

    if (count($problems) === $before) {
        echo "  clean\n";
    }
}

if ($asUser) {
    smoke_teardown($created);
}

echo "\n----------------------------------------\n";

if ($problems) {
    echo 'PROBLEMS (' . count($problems) . "):\n";

    foreach (array_unique($problems) as $problem) {
        echo "  - {$problem}\n";
    }

    exit(1);
}

echo "ALL CLEAN\n";