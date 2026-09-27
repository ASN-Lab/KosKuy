<?php

namespace Database\Seeders;

use App\Models\KosImage;
use App\Models\KosListing;
use App\Models\ThriftImage;
use App\Models\ThriftProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ImageSeeder extends Seeder
{
    private array $colors = [
        [99, 102, 241],   // indigo
        [168, 85, 247],   // purple
        [34, 197, 94],    // green
        [234, 179, 8],    // amber
        [239, 68, 68],    // red
        [14, 165, 233],   // sky
    ];

    public function run(): void
    {
        $storagePath = storage_path('app/public');
        $imagesPath = "$storagePath/kos";
        $thriftPath = "$storagePath/thrift";

        File::ensureDirectoryExists($imagesPath);
        File::ensureDirectoryExists($thriftPath);

        KosImage::query()->delete();
        ThriftImage::query()->delete();

        foreach (KosListing::all() as $index => $kos) {
            $color = $this->colors[$index % count($this->colors)];
            $count = rand(1, 3);

            for ($i = 1; $i <= $count; $i++) {
                $filename = "kos-{$kos->id}-{$i}.png";
                $this->generatePlaceholder("$imagesPath/$filename", $kos->title, $color);
                KosImage::create([
                    'kos_id' => $kos->id,
                    'image_path' => "kos/$filename",
                ]);
            }
        }

        foreach (ThriftProduct::all() as $index => $product) {
            $color = $this->colors[($index + 2) % count($this->colors)];

            $filename = "thrift-{$product->id}.png";
            $this->generatePlaceholder("$thriftPath/$filename", $product->title, $color);
            ThriftImage::create([
                'thrift_id' => $product->id,
                'image_path' => "thrift/$filename",
            ]);
        }
    }

    private function generatePlaceholder(string $path, string $label, array $color): void
    {
        $width = 800;
        $height = 600;
        $image = imagecreatetruecolor($width, $height);

        $bg = imagecolorallocate($image, $color[0], $color[1], $color[2]);
        $white = imagecolorallocate($image, 255, 255, 255);

        imagefill($image, 0, 0, $bg);

        // pola diagonal sederhana
        for ($i = 0; $i < $width + $height; $i += 40) {
            imageline($image, $i, 0, $i - $height, $height, imagecolorallocatealpha($image, 255, 255, 255, 90));
        }

        $label = strlen($label) > 30 ? substr($label, 0, 27) . '...' : $label;
        $font = $this->fontPath();

        if ($font !== '') {
            $fontSize = 24;
            $textBox = imagettfbbox($fontSize, 0, $font, $label);
            $textWidth = $textBox[4] - $textBox[6];
            $x = ($width - $textWidth) / 2;
            $y = $height / 2;
            imagettftext($image, $fontSize, 0, (int) $x + 2, (int) $y + 2, imagecolorallocatealpha($image, 0, 0, 0, 60), $font, $label);
            imagettftext($image, $fontSize, 0, (int) $x, (int) $y, $white, $font, $label);
        } else {
            // fallback font bitmap bawaan GD
            $fontSize = 5;
            $textWidth = imagefontwidth($fontSize) * strlen($label);
            $x = ($width - $textWidth) / 2;
            $y = ($height - imagefontheight($fontSize)) / 2;
            imagestring($image, $fontSize, (int) $x + 2, (int) $y + 2, $label, imagecolorallocatealpha($image, 0, 0, 0, 60));
            imagestring($image, $fontSize, (int) $x, (int) $y, $label, $white);
        }

        imagepng($image, $path);
        imagedestroy($image);
    }

    private function fontPath(): string
    {
        $candidates = [
            'C:/Windows/Fonts/arial.ttf',
            'C:/Windows/Fonts/segoeui.ttf',
            'C:/Windows/Fonts/DejaVuSans.ttf',
        ];

        foreach ($candidates as $font) {
            if (file_exists($font)) {
                return $font;
            }
        }

        return '';
    }
}
