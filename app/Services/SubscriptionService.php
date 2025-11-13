<?php

namespace App\Services;

use App\Repositories\Interfaces\SubscriptionRepositoryInterface;
use Illuminate\Support\Facades\Mail;

class SubscriptionService
{
    public function __construct(
        protected SubscriptionRepositoryInterface $repo
    ) {}

    public function subscribe(string $email)
    {
        $existing = $this->repo->findByEmail($email);
        if ($existing && !$existing->unsubscribed_at) {
            return $existing;
        }

        $subscription = $this->repo->create(['email' => $email]);

        // Gửi email xác nhận (giả lập)
        Mail::raw('Click to confirm your subscription!', function ($m) use ($email) {
            $m->to($email)->subject('Confirm your Daily Vocabulary subscription');
        });

        return $subscription;
    }

    public function confirm(string $email)
    {
        return $this->repo->confirm($email);
    }

    public function unsubscribe(string $email)
    {
        return $this->repo->unsubscribe($email);
    }
}
