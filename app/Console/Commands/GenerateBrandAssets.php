<?php

namespace App\Console\Commands;

use GdImage;
use Illuminate\Console\Command;

/**
 * Generates the raster KosKuy brand assets (favicon, app icon, Open Graph cover)
 * from the same palette that drives resources/css/app.css.
 *
 * Usage:  php artisan brand:assets
 */
class GenerateBrandAssets extends Command
{
    protected $signature = 'brand:assets';

    protected $description = 'Generate favicon, app icon, and Open Graph cover images for KosKuy';

    /** @var array<string, string> */
    private const PALETTE = [
        'brand' => '#4f46e5',
        'violet' => '#7c3aed',
        'fuchsia' => '#a855f7',
        'accent' => '#10b981',
        'mint' => '#6ee7b7',
        'ink' => '#171c2b',
        'inkSoft' => '#3b4358',
        'white' => '#ffffff',
    ];

    /** @var string[] Absolute paths of candidate fonts (Windows first, then Linux/macOS). */
    private const FONT_STACK = [
        'C:/Windows/Fonts/seguibl.ttf',
        'C:/Windows/Fonts/segoeuib.ttf',
        'C:/Windows/Fonts/seguisb.ttf',
        'C:/Windows/Fonts/arialbd.ttf',
        '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
        '/System/Library/Fonts/Supplemental/Arial Bold.ttf',
    ];

    private ?string $fontBold = null;

    private ?string $fontRegular = null;

    public function handle(): int
    {
        if (! extension_loaded('gd')) {
            $this->components->error('Ekstensi PHP GD belum aktif. Aktifkan extension=gd di php.ini.');

            return self::FAILURE;
        }

        $this->resolveFonts();

        if ($this->fontBold === null) {
            $this->components->warn('Tidak ada font TrueType ditemukan — teks pada gambar akan dilewati.');
        }

        @mkdir(public_path('images'), 0777, true);

        $this->components->info('Membuat brand assets…');

        $this->makeAppIcon(512);
        $this->makeAppIcon(192);
        $this->makeFavicon();
        $this->makeOgCover();

        $this->components->info('Selesai. File dibuat di public/images dan public/favicon.ico');

        return self::SUCCESS;
    }

    /* --------------------------------------------------------------------- */
    /* Generators                                                             */
    /* --------------------------------------------------------------------- */

    private function makeAppIcon(int $size): void
    {
        $im = $this->createCanvas($size, $size);
        $this->paintDiagonalGradient($im, 0, 0, $size, $size, [
            $this->rgb('brand'),
            $this->rgb('violet'),
            $this->rgb('fuchsia'),
        ]);

        $r = (int) round($size * 0.225);
        $this->filledRoundedRect($im, 0, 0, $size, $size, $r, $this->rgba('fuchsia', 0));

        $this->drawHouseMark($im, (int) ($size * 0.5), (int) ($size * 0.5), (int) ($size * 0.62));

        $path = public_path("images/app-icon-{$size}.png");
        imagepng($im, $path, 9);
        imagedestroy($im);

        $this->line("  <fg=gray>app-icon-{$size}.png</> " . $this->humanSize($size));
    }

    private function makeFavicon(): void
    {
        // 32x32 PNG, wrapped in an ICO container (PNG-in-ICO is supported by every
        // browser since IE11 / all modern engines).
        $size = 32;
        $im = $this->createCanvas($size, $size);
        $this->paintDiagonalGradient($im, 0, 0, $size, $size, [
            $this->rgb('brand'),
            $this->rgb('violet'),
            $this->rgb('fuchsia'),
        ]);
        $this->drawHouseMark($im, (int) ($size / 2), (int) ($size / 2), (int) ($size * 0.7));

        ob_start();
        imagepng($im, null, 9);
        $png = (string) ob_get_clean();
        imagedestroy($im);

        file_put_contents(public_path('favicon.png'), $png);

        // ICONDIR (6 bytes) + one ICONDIRENTRY (16 bytes) + inline PNG payload.
        $ico = pack(
            'vvvCCCCvvVV',
            0, 0,             // reserved, type = 1 (icon)
            1,                // image count
            $size, $size,     // width, height
            0, 0,             // palette size, reserved
            1, 32,            // colour planes, bits per pixel
            strlen($png),     // bytes in resource
            22                // offset of image data
        ) . $png;

        file_put_contents(public_path('favicon.ico'), $ico);

        $this->line('  <fg=gray>favicon.png / favicon.ico</> ' . $this->humanSize($size));
    }

