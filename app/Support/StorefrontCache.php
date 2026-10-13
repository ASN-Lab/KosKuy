<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * Version-keyed cache for the public storefront reads.
 *
 * The demo data lives on Supabase in ap-northeast-1, so every query costs
 * ~300ms of round-trip. Caching a page's data turns a warm load into a single
 * local file read. The file store has no tag support, so each "domain" carries
 * a version number instead; bumping it orphans every key under that domain at
 * once when the underlying rows change.
 */
class StorefrontCache
{
    /** Seconds a storefront entry stays fresh. Writes bump the version, so a
     *  long TTL is safe: edited rows still show on the very next request. */
    private const TTL = 3600;

    public static function version(string $domain): int
    {
        return (int) Cache::get(self::versionKey($domain), 1);
    }

    public static function bump(string ...$domains): void
    {
        foreach ($domains as $domain) {
            Cache::forever(self::versionKey($domain), self::version($domain) + 1);
        }
    }

    public static function remember(string $domain, string $key, Closure $callback): mixed
    {
        return Cache::remember(
            "storefront.{$domain}.v" . self::version($domain) . '.' . $key,
            self::TTL,
            $callback
        );
    }

    private static function versionKey(string $domain): string
    {
        return "storefront.{$domain}.version";
    }
}
