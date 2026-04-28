<?php

namespace App\Providers;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Codespaces / リバースプロキシ越しの HTTPS を強制
        if (str_contains(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
