<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\AssetRepositoryInterface::class,
            \App\Repositories\Eloquent\AssetRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\SparePartRepositoryInterface::class,
            \App\Repositories\Eloquent\SparePartRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\UserRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\CartyRepositoryInterface::class,
            \App\Repositories\Eloquent\CartyRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
