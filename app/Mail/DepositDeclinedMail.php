<?php

namespace App\Mail;

use App\Models\Deposit;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DepositDeclinedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $deposit;

    public function __construct(Deposit $deposit)
    {
        $this->deposit = $deposit;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Deposit Declined',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.deposit_declined',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
