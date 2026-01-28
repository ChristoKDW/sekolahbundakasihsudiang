<?php

namespace App\Mail;

use App\Models\Bill;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Bill $bill;
    public int $daysUntilDue;

    /**
     * Create a new message instance.
     */
    public function __construct(Bill $bill, int $daysUntilDue = 0)
    {
        $this->bill = $bill;
        $this->daysUntilDue = $daysUntilDue;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->daysUntilDue > 0 
            ? "Pengingat: Tagihan {$this->bill->billType->name} Jatuh Tempo {$this->daysUntilDue} Hari Lagi"
            : "Pengingat: Tagihan {$this->bill->billType->name} Sudah Jatuh Tempo";

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-reminder',
            with: [
                'bill' => $this->bill,
                'daysUntilDue' => $this->daysUntilDue,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
