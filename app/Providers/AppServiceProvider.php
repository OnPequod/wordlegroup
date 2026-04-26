<?php

namespace App\Providers;

use App\Concerns\GetsUserGroupsWithRelationshipsLoaded;
use App\Concerns\GetsUsersInSharedGroupsWithAuthenticatedUser;
use App\Database\Connectors\MySqlConnector;
use App\Services\AuthenticatedUserService;
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
        $this->app->bind('db.connector.mysql', MySqlConnector::class);

        $this->app->singleton(AuthenticatedUserService::class, function ($app) {
            return new AuthenticatedUserService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(GetsUsersInSharedGroupsWithAuthenticatedUser::class, function ($app) {
            return new GetsUsersInSharedGroupsWithAuthenticatedUser();
        });
    }
}
