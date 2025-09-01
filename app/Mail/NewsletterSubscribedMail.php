<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterSubscribedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->subject('Subscription Successful')
            ->view('emails.newsletter_subscribed');
    }
}


