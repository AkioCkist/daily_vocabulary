<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\{
    WordRepositoryInterface,
    SubscriptionRepositoryInterface,
    UserWordRepositoryInterface
};
use App\Repositories\Eloquent\{
    EloquentWordRepository,
    EloquentSubscriptionRepository,
    EloquentUserWordRepository
};

class AppServiceProvider extends ServiceProvider
{
    
    /**
     * Register any application services.
     */
    public function register(): void
    {
    $this->app->bind(WordRepositoryInterface::class, EloquentWordRepository::class);
    $this->app->bind(SubscriptionRepositoryInterface::class, EloquentSubscriptionRepository::class);
    $this->app->bind(UserWordRepositoryInterface::class, EloquentUserWordRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
