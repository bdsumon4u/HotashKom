<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMessage extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $contact
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [
                new Address(
                    $this->contact['email'],
                    $this->contact['name']
                ),
            ],
            subject: '['.(config('app.name') ?: 'Contact').'] '.$this->contact['subject'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-form-message',
            with: [
                'contact' => $this->contact,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
