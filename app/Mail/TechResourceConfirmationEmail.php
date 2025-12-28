<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TechResourceConfirmationEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $task;

    public $sender;

    public $actionType;

    /**
     * Create a new message instance.
     */
    public function __construct($task, $sender, $actionType = null)
    {
        $this->task = $task;
        $this->sender = $sender;
        $this->actionType = $actionType ?? ($task->allocation_type ?? 'allocation');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmation for new resource allocation',
            from: new Address($this->sender->email, $this->sender->name),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tech_resource_confirmation',
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
