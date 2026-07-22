<?php

namespace App\Providers;

use App\Models\Seo;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
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
        View::composer('*', function ($view) {
            $seoSettings = Cache::rememberForever('global_seo_settings', function () {
                return Seo::first();
            });
            $view->with('globalSeo', $seoSettings);
        });
    }
}
