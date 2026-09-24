<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
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
        // The production server's MySQL/MariaDB limits index keys to 767 bytes. With utf8mb4
        // (4 bytes per character) an indexed string column can hold at most 191 characters.
        Schema::defaultStringLength(191);
    }
}
