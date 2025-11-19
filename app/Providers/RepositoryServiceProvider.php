<?php

namespace App\Providers;

use App\Repositories\Eloquent\WordRepository;
use App\Repositories\Interfaces\WordRepositoryInterface;
use Illuminate\Support\ServiceProvider;

/**
 * Service provider for repository bindings.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(WordRepositoryInterface::class, WordRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}