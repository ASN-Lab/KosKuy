<?php

namespace App\Providers;

use App\Models\KosListing;
use App\Models\ThriftProduct;
use App\Policies\KosListingPolicy;
use App\Policies\ThriftProductPolicy;
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
    }
}
