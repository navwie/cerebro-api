<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Inertia::setRootView('app');
        Paginator::useBootstrap();
        if ($this->app->environment('production', 'dev')) {
            $this->app['request']->server->set('HTTPS','on');
            Url::forceScheme('https');
        }
    }
}
