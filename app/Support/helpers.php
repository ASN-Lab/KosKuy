<?php

use App\Models\Category;
use App\Models\KosListing;
use App\Models\ThriftProduct;

/*
|--------------------------------------------------------------------------
| KosKuy view helpers
|--------------------------------------------------------------------------
| Small presentation helpers shared by the storefront Blade templates.
| Loaded through composer's `files` autoloader.
*/

if (! function_exists('rupiah')) {
    /**
     * Format a number as Indonesian Rupiah, e.g. 1200000 => "Rp 1.200.000".
     */
    function rupiah(int|float|string|null $amount, bool $withPrefix = true): string
    {
        $value = (float) ($amount ?? 0);
        $formatted = number_format($value, 0, ',', '.');

        return $withPrefix ? 'Rp ' . $formatted : $formatted;
    }
}

if (! function_exists('rupiahShort')) {
    /**
     * Compact Rupiah for tight spaces, e.g. 1200000 => "Rp 1,2 jt".
     */
    function rupiahShort(int|float|string|null $amount): string
    {
        $value = (float) ($amount ?? 0);

        return match (true) {
            $value >= 1_000_000 => 'Rp ' . rtrim(rtrim(number_format($value / 1_000_000, 1, ',', ''), '0'), ',') . ' jt',
            $value >= 1_000 => 'Rp ' . number_format($value / 1_000, 0, ',', '.') . ' rb',
            default => rupiah($value),
        };
    }
}

if (! function_exists('compactNumber')) {
    /**
     * Short social-proof numbers, e.g. 12500 => "12,5rb".
     */
    function compactNumber(int|float|null $number): string
    {
        $value = (float) ($number ?? 0);

        return match (true) {
            $value >= 1_000_000 => rtrim(rtrim(number_format($value / 1_000_000, 1, ',', ''), '0'), ',') . ' jt',
            $value >= 1_000 => rtrim(rtrim(number_format($value / 1_000, 1, ',', ''), '0'), ',') . ' rb',
            default => number_format($value, 0, ',', '.'),
        };
    }
}

if (! function_exists('genderLabel')) {
    /**
     * Human label for the kos gender_type enum.
     */
    function genderLabel(?string $type): string
    {
        return match ($type) {
            'putra' => 'Putra',
            'putri' => 'Putri',
            'campur' => 'Campur',
            default => 'Semua',
        };
    }
}

if (! function_exists('genderTone')) {
    /**
     * Tailwind classes for the gender badge so cards stay visually consistent.
     */
    function genderTone(?string $type): string
    {
        return match ($type) {
            'putra' => 'bg-sky-50 text-sky-700 ring-sky-100',
            'putri' => 'bg-rose-50 text-rose-700 ring-rose-100',
            'campur' => 'bg-violet-50 text-violet-700 ring-violet-100',
            default => 'bg-ink-100 text-ink-600 ring-ink-200',
        };
    }
}

if (! function_exists('genderIcon')) {
    /**
     * Icon name for the gender badge.
     */
    function genderIcon(?string $type): string
    {
        return match ($type) {
            'putra' => 'users',
            'putri' => 'sparkles',
            default => 'users',
        };
    }
}

if (! function_exists('waNumber')) {
    /**
     * Normalise an Indonesian phone number into wa.me format (62xxx).
     */
    function waNumber(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === null || $digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        } elseif (! str_starts_with($digits, '62')) {
            $digits = '62' . $digits;
        }

        return $digits;
    }
}

if (! function_exists('localPhone')) {
    /**
     * Format an Indonesian phone number the way it is written locally, e.g.
     * 08978889402 or 628978889402 both render as 0897-8889-402.
     *
     * waNumber() produces the international digits that deep links need; this is
     * the display counterpart, so a printed flyer never shows a bare 62 number.
     */
    function localPhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === null || $digits === '') {
            return null;
        }

        if (str_starts_with($digits, '62')) {
            $digits = '0' . substr($digits, 2);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '0' . $digits;
        }

        if (strlen($digits) < 9) {
            return $digits;
        }

        return substr($digits, 0, 4) . '-' . substr($digits, 4, 4) . '-' . substr($digits, 8);
    }
}

