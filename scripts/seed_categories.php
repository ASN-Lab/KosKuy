<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Str;

$categories = [
    'Pakaian & Fashion',
    'Elektronik & Gadget',
    'Buku & Alat Tulis',
    'Peralatan Kamar & Kost',
    'Olahraga & Hobi',
];

foreach ($categories as $cat) {
    App\Models\Category::firstOrCreate(
        ['name' => $cat],
        ['slug' => Str::slug($cat)]
    );
}

echo "Done seeding categories!" . PHP_EOL;
