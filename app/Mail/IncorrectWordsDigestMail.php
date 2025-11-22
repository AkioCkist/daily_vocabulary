<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IncorrectWordsDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function build()
    {
        return $this
            ->subject('Your Frequently Incorrect Words')
            ->markdown('emails.incorrect_words_digest', [
                'user' => $this->user,
            ]);
    }
}