if (! function_exists('waLink')) {
    /**
     * Deep link that opens WhatsApp with a pre-filled message.
     */
    function waLink(?string $phone, string $message = ''): ?string
    {
        $number = waNumber($phone);

        if ($number === null) {
            return null;
        }

        $url = 'https://wa.me/' . $number;

        return $message !== '' ? $url . '?text=' . rawurlencode($message) : $url;
    }
}

if (! function_exists('initials')) {
    /**
     * Two-letter avatar fallback from a person's name.
     */
    function initials(?string $name): string
    {
        $name = trim((string) $name);

        if ($name === '') {
            return '?';
        }

        $parts = preg_split('/\s+/', $name) ?: [];
        $first = mb_substr($parts[0] ?? '', 0, 1);
        $second = count($parts) > 1 ? mb_substr($parts[1] ?? '', 0, 1) : '';

        return mb_strtoupper($first . $second);
    }
}

if (! function_exists('listingImage')) {
    /**
     * First image URL for a listing, or null when it has no photos.
     *
     * @param  KosListing|ThriftProduct  $listing
     */
    function listingImage($listing): ?string
    {
        $image = $listing->images->first();

        return $image?->image_path ? asset('storage/' . $image->image_path) : null;
    }
}

if (! function_exists('imageCount')) {
    /**
     * @param  KosListing|ThriftProduct  $listing
     */
    function imageCount($listing): int
    {
        return $listing->images->count();
    }
}

if (! function_exists('facilityIcon')) {
    /**
     * Map a free-text facility name onto one of the bundled icons.
     */
    function facilityIcon(?string $facility): string
    {
        $name = mb_strtolower((string) $facility);

        // Collapse separators so "wi-fi", "AC / Pendingin", and "kamar_mandi"
        // all match the same rules.
        $key = preg_replace('/[^a-z0-9]+/', '', $name) ?: $name;

        return match (true) {
            str_contains($key, 'wifi') || str_contains($key, 'internet') => 'wifi',
            str_contains($key, 'ac') || str_contains($key, 'pendingin') => 'cloud',
            str_contains($key, 'kamarmandi') || str_contains($key, 'bathroom') => 'inbox-stack',
            str_contains($key, 'dapur') || str_contains($key, 'kitchen') => 'home',
            str_contains($key, 'laundry') || str_contains($key, 'cuci') => 'cube-transparent',
            str_contains($key, 'parkir') || str_contains($key, 'parking') => 'truck',
            str_contains($key, 'kas') || str_contains($key, 'security') || str_contains($key, 'cctv') => 'shield-check',
            str_contains($key, 'meja') || str_contains($key, 'kursi') || str_contains($key, 'furnitur') => 'square-3-stack-3d',
            str_contains($key, 'rak') || str_contains($key, 'lemari') || str_contains($key, 'storage') => 'square-2-stack',
            str_contains($key, 'listrik') || str_contains($key, 'pln') => 'bolt',
            str_contains($key, 'air') || str_contains($key, 'water') => 'bolt-slash',
            str_contains($key, 'taman') || str_contains($key, 'garden') => 'sun',
            str_contains($key, 'musala') || str_contains($key, 'sholat') => 'hand-raised',
            str_contains($key, 'baca') || str_contains($key, 'perpustakaan') => 'book-open',
            default => 'check-badge',
        };
    }
}

if (! function_exists('conditionTone')) {
    /**
     * Badge tone for a thrift condition_status string.
     */
    function conditionTone(?string $condition): string
    {
        $value = mb_strtolower((string) $condition);

        return match (true) {
            str_contains($value, '95') || str_contains($value, '100') || str_contains($value, 'baru') || str_contains($value, 'new') => 'bg-accent-50 text-accent-700 ring-accent-100',
            str_contains($value, '80') || str_contains($value, '85') || str_contains($value, 'layak') => 'bg-sun-100 text-sun-700 ring-sun-200',
            default => 'bg-ink-100 text-ink-600 ring-ink-200',
        };
    }
}

