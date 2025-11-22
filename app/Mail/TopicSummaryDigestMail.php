<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TopicSummaryDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function build()
    {
        return $this
            ->subject('Your Learning Topic Summary')
            ->markdown('emails.topic_summary_digest', [
                'user' => $this->user,
            ]);
    }
}
