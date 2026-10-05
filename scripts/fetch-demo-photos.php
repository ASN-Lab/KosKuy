<?php

/**
 * Downloads freely-licensed demo photography from Wikimedia Commons.
 *
 * Replaces the generated SVG illustrations with real photographs so the
 * storefront looks like a genuine marketplace listing.
 *
 * Every file is normalised to 800x600 JPEG (4:3, quality 82) with GD, centred
 * crop, which is what the card and gallery CSS expects.
 *
 * Attribution for every downloaded file is written to
 * storage/app/public/PHOTO-CREDITS.md.
 *
 * Usage:  php scripts/fetch-demo-photos.php
 */

const API = 'https://commons.wikimedia.org/w/api.php';

const UA = 'KosKuy/1.0 (demo asset fetcher; contact: dev@koskuy.test)';

/**
 * Curated allowlist. Titles were chosen so the file name itself confirms the
 * subject, which is the only way to vet relevance without viewing the image.
 *
 * slot => Commons file title
 */
const MANIFEST = [
    // ---- Kos 1: Putra -----------------------------------------------------
    'kos/kos-1-1.jpg' => 'File:Twin room in a German youth hostel with single beds and wardrobes.jpg',
    'kos/kos-1-2.jpg' => 'File:Dorm room (3859180139).jpg',
    'kos/kos-1-3.jpg' => 'File:Hostel 6-bed dorm room, Kuching, Malaysia.jpg',

    // ---- Kos 2: Putri -----------------------------------------------------
    'kos/kos-2-1.jpg' => 'File:Cozy wooden bedroom with floral bedding.jpg',
    'kos/kos-2-2.jpg' => 'File:Cozy wooden cabin interior with a bed.jpg',
    'kos/kos-2-3.jpg' => 'File:SRMPS - Hostel Room.jpg',

    // ---- Kos 3: Campur ----------------------------------------------------
    'kos/kos-3-1.jpg' => 'File:Dormitory of SUSTech.jpg',
    'kos/kos-3-2.jpg' => 'File:Tatsat Hostel.jpg',
    'kos/kos-3-3.jpg' => 'File:Dialynas Hall common room photosphere.jpg',

    // ---- Thrift -----------------------------------------------------------
    'thrift/thrift-1.jpg' => 'File:Stack of multicolored books on a table.jpg',
    'thrift/thrift-2.jpg' => 'File:Laptop on a neat desk (Unsplash).jpg',
    'thrift/thrift-3.jpg' => 'File:Folding chairs in a display.jpg',
];

const TARGET_W = 800;
const TARGET_H = 600;

function api(array $params): array
{
    $params += ['format' => 'json', 'formatversion' => 2];

    $url = API . '?' . http_build_query($params);

    for ($attempt = 1; $attempt <= 4; $attempt++) {
        $raw = @file_get_contents($url, false, stream_context_create([
            'http' => ['timeout' => 30, 'user_agent' => UA, 'header' => "Accept: application/json\r\n"],
        ]));

        if ($raw !== false) {
            $json = json_decode($raw, true);

            if (is_array($json)) {
                return $json;
            }
        }

        // Commons rate-limits bursts; back off before retrying.
        sleep($attempt * 2);
    }

    return [];
}

/** Resolve a file title to its imageinfo, falling back to a prefix search. */
function resolve(string $title): ?array
{
    $json = api([
        'action' => 'query',
        'titles' => $title,
        'prop' => 'imageinfo',
        'iiprop' => 'url|size|mime|extmetadata',
        'iiurlwidth' => 1600,
    ]);

    $page = $json['query']['pages'][0] ?? null;

    if (($page['missing'] ?? false) === false && ! empty($page['imageinfo'][0])) {
        return $page + ['resolved_title' => $page['title']];
    }

    // Title did not match exactly (truncated in our notes, renamed, etc).
    $needle = preg_replace('/^File:/', '', $title);

    $json = api([
        'action' => 'query',
        'generator' => 'search',
        'gsrsearch' => 'filetype:bitmap ' . $needle,
        'gsrnamespace' => 6,
        'gsrlimit' => 5,
        'prop' => 'imageinfo',
        'iiprop' => 'url|size|mime|extmetadata',
        'iiurlwidth' => 1600,
    ]);

    $best = null;

    foreach ($json['query']['pages'] ?? [] as $candidate) {
        if (empty($candidate['imageinfo'][0])) {
            continue;
        }

        $info = $candidate['imageinfo'][0];

        if ($info['mime'] !== 'image/jpeg' || $info['width'] < 900) {
            continue;
        }

        // Prefer the closest title to what we asked for.
        similar_text($needle, preg_replace('/^File:/', '', $candidate['title']), $score);

        if ($best === null || $score > $best['score']) {
            $best = ['page' => $candidate, 'score' => $score];
        }
    }

    return $best === null ? null : $best['page'] + ['resolved_title' => $best['page']['title']];
}

