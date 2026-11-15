<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProfitToppedUpMail extends Mailable
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
            subject: 'Profit Added to Your Account',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.profit_topup',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
