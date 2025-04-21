<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MissionEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param array $mission An associative array containing:
     *   - start_date: string
     *   - end_date: string
     *   - purpose: string
     *   - description: string
     *   - schools: array
     */
    public function __construct(private array $mission)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'អ្នកត្រូវបានអញ្ជើញឲ្យចូលរួមបេសកកម្មនៅ ' . formatKhmerDate($this->mission['start_date'])
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.mission-email',
            with: [
                'start_date'  => $this->mission['start_date'],
                'end_date'    => $this->mission['end_date'],
                'purpose'     => $this->mission['purpose'],
                'description' => $this->mission['description'],
                'schools'     => $this->mission['schools']
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
