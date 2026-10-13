<?php

$app = require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== listings ===\n";

foreach (DB::table('kos_listings')->get() as $k) {
    $images = DB::table('kos_images')->where('kos_id', $k->id)->count();
    $facs = DB::table('kos_facilities')->where('kos_id', $k->id)->count();

    printf("  #%-3s %-42s images=%d facilities=%d\n", $k->id, $k->title, $images, $facs);
}

echo "\n=== orphans ===\n";

$ids = DB::table('kos_listings')->pluck('id')->all();

foreach (['kos_images', 'kos_facilities'] as $table) {
    $orphans = DB::table($table)->whereNotIn('kos_id', $ids ?: [0])->get();

    echo "  {$table}: " . $orphans->count() . " orphaned, pointing at kos_id "
        . ($orphans->pluck('kos_id')->unique()->implode(', ') ?: '-') . "\n";
}

echo "\n=== smoke leftovers ===\n";

foreach (DB::table('users')->get() as $u) {
    $flag = str_contains($u->email, 'koskuy.test') ? '  <-- SMOKE LEFTOVER' : '';
    echo "  {$u->email}{$flag}\n";
}

echo "\n=== sessions / cache ===\n";
echo '  sessions: ' . DB::table('sessions')->count() . "\n";

if (DB::getSchemaBuilder()->hasTable('cache')) {
    echo '  cache keys: ' . (DB::table('cache')->pluck('key')->implode(', ') ?: '(none)') . "\n";
} else {
    echo "  cache table: absent (cache driver is not database)\n";
}
