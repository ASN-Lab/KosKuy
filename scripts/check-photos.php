<?php

/**
 * Sanity-checks the seeded listing artwork without needing to view it.
 *
 * A broken centre-crop leaves large flat white regions, so we sample a coarse
 * grid and assert there is real tonal variety and no dominant blank area.
 */

$files = array_merge(
    glob(__DIR__ . '/../storage/app/public/kos/*.jpg') ?: [],
    glob(__DIR__ . '/../storage/app/public/thrift/*.jpg') ?: [],
);

if ($files === []) {
    echo "  no artwork found\n";
    exit(1);
}

$problems = 0;

foreach ($files as $file) {
    $im = @imagecreatefromjpeg($file);

    if ($im === false) {
        echo '  UNREADABLE: ' . basename($file) . "\n";
        $problems++;
        continue;
    }

    $w = imagesx($im);
    $h = imagesy($im);

    $colours = [];
    $white = 0;
    $samples = 0;

    for ($y = 2; $y < $h; $y += 25) {
        for ($x = 2; $x < $w; $x += 25) {
            $rgb = imagecolorat($im, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            // Quantise so JPEG noise does not inflate the palette count.
            $colours[($r >> 4) . ':' . ($g >> 4) . ':' . ($b >> 4)] = true;
            $samples++;

            if ($r > 242 && $g > 242 && $b > 242) {
                $white++;
            }
        }
    }

    imagedestroy($im);

    $palette = count($colours);
    $whiteShare = $white / max(1, $samples);

    $flags = [];

    if ($w !== 800 || $h !== 600) {
        $flags[] = "wrong size {$w}x{$h}";
    }

    // A photograph has many distinct tones; flat art does not.
    if ($palette < 40) {
        $flags[] = "too few tones ({$palette}) - may be flat/blank";
    }

    if ($whiteShare > 0.30) {
        $flags[] = sprintf('%.0f%% near-white - crop likely broken', $whiteShare * 100);
    }

    printf(
        '  %-22s %5.1f kB  %d tones  %4.1f%% white  %s%s' . PHP_EOL,
        basename($file),
        filesize($file) / 1024,
        $palette,
        $whiteShare * 100,
        $flags === [] ? 'OK' : 'FAIL: ',
        implode(', ', $flags),
    );

    if ($flags !== []) {
        $problems++;
    }
}

echo $problems === 0
    ? "\n  OK - all " . count($files) . " photos look like real photography\n"
    : "\n  {$problems} problem(s)\n";

exit($problems === 0 ? 0 : 1);
