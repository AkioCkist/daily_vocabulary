<?php

namespace App\Services;

use App\Repositories\Interfaces\SubscriptionRepositoryInterface;
use Illuminate\Support\Facades\Mail;

class SubscriptionService
{
    public function __construct(
        protected SubscriptionRepositoryInterface $repo
    ) {}

    public function subscribe(string $email, ?int $userId = null)
    {
        $existing = $this->repo->findByEmail($email);
        
        if ($existing) {
            // If user previously unsubscribed, re-subscribe them
            if ($existing->unsubscribed_at) {
                $existing->update([
                    'unsubscribed_at' => null,
                    'confirmed_at' => now(),
                    'user_id' => $userId,
                ]);
                
                // Send welcome back email
                Mail::mailer('smtp')->raw('Welcome back to Daily Vocabulary! You will receive a new word every day.', function ($m) use ($email) {
                    $m->to($email)->subject('Welcome Back to Daily Vocabulary!');
                });
                
                return $existing;
            }
            
            // If exists and not unsubscribed, confirm it immediately if not already confirmed
            if (!$existing->confirmed_at) {
                $existing->update(['confirmed_at' => now()]);
            }
            
            return $existing;
        }

        // Create new subscription with confirmed_at set to now() and link to user_id
        $subscription = $this->repo->create([
            'email' => $email,
            'user_id' => $userId,
            'confirmed_at' => now(), // Confirm immediately for authenticated users
        ]);

        // Send confirmation email instantly via Mailhog (smtp mailer)
        Mail::mailer('smtp')->raw('You are now subscribed to Daily Vocabulary! You will receive a new word every day.', function ($m) use ($email) {
            $m->to($email)->subject('Welcome to Daily Vocabulary!');
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

    public function isSubscribed(string $email): bool
    {
        $subscription = $this->repo->findByEmail($email);
        
        if (!$subscription) {
            return false;
        }

        // User is subscribed if they have confirmed and not unsubscribed
        return $subscription->confirmed_at !== null && $subscription->unsubscribed_at === null;
    }
}