    private function makeOgCover(): void
    {
        $w = 1200;
        $h = 630;
        $im = $this->createCanvas($w, $h);

        $this->paintDiagonalGradient($im, 0, 0, $w, $h, [
            $this->rgb('brand'),
            $this->rgb('violet'),
            $this->rgb('fuchsia'),
        ]);

        // Soft light blobs
        $this->radialGlow($im, 1010, 110, 460, $this->rgba('fuchsia', 78));
        $this->radialGlow($im, 120, 560, 400, $this->rgba('brand', 70));
        $this->radialGlow($im, 640, -60, 340, $this->rgba('mint', 34));

        // Faint grid
        for ($x = 0; $x < $w; $x += 50) {
            imageline($im, $x, 0, $x, $h, $this->rgba('white', 12));
        }
        for ($y = 0; $y < $h; $y += 50) {
            imageline($im, 0, $y, $w, $y, $this->rgba('white', 12));
        }

        /* ---------------- Floating listing card mock-up (right) ---------------- */
        $cardX = 720;
        $cardY = 118;
        $cardW = 420;
        $cardH = 396;

        // shadow plate
        $this->filledRoundedRect($im, $cardX + 8, $cardY + 14, $cardW, $cardH, 28, $this->rgba('ink', 46));
        // back card (rotated feel via offset)
        $this->filledRoundedRect($im, $cardX + 40, $cardY - 26, $cardW, $cardH, 28, $this->rgba('white', 60));

        // main card
        $this->filledRoundedRect($im, $cardX, $cardY, $cardW, $cardH, 28, $this->rgba('white', 255));

        // "photo" area
        $photoH = 214;
        $this->saveAlpha($im, true);
        $this->filledRoundedRect($im, $cardX + 18, $cardY + 18, $cardW - 36, $photoH, 20, $this->rgba('white', 255));
        $this->paintDiagonalGradientRegion($im, $cardX + 18, $cardY + 18, $cardW - 36, $photoH, [
            $this->rgb('violet'),
            $this->rgb('fuchsia'),
        ]);
        $this->drawHouseMark($im, $cardX + (int) ($cardW / 2), $cardY + 18 + (int) ($photoH / 2), 118);
        // clip the bottom corners of the photo area
        $this->filledRect($im, $cardX + 18, $cardY + 18 + $photoH - 22, $cardW - 36, 4, $this->rgba('white', 0));
        $this->filledRoundedRect($im, $cardX + 18, $cardY + 18 + $photoH - 22, $cardW - 36, 22, 0, $this->rgba('white', 255));
        $this->saveAlpha($im, false);

        // card text lines
        $this->text($im, $cardX + 28, $cardY + $photoH + 52, 'Kos Melati Putri', 25, $this->rgb('ink'), 'bold');
        $this->pill($im, $cardX + $cardW - 108, $cardY + $photoH + 34, 80, 30, $this->rgba('accent', 255), 'PUTRI');
        $this->text($im, $cardX + 28, $cardY + $photoH + 96, 'Rp 1.200.000 / bulan', 21, $this->rgb('brand'), 'bold');
        $this->pill($im, $cardX + 28, $cardY + $photoH + 118, 86, 26, $this->rgba('ink', 26), 'Wi-Fi');
        $this->pill($im, $cardX + 124, $cardY + $photoH + 118, 120, 26, $this->rgba('ink', 26), 'Kamar mandi dalam');
        $this->filledRoundedRect($im, $cardX + 28, $cardY + $cardH - 62, $cardW - 56, 42, 21, $this->rgb('brand'));

        /* ---------------- Copy block (left) ---------------- */
        $x = 84;

        // logo lockup
        $this->filledRoundedRect($im, $x, 74, 54, 54, 16, $this->rgba('white', 255));
        $this->drawHouseMark($im, $x + 27, 74 + 27, 34, $this->rgb('brand'));
        $this->text($im, $x + 70, 112, 'KosKuy', 34, $this->rgb('white'), 'bold');

        // eyebrow pill
        $this->pill($im, $x, 176, 372, 40, $this->rgba('white', 40), 'PLATFORM KOS & THRIFT MAHASISWA', 15);

        // headline
        $this->text($im, $x - 2, 268, 'Kos Impian,', 62, $this->rgb('white'), 'bold');
        $this->text($im, $x - 2, 336, 'Thrift Pilihan.', 62, $this->rgba('mint', 255), 'bold');

        // sub-copy
        $this->wrapText($im, $x, 400, 560, 30, [
            'Cari kos, bandingkan harga, lalu chat pemilik',
            'langsung lewat WhatsApp. Gratis untuk mahasiswa.',
        ], 19, $this->rgba('white', 226));

        // CTA pills
        $this->filledRoundedRect($im, $x, 486, 208, 52, 26, $this->rgba('white', 255));
        $this->text($im, $x + 26, 522, 'Cari Kos Gratis', 19, $this->rgb('brand'), 'bold');
        $this->outlineRoundedRect($im, $x + 226, 486, 168, 52, 26, $this->rgba('white', 150), 2);
        $this->text($im, $x + 258, 522, 'Lihat Thrift', 19, $this->rgb('white'), 'bold');

        // url
        $this->text($im, $x, 588, 'koskuy.id  •  Dibuat oleh mahasiswa, untuk mahasiswa', 17, $this->rgba('white', 190));

        $path = public_path('images/og-cover.png');
        imagepng($im, $path, 9);
        imagedestroy($im);

        $this->line('  <fg=gray>og-cover.png</> 1200x630 ' . $this->humanSize($w));
    }

