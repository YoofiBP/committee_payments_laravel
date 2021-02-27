<?php

namespace App\Providers;

use App\Services\EloquentContributionService;
use App\Services\EloquentEventService;
use App\Services\EloquentUserService;
use App\Services\Interfaces\ContributionServiceInterface;
use App\Services\Interfaces\EventServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserServiceInterface::class, EloquentUserService::class);
        $this->app->bind(ContributionServiceInterface::class, EloquentContributionService::class);
        $this->app->bind(EventServiceInterface::class, EloquentEventService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