function plain(string $html): string
{
    return trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5));
}

function fetchBytes(string $url): ?string
{
    for ($attempt = 1; $attempt <= 4; $attempt++) {
        $bytes = @file_get_contents($url, false, stream_context_create([
            'http' => ['timeout' => 45, 'user_agent' => UA],
        ]));

        if ($bytes !== false && strlen($bytes) > 4096) {
            return $bytes;
        }

        sleep($attempt * 2);
    }

    return null;
}

/** Centre-crop whatever we downloaded down to exactly 800x600. */
function normalise(string $bytes, string $destination): bool
{
    $src = @imagecreatefromstring($bytes);

    if ($src === false) {
        return false;
    }

    $sw = imagesx($src);
    $sh = imagesy($src);

    // Scale so the smaller side covers the target, then centre-crop the excess.
    $scale = max(TARGET_W / $sw, TARGET_H / $sh);
    $scaledW = (int) ceil($sw * $scale);
    $scaledH = (int) ceil($sh * $scale);

    $canvas = imagecreatetruecolor(TARGET_W, TARGET_H);
    imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
    imagecopyresampled(
        $canvas,
        $src,
        (int) ((TARGET_W - $scaledW) / 2),
        (int) ((TARGET_H - $scaledH) / 2),
        0,
        0,
        $scaledW,
        $scaledH,
        $sw,
        $sh,
    );

    $ok = imagejpeg($canvas, $destination, 82);

    imagedestroy($src);
    imagedestroy($canvas);

    return $ok;
}

$root = dirname(__DIR__) . '/storage/app/public';
$credits = [];

foreach (MANIFEST as $relative => $wanted) {
    $page = resolve($wanted);

    if ($page === null) {
        echo "  MISS   {$relative}  (no candidate for '{$wanted}')\n";
        continue;
    }

    $info = $page['imageinfo'][0];
    $source = $info['thumburl'] ?? $info['url'];
    $destination = "{$root}/{$relative}";

    if (! is_dir(dirname($destination))) {
        mkdir(dirname($destination), 0755, true);
    }

    $bytes = fetchBytes($source);

    if ($bytes === null) {
        echo "  FAIL   {$relative}  (download failed)\n";
        continue;
    }

    if (! normalise($bytes, $destination)) {
        echo "  FAIL   {$relative}  (GD could not process it)\n";
        continue;
    }

    $meta = $info['extmetadata'] ?? [];

    $credits[] = [
        'file' => $relative,
        'title' => $page['resolved_title'],
        'author' => plain($meta['Artist']['value'] ?? 'Unknown'),
        'license' => plain($meta['LicenseShortName']['value'] ?? 'See source'),
        'source' => $info['descriptionurl'] ?? $source,
    ];

    printf(
        "  OK     %-22s %6.1f kB  %s\n",
        $relative,
        filesize($destination) / 1024,
        mb_strimwidth($page['resolved_title'], 0, 46, '...'),
    );

    usleep(400000);
}

$md = "# Demo photo credits\n\n"
    ."Photography used by the seeded demo listings, sourced from Wikimedia Commons.\n"
    ."These exist only so the storefront is not empty during development.\n"
    ."Replace them with real listing photography before going live.\n\n";

foreach ($credits as $c) {
    $md .= "## {$c['file']}\n\n"
        ."- Source: [{$c['title']}]({$c['source']})\n"
        ."- Author: {$c['author']}\n"
        ."- Licence: {$c['license']}\n\n";
}

file_put_contents("{$root}/PHOTO-CREDITS.md", $md);

echo "\n  " . count($credits) . " / " . count(MANIFEST) . " downloaded\n";
echo "  credits written to storage/app/public/PHOTO-CREDITS.md\n";