    /* --------------------------------------------------------------------- */
    /* Drawing helpers                                                        */
    /* --------------------------------------------------------------------- */

    private function createCanvas(int $w, int $h): GdImage
    {
        $im = imagecreatetruecolor($w, $h);
        imagealphablending($im, true);
        imagesavealpha($im, true);
        imageantialias($im, true);
        imagefilledrectangle($im, 0, 0, $w, $h, $this->rgb('brand'));

        return $im;
    }

    private function saveAlpha(GdImage $im, bool $on): void
    {
        imagesavealpha($im, $on);
    }

    /** @param int[] $stops Packed truecolor integers. */
    private function paintDiagonalGradient(GdImage $im, int $x, int $y, int $w, int $h, array $stops): void
    {
        $this->paintDiagonalGradientRegion($im, $x, $y, $w, $h, $stops);
    }

    /** @param int[] $stops Packed truecolor integers. */
    private function paintDiagonalGradientRegion(GdImage $im, int $x, int $y, int $w, int $h, array $stops): void
    {
        $segments = count($stops) - 1;
        $max = max($w, $h);

        for ($py = 0; $py < $h; $py++) {
            for ($px = 0; $px < $w; $px++) {
                $t = ($px + $py) / (2 * $max);
                $t = min(1, max(0, $t * 1.45));
                $pos = $t * $segments;
                $idx = min($segments - 1, (int) floor($pos));
                $local = $pos - $idx;

                imagesetpixel($im, $x + $px, $y + $py, $this->mix($stops[$idx], $stops[$idx + 1], $local));
            }
        }
    }

