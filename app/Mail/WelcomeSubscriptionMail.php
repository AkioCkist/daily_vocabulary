<?php

namespace App\Mail;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeSubscriptionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ?User $user = null,
        public ?Subscription $subscription = null,
    ) {}

    public function build()
    {
        return $this
            ->subject('Welcome to Daily Vocabulary!')
            ->markdown('emails.welcome', [
                'user' => $this->user,
                'subscription' => $this->subscription,
            ]);
    }
}
