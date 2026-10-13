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
 * Curated allowlist, keyed by the seeded listing/product title. Titles were
 * chosen so the file name itself confirms the subject, which is the only way
 * to vet relevance without viewing the image.
 *
 * Source file names are resolved to row ids from the database, because the
 * photo files are named after row ids (see AGENTS.md gotcha 10). Always seed
 * before running this script.
 *
 * listing title => 3 Commons file titles (photo angles for the gallery)
 */
const KOS_PHOTOS = [
    'Kos Maju Jaya Putra' => [
        'File:HLS-dorm.jpg',
        'File:Dormroom.JPG',
        'File:Room in Hostel 9, Infosys Mysore (2).JPG',
    ],
    'Kos Melati Putri' => [
        'File:Bedroom twin beds.jpg',
        'File:Bedroom king size.jpg',
        'File:Bedrooms of Bunkhouse (6124760082).jpg',
    ],
    'Kos Sejahtera Campur' => [
        'File:Bedroom with wooden bed (1133).jpg',
        'File:Bedroom in the Bosniak house.jpg',
        'File:Bedroom window (Unsplash).jpg',
    ],
    'Kos Tidar Asri' => [
        'File:Average Andrews Dorm Room.jpg',
        'File:Gillsonroom.jpg',
        'File:Hayesroom.jpg',
    ],
    'Kos Anggrek Putri' => [
        'File:Blue bedroom (7530077032).jpg',
        'File:Bedroom logela.jpg',
        'File:Belle chambre.jpg',
    ],
    'Kos Pandan Campur' => [
        'File:Liberty University dorm.jpg',
        'File:O&C dormitory.jpg',
        'File:Dorm Studio (2422564425).jpg',
    ],
    'Kos Kenanga Putri' => [
        'File:Camera dubla cu baie privata nr1.jpg',
        'File:Camera dubla cu bai nr 2.jpg',
        'File:Camera dubla nr 4.jpg',
    ],
    'Kos Merbabu Putra' => [
        'File:DormITESMMonterrey 03.JPG',
        'File:Bulacan,Batangasjf8355 01.JPG',
        'File:Bulacan,Batangasjf8355 02.JPG',
    ],
    'Kos Sendang Sari' => [
        'File:LA2-vx06-evedal6.jpg',
        'File:Youth hostel Wiltz Luxembourg 03.jpg',
        'File:Warszawa Hostel Helvetia - pokoj (2).JPG',
    ],
    'Kos Bougenville' => [
        'File:Paris 75018 Rue Pajol Auberge Jeunesse Yves-Robert chambre 01.jpg',
        'File:Paris 75018 Rue Pajol Auberge Jeunesse Yves-Robert chambre 02.jpg',
        'File:Zimmer in der Jugendherberge - panoramio.jpg',
    ],
    'Kos Borobudur View' => [
        'File:Dormitory in barrack in Taiwan 2019-01-29.jpg',
        'File:Move In Day 2015 (20763692498).jpg',
        'File:Move In Day 2015 (20329066224).jpg',
    ],
    'Kos Tidar Premium' => [
        'File:Planet Traveler Hostel in Toronto (54586286551).jpg',
        'File:1-3rd Residence Tokyo Serviced Apartments, Akihabara - floor beds after bed making (2015-06-14 06.12.44 by Franklin Heijnen).jpg',
        'File:1-3rd Residence Tokyo Serviced Apartments, Akihabara - sleeping in beds (2015-06-14 06.12.29 by Franklin Heijnen).jpg',
    ],
    'Kos Melati Residence' => [
        'File:Bedroom large double bed.jpg',
        'File:Iso Residence bedroom.jpg',
        'File:Japanese bedroom.jpg',
    ],
    'Kos Sky Terrace' => [
        'File:Room sample of Univ. of Tsukuba Hirasuna residence.jpg',
        'File:Cleveland Hostel room.jpg',
        'File:YouthHostelNagoya.jpg',
    ],
    'Kos Kasogangan Loft' => [
        'File:Winterbourne main bedroom 2016.jpg',
        'File:Bedroom of Canopy Tower in Gamboa, Panama 01.jpg',
        'File:Bed & Nightstand (49875912823).jpg',
    ],
];

/**
 * product title => Commons file title
 */
const THRIFT_PHOTOS = [
    'Buku Pemrograman Python' => 'File:Stack of multicolored books on a table.jpg',
    'Kipas Angin Meja Mini' => 'File:A mini table fan.jpg',
    'Kursi Lipat Mahasiswa' => 'File:Folding chairs in a display.jpg',
    'Laptop Bekas Core i5' => 'File:Laptop on a neat desk (Unsplash).jpg',
    'Novel Haruki Murakami' => 'File:Stack of books on table.jpg',
    'Keyboard Mekanik RGB' => 'File:Close-up keyboard view.jpg',
    'Setrika Uap Philips' => 'File:Walton electric iron.jpg',
    'Sepeda Lipat Second' => 'File:Brompton1.jpeg',
    'Gitar Akustik Pemula' => 'File:Two acoustic guitars leaning against a wall.jpg',
    'Tas Ransel 25 Liter' => 'File:Backpack small.jpg',
    'Meja Belajar Kayu' => 'File:Female student writing and studying at dorm room desk.jpg',
    'Sepatu Sneaker Putih' => 'File:White sneakers.jpg',
    'Kamera Analog Canon AE-1' => 'File:Canon AE-1 front with 50mm lens.jpg',
    'Rice Cooker 1.8 Liter' => 'File:Panasonic RICE COOKER SR-L15H8.jpg',
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

$app = require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$root = dirname(__DIR__) . '/storage/app/public';
$credits = [];

// Photo files are named after row ids, so map the curated titles to the ids
// the seeders actually produced.
$kosByTitle = \App\Models\KosListing::pluck('id', 'title');
$thriftByTitle = \App\Models\ThriftProduct::pluck('id', 'title');

$manifest = [];

foreach (KOS_PHOTOS as $title => $shots) {
    if (! isset($kosByTitle[$title])) {
        echo "  SKIP   kos '{$title}' not in DB (seed first, then re-run)\n";
        continue;
    }

    $id = $kosByTitle[$title];

    foreach ($shots as $i => $wanted) {
        $manifest["kos/kos-{$id}-" . ($i + 1) . '.jpg'] = $wanted;
    }
}

foreach (THRIFT_PHOTOS as $title => $wanted) {
    if (! isset($thriftByTitle[$title])) {
        echo "  SKIP   thrift '{$title}' not in DB (seed first, then re-run)\n";
        continue;
    }

    $manifest["thrift/thrift-{$thriftByTitle[$title]}.jpg"] = $wanted;
}

foreach ($manifest as $relative => $wanted) {
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

echo "\n  " . count($credits) . " / " . count($manifest) . " downloaded\n";
echo "  credits written to storage/app/public/PHOTO-CREDITS.md\n";
