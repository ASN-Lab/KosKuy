<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\KosFacility;
use App\Models\KosListing;
use App\Models\ThriftProduct;
use App\Models\User;
use App\Policies\KosListingPolicy;
use App\Policies\ThriftProductPolicy;
use App\Support\StorefrontCache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(KosListing::class, KosListingPolicy::class);
        Gate::policy(ThriftProduct::class, ThriftProductPolicy::class);

        $this->registerStorefrontCacheInvalidation();
    }

    /**
     * Drop the cached storefront reads whenever the rows they render change,
     * so an owner's Filament edit shows up on the next page load instead of
     * waiting out the TTL.
     */
    private function registerStorefrontCacheInvalidation(): void
    {
        KosListing::saved(fn () => StorefrontCache::bump('kos', 'home'));
        KosListing::deleted(fn () => StorefrontCache::bump('kos', 'home'));

        KosFacility::saved(fn () => StorefrontCache::bump('kos'));
        KosFacility::deleted(fn () => StorefrontCache::bump('kos'));

        ThriftProduct::saved(fn () => StorefrontCache::bump('thrift', 'home'));
        ThriftProduct::deleted(fn () => StorefrontCache::bump('thrift', 'home'));

        Category::saved(fn () => StorefrontCache::bump('categories', 'home', 'thrift'));
        Category::deleted(fn () => StorefrontCache::bump('categories', 'home', 'thrift'));

        User::saved(fn () => StorefrontCache::bump('kos', 'thrift', 'home'));
        User::deleted(fn () => StorefrontCache::bump('kos', 'thrift', 'home'));
    }
}
