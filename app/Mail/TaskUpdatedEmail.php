<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskUpdatedEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $task;
    public $sender;
    public $actionType;

    /**
     * Create a new message instance.
     */
    public function __construct($task, $sender, $actionType)
    {
        $this->task = $task;
        $this->sender = $sender;
        $this->actionType = $actionType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Updated: Resource '.ucfirst($this->actionType).' Recommendation',
            from: new Address($this->sender->email, $this->sender->name),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.task_updated',
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