    /** Mixes two packed truecolor integers. */
    private function mix(int $a, int $b, float $t): int
    {
        $ar = ($a >> 16) & 0xFF;
        $ag = ($a >> 8) & 0xFF;
        $ab = $a & 0xFF;
        $br = ($b >> 16) & 0xFF;
        $bg = ($b >> 8) & 0xFF;
        $bb = $b & 0xFF;

        $r = (int) round($ar + ($br - $ar) * $t);
        $g = (int) round($ag + ($bg - $ag) * $t);
        $bl = (int) round($ab + ($bb - $ab) * $t);

        return ($r << 16) | ($g << 8) | $bl;
    }

    private function radialGlow(GdImage $im, int $cx, int $cy, int $radius, int $color): void
    {
        $steps = 42;

        for ($i = $steps; $i > 0; $i--) {
            $r = (int) round($radius * ($i / $steps));
            $a = (int) round(($color >> 24) & 0x7F);
            $t = $i / $steps;
            $alpha = (int) round($a * (1 - $t) ** 1.7);

            if ($alpha <= 0) {
                continue;
            }

            $c = imagecolorallocatealpha(
                $im,
                ($color >> 16) & 0xFF,
                ($color >> 8) & 0xFF,
                $color & 0xFF,
                $alpha,
            );

            imagefilledellipse($im, $cx, $cy, $r * 2, $r * 2, $c);
        }
    }

    private function drawHouseMark(GdImage $im, int $cx, int $cy, int $size, ?int $color = null): void
    {
        $color ??= $this->rgba('white', 245);
        $s = $size / 64; // design grid is 64x64

        // Roof + body silhouette
        $pts = [
            (int) (32 * $s), (int) (13 * $s),
            (int) (14 * $s), (int) (27.5 * $s),
            (int) (14 * $s), (int) (51 * $s),
            (int) (29.5 * $s), (int) (51 * $s),
            (int) (29.5 * $s), (int) (38.5 * $s),
            (int) (34.5 * $s), (int) (38.5 * $s),
            (int) (34.5 * $s), (int) (51 * $s),
            (int) (50 * $s), (int) (51 * $s),
            (int) (50 * $s), (int) (27.5 * $s),
        ];

        $points = [];
        foreach ($pts as $i => $v) {
            $points[$i] = $i % 2 === 0 ? $cx + $v - (int) (32 * $s) : $cy + $v - (int) (32 * $s);
        }

        imagefilledpolygon($im, $points, $color);

        // Accent dot
        imagefilledellipse(
            $im,
            $cx,
            $cy - (int) (10 * $s),
            (int) (6.4 * $s),
            (int) (6.4 * $s),
            $this->rgba('mint', 235),
        );
    }

    private function filledRoundedRect(GdImage $im, int $x, int $y, int $w, int $h, int $r, int $color): void
    {
        if ($w <= 0 || $h <= 0) {
            return;
        }

        $r = max(0, min($r, (int) floor(min($w, $h) / 2)));

        imagefilledrectangle($im, $x + $r, $y, $x + $w - $r, $y + $h, $color);
        imagefilledrectangle($im, $x, $y + $r, $x + $w, $y + $h - $r, $color);

        $d = $r * 2;
        imagefilledellipse($im, $x + $r, $y + $r, $d, $d, $color);
        imagefilledellipse($im, $x + $w - $r, $y + $r, $d, $d, $color);
        imagefilledellipse($im, $x + $r, $y + $h - $r, $d, $d, $color);
        imagefilledellipse($im, $x + $w - $r, $y + $h - $r, $d, $d, $color);
    }

