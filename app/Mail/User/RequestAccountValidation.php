<?php

namespace App\Mail\User;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequestAccountValidation extends Mailable
{
    use Queueable, SerializesModels;

    protected $dashboardUrl;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        $this->dashboardUrl = env('FRONTEND_URL') . '/admin/dashboard';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: env('MAIL_FROM_ADDRESS'),
            subject: "Vous avez une nouvelle demande d'inscription.",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user.request-account-validation',
            with: [
                'dashboardUrl' => $this->dashboardUrl
            ]
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
