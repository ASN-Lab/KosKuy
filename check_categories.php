<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CATEGORIES ===" . PHP_EOL;
foreach (App\Models\Category::all() as $c) {
    echo "{$c->id} | {$c->name}" . PHP_EOL;
}
