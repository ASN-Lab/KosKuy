<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$map = [
    'admin@koskuy.id' => 'admin123',
    'owner@koskuy.id' => 'owner123',
    'seller@koskuy.id' => 'seller123',
    'asn.labs1210@gmail.com' => 'student123',
    'refasya.audrey.mukhni@students.untidar.ac.id' => 'student123',
    'mahasiswa@koskuy.id' => 'mahasiswa123',
];

printf("%-9s %-48s %s\n", 'ROLE', 'EMAIL', 'PASSWORD');
foreach (User::orderBy('role')->get() as $u) {
    printf("%-9s %-48s %s\n", $u->role, $u->email, $map[$u->email] ?? '?');
}
echo "\nTotal: " . User::count() . " users\n";