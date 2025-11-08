<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Produksi;
use App\Observers\ProduksiObserver;

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
        Produksi::observe(ProduksiObserver::class);
    }
}
