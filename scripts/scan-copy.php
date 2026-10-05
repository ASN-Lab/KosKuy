<?php
/**
 * Scans source files for stray non-Latin characters (CJK, Cyrillic, etc.) that
 * indicate corrupted copy. Run: php storage/app/scan-copy.php
 */

$suspect = '/[\x{0400}-\x{04FF}\x{0590}-\x{05FF}\x{0600}-\x{06FF}\x{0900}-\x{097F}\x{0E00}-\x{0E7F}\x{3040}-\x{30FF}\x{3400}-\x{4DBF}\x{4E00}-\x{9FFF}\x{AC00}-\x{D7AF}\x{F900}-\x{FAFF}\x{FF00}-\x{FFEF}]/u';

$allowed = [
    '—', '–', '·', '’', '‘', '“', '”', '…', '×', '°', '≥', '≤', '→', '←', '↑', '↓', '≠', '±', '≈', '€', '™', '®',
];

$dirs = [
    'resources/views',
    'app/Support',
    'app/Http/Controllers',
    'app/Console/Commands',
    'database/seeders',
    'routes',
];

$files = [];

foreach ($dirs as $dir) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/../' . $dir));

    foreach ($it as $f) {
        if ($f->isFile() && in_array($f->getExtension(), ['php', 'blade.php', 'js', 'css'], true)) {
            $files[] = $f->getPathname();
        }
    }
}

$found = 0;

foreach ($files as $file) {
    $lines = file($file, FILE_IGNORE_NEW_LINES);

    foreach ($lines as $n => $line) {
        if (! preg_match($suspect, $line, $m)) {
            continue;
        }

        // Whitelist the handful of typographic characters we intentionally use.
        $clean = str_replace($allowed, '', $line);

        if (! preg_match($suspect, $clean)) {
            continue;
        }

        $found++;
        printf(
            "%s:%d  %s\n",
            str_replace(__DIR__ . '/../', '', $file),
            $n + 1,
            trim(preg_replace('/\s+/', ' ', substr($line, max(0, strpos($line, $m[0]) - 45), 110)))
        );
    }
}

echo $found === 0
    ? "OK - tidak ada karakter mencurigakan.\n"
    : "\n{$found} baris perlu diperiksa.\n";
