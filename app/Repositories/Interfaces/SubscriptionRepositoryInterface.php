<?php 

namespace App\Repositories\Interfaces;

use App\Models\Subscription;

interface SubscriptionRepositoryInterface
{
    public function findByEmail(string $email): ?Subscription;
    public function create(array $data): Subscription;
    public function confirm(string $email): bool;
    public function unsubscribe(string $email): bool;
}
