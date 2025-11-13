<?php 

namespace App\Repositories\Eloquent;

use App\Models\Subscription;
use App\Repositories\Interfaces\SubscriptionRepositoryInterface;

class EloquentSubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function findByEmail(string $email): ?Subscription
    {
        return Subscription::where('email', $email)->first();
    }

    public function create(array $data): Subscription
    {
        return Subscription::create($data);
    }

    public function confirm(string $email): bool
    {
        return Subscription::where('email', $email)
            ->update(['confirmed_at' => now()]);
    }

    public function unsubscribe(string $email): bool
    {
        return Subscription::where('email', $email)
            ->update(['unsubscribed_at' => now()]);
    }
}