if (! function_exists('categoryIcon')) {
    /**
     * Icon for a thrift category, falling back to a tag.
     */
    function categoryIcon(?Category $category): string
    {
        $name = mb_strtolower((string) ($category?->slug ?? ''));

        return match (true) {
            str_contains($name, 'buku') => 'book-open',
            str_contains($name, 'elektronik') => 'device-phone-mobile',
            str_contains($name, 'furnitur') => 'square-3-stack-3d',
            str_contains($name, 'alat') || str_contains($name, 'rumah') => 'home',
            str_contains($name, 'fashion') || str_contains($name, 'pakaian') => 'tag',
            str_contains($name, 'olahraga') || str_contains($name, 'sport') => 'trophy',
            default => 'tag',
        };
    }
}

if (! function_exists('shareUrl')) {
    /**
     * Canonical URL for social sharing.
     */
    function shareUrl(string $path): string
    {
        return rtrim(config('app.url'), '/') . '/' . ltrim($path, '/');
    }
}

if (! function_exists('mapsEmbedUrl')) {
    /**
     * Keyless OpenStreetMap embed for a coordinate pair.
     */
    function mapsEmbedUrl(?float $lat, ?float $lng, int $zoom = 15): ?string
    {
        if (! $lat || ! $lng) {
            return null;
        }

        $bbox = sprintf(
            '%F,%F,%F,%F',
            $lng - 0.01,
            $lat - 0.006,
            $lng + 0.01,
            $lat + 0.006,
        );

        return 'https://www.openstreetmap.org/export/embed.html?bbox=' . $bbox
            . '&layer=mapnik&marker=' . $lat . ',' . $lng;
    }
}

if (! function_exists('mapsLink')) {
    /**
     * Directions deep link that works without any API key.
     */
    function mapsLink(?float $lat, ?float $lng): ?string
    {
        if (! $lat || ! $lng) {
            return null;
        }

        return 'https://www.google.com/maps/search/?api=1&query=' . $lat . ',' . $lng;
    }
}

if (! function_exists('siteUrl')) {
    /**
     * Absolute URL to a path on this site, preferring the configured
     * KOSKUY_URL so printed material (the flyer) keeps a stable domain even when
     * the app is reached over a LAN address or `php artisan serve`.
     */
    function siteUrl(string $path = ''): string
    {
        $base = rtrim((string) config('koskuy.url'), '/');

        if ($base === '') {
            $base = rtrim((string) config('app.url'), '/');
        }

        if ($base === '' && function_exists('request')) {
            $base = rtrim(request()->root(), '/');
        }

        $path = '/'.ltrim($path, '/');

        return ($base ?: '').($path === '/' ? '' : $path);
    }
}

if (! function_exists('siteHost')) {
    /**
     * Bare hostname for on-screen display, e.g. "koskuy.id".
     */
    function siteHost(): string
    {
        $url = (string) config('koskuy.url');

        if ($url === '') {
            return (string) config('app.name');
        }

        $host = parse_url($url, PHP_URL_HOST);

        if (is_string($host) && $host !== '') {
            // Strip a leading "www." so the printed domain stays short.
            return preg_replace('/^www\./i', '', $host);
        }

        return ltrim($url, '/');
    }
}

if (! function_exists('qrImageUrl')) {
    /**
     * Resolves KOSKUY_NEWSLETTER_QR into a usable image URL, or null.
     *
     * Accepts a root-relative path, a public/ relative path, or an absolute
     * http(s) URL so a deployment can point at whichever it prefers.
     */
    function qrImageUrl(): ?string
    {
        $value = trim((string) config('koskuy.newsletter_qr'));

        if ($value === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $value) === 1) {
            return $value;
        }

        // "images/x.png" is a public/ path; "/images/x.png" is already rooted.
        if (str_starts_with($value, '/')) {
            return $value;
        }

        return asset($value);
    }
}