<?php

/*
|--------------------------------------------------------------------------
| Blade icon audit
|--------------------------------------------------------------------------
| Fails when a Blade template references an icon name that is not present in
| App\Support\Icons. Icon names returned from the helpers (facilityIcon,
| categoryIcon, genderIcon) are checked too, so a typo anywhere surfaces here
| instead of as a missing glyph in the browser.
|
| Usage: php storage/app/check-icons.php
*/

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/Support/Icons.php';

$known = App\Support\Icons::names();

$missing = [];

// --- Icons referenced through the <x-ui.icon name="..." /> component ---------

$bladeIterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(__DIR__ . '/../resources/views')
);

$referenced = [];

foreach ($bladeIterator as $file) {
    if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
        continue;
    }

    $contents = file_get_contents($file->getPathname());

    preg_match_all('/<x-ui\.icon[^>]*\b:?name="([a-z0-9\-]+)"/i', $contents, $matches);

    foreach ($matches[1] as $name) {
        $referenced[$name][] = $file->getFilename();
    }

    // Icon names can also live in a PHP array literal, e.g.
    // ['icon' => 'building-office-2'] inside a @foreach seed.
    preg_match_all("/'icon'\s*=>\s*'([a-z0-9\-]+)'/", $contents, $arrayMatches);

    foreach ($arrayMatches[1] as $name) {
        $referenced[$name][] = $file->getFilename();
    }
}

// --- Icon names returned by the presentation helpers -----------------------

$helpers = file_get_contents(__DIR__ . '/../app/Support/helpers.php');

foreach (['facilityIcon', 'categoryIcon', 'genderIcon'] as $helper) {
    if (! preg_match('/function ' . $helper . '\(.*?\n    \}/s', $helpers, $body)) {
        continue;
    }

    $source = $body[0];

    // Drop str_contains()/str_starts_with() needles: those are keywords, not icons.
    $source = preg_replace('/str_\w+\([^)]*\)/', '', $source);

    preg_match_all("/=>\s*'([a-z0-9\-]+)'/", $source, $helperMatches);

    foreach ($helperMatches[1] as $name) {
        $referenced[$name][] = 'helpers.php:' . $helper;
    }
}

ksort($referenced);

foreach ($referenced as $name => $files) {
    if (! in_array($name, $known, true)) {
        $missing[$name] = array_values(array_unique($files));
    }
}

echo 'Icons bundled: ' . count($known) . "\n";
echo 'Icons referenced: ' . count($referenced) . "\n\n";

if ($missing) {
    echo "MISSING ICONS:\n";

    foreach ($missing as $name => $files) {
        echo "  - {$name} (referenced in: " . implode(', ', $files) . ")\n";
    }

    exit(1);
}

echo "OK - every referenced icon exists.\n";

$unused = array_diff($known, array_keys($referenced));

echo "\nUnused icons (" . count($unused) . '): ' . implode(', ', $unused) . "\n";