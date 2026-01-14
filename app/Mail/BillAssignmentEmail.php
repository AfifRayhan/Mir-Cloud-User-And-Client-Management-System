<?php

namespace App\Mail;

use App\Models\ResourceTransfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BillAssignmentEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $transfer;

    public $sender;

    public $customer;

    public $task;

    /**
     * Create a new message instance.
     */
    public function __construct(ResourceTransfer $transfer, $sender, $task = null)
    {
        $this->transfer = $transfer;
        $this->sender = $sender;
        $this->customer = $transfer->customer;
        $this->task = $task;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Transfer Assignment',
            from: new Address($this->sender->email, $this->sender->name),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.bill_assignment',
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
