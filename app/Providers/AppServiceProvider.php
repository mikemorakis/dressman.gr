<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Product;
use App\Observers\SlugRedirectObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        Product::observe(SlugRedirectObserver::class);
        Category::observe(SlugRedirectObserver::class);

        RateLimiter::for('checkout', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('stripe-webhook', function (Request $request) {
            return Limit::perMinute(100)->by($request->ip());
        });
    }
}
