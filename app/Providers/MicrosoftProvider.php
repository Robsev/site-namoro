<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Microsoft\Provider;

class MicrosoftProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend(
            'microsoft',
            function ($app) use ($socialite) {
                $config = $app['config']['services.microsoft'];
                return $socialite->buildProvider(Provider::class, $config);
            }
        );
    }
}
