<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->registerGates();
    }

    private function registerGates()
    {
        Gate::define('can-operator', function ($user) {
            return $user->isOperator();
        });
        Gate::define('can-head', function ($user) {
            return $user->isHead();
        });
        Gate::define('can-host', function ($user) {
            return $user->isHost();
        });
    }
}
