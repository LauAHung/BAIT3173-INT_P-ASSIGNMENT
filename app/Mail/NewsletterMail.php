<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    private string $dynamicSubject;
    private string $contentHtml;
    private $user;

    public function __construct(string $subject, string $content, $user = null)
    {
        $this->dynamicSubject = $subject;
        $this->contentHtml = $content;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject($this->dynamicSubject)
            ->view('emails.newsletter_general')
            ->with([
                'content' => $this->contentHtml,
                'user' => $this->user,
            ]);
    }
}


