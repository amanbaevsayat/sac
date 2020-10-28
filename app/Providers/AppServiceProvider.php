<?php

namespace App\Providers;

use App\Services\CloudPaymentsService;
use App\Services\GitService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerServices();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    private function registerServices()
    {
        $this->app->singleton(CloudPaymentsService::class);
        $this->app->singleton(GitService::class);
    }
}
