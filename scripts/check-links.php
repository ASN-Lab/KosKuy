<?php

$app = require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$checks = [
    '/flyer' => static function ($html) {
        return [
            'references newsletter-qr.png' => str_contains($html, 'newsletter-qr.png'),
            'shows configured host' => str_contains($html, 'koskuy.test'),
            'no hardcoded koskuy.id' => ! str_contains($html, 'koskuy.id'),
            'QR image has alt text' => (bool) preg_match('/<img[^>]*newsletter-qr\.png[^>]*alt="[^"]+"/', $html),
        ];
    },
    '/' => static function ($html) {
        preg_match_all('/href="([^"]+)"/', $html, $m);

        return [
            'whatsapp deep link built from digits' => (bool) preg_grep('#^https://wa\.me/62\d+#', $m[1]),
            'instagram link present' => in_array('https://instagram.com/koskuy', $m[1], true),
            'no bare-digit hrefs' => ! preg_grep('#^62\d+$#', $m[1]),
            'no href="#"' => ! in_array('#', $m[1], true),
        ];
    },
];

foreach ($checks as $uri => $assert) {
    $html = (string) $kernel->handle(Illuminate\Http\Request::create($uri, 'GET'))->getContent();

    echo "=== {$uri} ===\n";

    foreach ($assert($html) as $label => $ok) {
        echo ($ok ? '  OK   ' : '  FAIL ') . $label . "\n";
    }
}