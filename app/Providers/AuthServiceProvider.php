<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();
        Passport::tokensExpireIn(\Carbon\Carbon::now()->addDays(7));//You can also use addDays(10)
        Passport::refreshTokensExpireIn(\Carbon\Carbon::now()->addDays(7));//You can also use addDays(10)
        Passport::pruneRevokedTokens(); //basic garbage collector

        //
    }
}
