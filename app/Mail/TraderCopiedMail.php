<?php

namespace App\Mail;

use App\Models\Trader;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TraderCopiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $trader;

    public function __construct(User $user, Trader $trader)
    {
        $this->user = $user;
        $this->trader = $trader;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You Are Now Copying a Trader',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.trader_copied',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
