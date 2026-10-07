<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== USERS ===" . PHP_EOL;
foreach (App\Models\User::all() as $u) {
    echo "{$u->id} | {$u->email} | {$u->role}" . PHP_EOL;
}

echo PHP_EOL . "=== ROLE ENUM (check_columns) ===" . PHP_EOL;
$types = DB::select("SHOW COLUMNS FROM users LIKE 'role'");
print_r($types);