    private function outlineRoundedRect(GdImage $im, int $x, int $y, int $w, int $h, int $r, int $color, int $thickness = 1): void
    {
        $r = max(0, min($r, (int) floor(min($w, $h) / 2)));

        imagesetthickness($im, $thickness);
        imagearc($im, $x + $r, $y + $r, $r * 2, $r * 2, 180, 270, $color);
        imagearc($im, $x + $w - $r, $y + $r, $r * 2, $r * 2, 270, 360, $color);
        imagearc($im, $x + $r, $y + $h - $r, $r * 2, $r * 2, 90, 180, $color);
        imagearc($im, $x + $w - $r, $y + $h - $r, $r * 2, $r * 2, 0, 90, $color);
        imageline($im, $x + $r, $y, $x + $w - $r, $y, $color);
        imageline($im, $x + $r, $y + $h - 1, $x + $w - $r, $y + $h - 1, $color);
        imageline($im, $x, $y + $r, $x, $y + $h - $r, $color);
        imageline($im, $x + $w - 1, $y + $r, $x + $w - 1, $y + $h - $r, $color);
        imagesetthickness($im, 1);
    }

    private function filledRect(GdImage $im, int $x, int $y, int $w, int $h, int $color): void
    {
        imagefilledrectangle($im, $x, $y, $x + $w, $y + $h, $color);
    }

    private function pill(GdImage $im, int $x, int $y, int $w, int $h, int $bg, string $label, int $fontSize = 13): void
    {
        $this->filledRoundedRect($im, $x, $y, $w, $h, (int) ($h / 2), $bg);
        $tw = $this->textWidth($label, $fontSize);
        $this->text($im, $x + (int) (($w - $tw) / 2), $y + (int) ($h / 2) + (int) ($fontSize * 0.36), $label, $fontSize, $this->rgb('ink'), 'bold');
    }

    private function text(GdImage $im, int $x, int $y, string $text, int $size, int $color, string $weight = 'regular'): void
    {
        $font = $weight === 'bold' ? $this->fontBold : $this->fontRegular;

        if ($font === null) {
            return;
        }

        imagettftext($im, $size, 0, $x, $y, $color, $font, $text);
    }

    /** @param string[] $lines */
    private function wrapText(GdImage $im, int $x, int $y, int $maxWidth, int $lineHeight, array $lines, int $size, int $color): void
    {
        foreach ($lines as $i => $line) {
            $this->text($im, $x, $y + ($i * $lineHeight), $line, $size, $color);
        }
    }

    private function textWidth(string $text, int $size): int
    {
        $font = $this->fontBold ?? $this->fontRegular;

        if ($font === null) {
            return strlen($text) * (int) ($size * 0.6);
        }

        $box = imagettfbbox($size, 0, $font, $text);

        return $box ? ($box[2] - $box[0]) : 0;
    }

    /* --------------------------------------------------------------------- */
    /* Colour + font plumbing                                                 */
    /* --------------------------------------------------------------------- */

    private function rgb(string $key): int
    {
        [$r, $g, $b] = $this->hex(self::PALETTE[$key] ?? '#000000');

        // Truecolor images encode colours as plain integers, so this is safe to
        // reuse across every canvas we create.
        return ($r << 16) | ($g << 8) | $b;
    }

    private function rgba(string $key, int $alpha): int
    {
        [$r, $g, $b] = $this->hex(self::PALETTE[$key] ?? '#000000');
        $a = (int) round(127 - ($alpha / 255) * 127);

        return ($a << 24) | ($r << 16) | ($g << 8) | $b;
    }

    /** @return array{int,int,int} */
    private function hex(string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [
            (int) hexdec(substr($hex, 0, 2)),
            (int) hexdec(substr($hex, 2, 2)),
            (int) hexdec(substr($hex, 4, 2)),
        ];
    }

    private function resolveFonts(): void
    {
        foreach (self::FONT_STACK as $path) {
            if ($this->fontBold === null && is_file($path)) {
                $this->fontBold = str_replace('/', '\\', $path);
            }
        }

        $regular = [
            'C:/Windows/Fonts/segoeui.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/System/Library/Fonts/Supplemental/Arial.ttf',
        ];

        foreach ($regular as $path) {
            if (is_file($path)) {
                $this->fontRegular = str_replace('/', '\\', $path);

                return;
            }
        }

        $this->fontRegular = $this->fontBold;
    }

    private function humanSize(int $px): string
    {
        return "({$px}px)";
    }
}
