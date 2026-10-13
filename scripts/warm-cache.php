<?php

/*
|--------------------------------------------------------------------------
| Warm the storefront cache
|--------------------------------------------------------------------------
| The demo data lives on a remote Postgres (Supabase, ~300ms round-trip), so
| the first hit on any page is slow. This dispatches the common storefront
| URLs through the HTTP kernel once so the file cache (see App\Support\
| StorefrontCache) is populated before the pages are shown to anyone.
|
| Run it right before the demo:  php scripts/warm-cache.php
*/

use App\Models\KosListing;
use App\Models\ThriftProduct;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

$app = require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$urls = ['/', '/kos', '/thrift'];

foreach (['putra', 'putri', 'campur'] as $gender) {
    $urls[] = '/kos?gender_type=' . $gender;
}

foreach (KosListing::where('is_available', true)->latest()->take(8)->pluck('id') as $id) {
    $urls[] = '/kos/' . $id;
}

foreach (ThriftProduct::latest()->take(8)->pluck('id') as $id) {
    $urls[] = '/thrift/' . $id;
}

$urls = array_values(array_unique($urls));

echo 'Warming ' . count($urls) . " storefront URLs...\n";

$start = microtime(true);

foreach ($urls as $uri) {
    $t0 = microtime(true);
    $response = $kernel->handle(Request::create($uri, 'GET'));
    $status = $response->getStatusCode();
    strlen((string) $response->getContent());

    printf("  %-24s %d  %.2fs\n", $uri, $status, microtime(true) - $t0);
}

printf("Done in %.2fs\n", microtime(true) - $start);
