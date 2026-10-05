<?php
/**
 * Regenerates app/Support/Icons.php from the Heroicons v2 SVG sources that ship
 * inside vendor/blade-ui-kit/blade-heroicons.
 *
 * Run:  php storage/app/gen-icons.php
 */

$dir = __DIR__ . '/../vendor/blade-ui-kit/blade-heroicons/resources/svg';

$outline = [
    'home', 'magnifying-glass', 'map-pin', 'sparkles', 'building-office-2', 'tag',
    'users', 'chat-bubble-oval-left', 'check-badge', 'bolt', 'heart', 'share',
    'chevron-down', 'chevron-left', 'chevron-right', 'x-mark', 'bars-3',
    'arrow-right', 'arrow-up-right', 'academic-cap', 'wifi', 'banknotes',
    'shield-check', 'clock', 'calendar-days', 'photo', 'fire', 'truck',
    'star', 'question-mark-circle', 'lock-closed', 'arrow-trending-up', 'gift',
    'device-phone-mobile', 'adjustments-horizontal', 'funnel', 'view-columns',
    'square-3-stack-3d', 'hand-raised', 'currency-rupee', 'envelope',
    'phone', 'plus-circle', 'minus-circle', 'information-circle', 'exclamation-triangle',
    'no-symbol', 'trophy', 'cube-transparent', 'language', 'paint-brush',
    'wrench-screwdriver', 'book-open', 'computer-desktop', 'globe-alt',
    'arrow-down-tray', 'play-circle', 'rocket-launch', 'key', 'sun',
    'cloud', 'bolt-slash', 'arrow-path', 'arrows-pointing-out', 'printer',
    'inbox-stack', 'square-2-stack', 'cog-6-tooth', 'lifebuoy', 'megaphone',
];

$solid = [
    'sparkles', 'bolt', 'check-badge', 'heart', 'star', 'map-pin', 'shield-check',
    'arrow-trending-up', 'fire', 'gift', 'trophy', 'academic-cap', 'check',
    'x-circle', 'plus-circle', 'information-circle', 'play', 'pause',
];

$read = static function (string $file): ?array {
    if (! is_file($file)) {
        fwrite(STDERR, "MISSING: {$file}\n");

        return null;
    }

    $svg = (string) file_get_contents($file);

    if (! preg_match('/<svg\b([^>]*)>(.*)<\/svg>/s', $svg, $m)) {
        fwrite(STDERR, "PARSE FAIL: {$file}\n");

        return null;
    }

    $inner = trim($m[2]);
    $inner = (string) preg_replace('/\s*\n\s*/', '', $inner);
    $inner = (string) preg_replace('/>\s+</', '><', $inner);

    $isOutline = str_contains($m[1], 'fill="none"');

    return [
        'inner' => $inner,
        'fill' => $isOutline ? 'none' : 'currentColor',
        'stroke' => $isOutline ? 'currentColor' : 'none',
    ];
};

$cases = [];

foreach ($outline as $name) {
    $data = $read($dir . "/o-{$name}.svg");

    if ($data !== null) {
        $cases[$name] = $data;
    }
}

foreach ($solid as $name) {
    $data = $read($dir . "/s-{$name}.svg");

    if ($data !== null) {
        $cases[$name . '-solid'] = $data;
    }
}

ksort($cases);

$entries = '';

foreach ($cases as $name => $d) {
    $entries .= '        ' . var_export($name, true) . ' => ['
        . "'fill' => " . var_export($d['fill'], true) . ', '
        . "'stroke' => " . var_export($d['stroke'], true) . ', '
        . "'inner' => " . var_export($d['inner'], true)
        . "],\n";
}

$head = <<<'HEAD'
<?php

namespace App\Support;

/**
 * Inline SVG icon set for the KosKuy storefront.
 *
 * Path data is copied verbatim from Heroicons v2 (MIT © Tailwind Labs) so the
 * public site needs no icon font, sprite sheet, or CDN request at runtime.
 * Regenerate with: php storage/app/gen-icons.php
 */
final class Icons
{
    /**
     * name => [fill, stroke, inner svg markup]
     *
     * Outline icons use fill="none" + stroke="currentColor".
     * Solid variants are suffixed with "-solid" and use fill="currentColor".
     *
     * @var array<string, array{fill: string, stroke: string, inner: string}>
     */
    private const SET = [
HEAD;

$tail = <<<'TAIL'
    ];

    /**
     * Render an icon as an SVG string.
     */
    public static function svg(string $name, string $class = 'size-5'): string
    {
        $icon = self::SET[$name] ?? self::SET['sparkles'];

        $strokeWidth = $icon['stroke'] === 'none' ? '0' : '1.5';

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="' . $icon['fill'] . '"'
            . ' stroke="' . $icon['stroke'] . '" stroke-width="' . $strokeWidth . '"'
            . ' stroke-linecap="round" stroke-linejoin="round" class="' . e($class) . '"'
            . ' aria-hidden="true" focusable="false">' . $icon['inner'] . '</svg>';
    }

    /**
     * Every icon name available to the UI layer.
     *
     * @return array<int, string>
     */
    public static function names(): array
    {
        return array_keys(self::SET);
    }
}
TAIL;

$target = __DIR__ . '/../app/Support/Icons.php';

if (! is_dir(dirname($target))) {
    mkdir(dirname($target), 0777, true);
}

file_put_contents($target, $head . "\n" . $entries . $tail . "\n");

echo 'Wrote ' . count($cases) . " icons to {$target}\n";
