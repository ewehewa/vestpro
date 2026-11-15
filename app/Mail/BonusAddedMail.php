<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BonusAddedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $amount;

    public function __construct(User $user, float $amount)
    {
        $this->user = $user;
        $this->amount = $amount;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bonus Added to Your Account',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.bonus_added',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
