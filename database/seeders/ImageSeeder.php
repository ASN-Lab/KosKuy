<?php

namespace Database\Seeders;

use App\Models\KosImage;
use App\Models\KosListing;
use App\Models\ThriftImage;
use App\Models\ThriftProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * Attaches listing photos to the seeded demo data.
 *
 * The image files themselves are not generated: they are real, freely-licensed
 * photographs downloaded once by `php scripts/fetch-demo-photos.php` and stored
 * in storage/app/public. This seeder only wires the rows, and it is written to
 * degrade gracefully so a fresh clone without the photos still migrates.
 *
 * Attribution for every file lives in storage/app/public/PHOTO-CREDITS.md.
 */
class ImageSeeder extends Seeder
{
    /**
     * Three angles per kos listing, matched to the room photos fetched for it.
     *
     * @var array<int, string[]>
     */
    private array $kosAngles = [1, 2, 3];

    public function run(): void
    {
        $storagePath = storage_path('app/public');
        $imagesPath = "$storagePath/kos";
        $thriftPath = "$storagePath/thrift";

        File::ensureDirectoryExists($imagesPath);
        File::ensureDirectoryExists($thriftPath);

        // Clear artwork from earlier runs so renamed files never linger.
        File::delete(File::glob("$imagesPath/*.png"));
        File::delete(File::glob("$imagesPath/*.svg"));
        File::delete(File::glob("$thriftPath/*.png"));
        File::delete(File::glob("$thriftPath/*.svg"));

        KosImage::query()->delete();
        ThriftImage::query()->delete();

        foreach (KosListing::all() as $kos) {
            foreach ($this->kosAngles as $n) {
                $relative = "kos/kos-{$kos->id}-{$n}.jpg";

                if (! File::exists("$storagePath/$relative")) {
                    $this->missing($relative);

                    continue;
                }

                KosImage::create([
                    'kos_id' => $kos->id,
                    'image_path' => $relative,
                ]);
            }
        }

        foreach (ThriftProduct::all() as $product) {
            $relative = "thrift/thrift-{$product->id}.jpg";

            if (! File::exists("$storagePath/$relative")) {
                $this->missing($relative);

                continue;
            }

            ThriftImage::create([
                'thrift_id' => $product->id,
                'image_path' => $relative,
            ]);
        }
    }

    private function missing(string $relative): void
    {
        $this->command?->warn("  photo missing: {$relative} (run: php scripts/fetch-demo-photos.php)");
    }
}
