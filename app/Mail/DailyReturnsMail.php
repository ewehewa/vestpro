<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * One summary email per user per payout run, listing every investment return
 * (and any capital returned) credited in that run.
 */
class DailyReturnsMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array<int, array{plan: string, day: int, days: int, amount: float, kind: string}> $items
     */
    public function __construct(
        public User $user,
        public array $items,
        public float $total,
        public float $balance,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Credit Alert: Investment returns +$' . number_format($this->total, 2),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'email.daily_returns');
    }
}
