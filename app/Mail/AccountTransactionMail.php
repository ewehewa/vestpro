<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Credit / debit alert sent for every balance change made through BalanceService. */
class AccountTransactionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Transaction $transaction)
    {
    }

    public function envelope(): Envelope
    {
        $sign = $this->transaction->isCredit() ? '+' : '-';
        $kind = $this->transaction->isCredit() ? 'Credit' : 'Debit';

        return new Envelope(
            subject: "{$kind} Alert: {$sign}$" . number_format($this->transaction->amount, 2),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.account_transaction',
            with: [
                'transaction' => $this->transaction,
                'user' => $this->transaction->user,
            ],
        );
    }
}
