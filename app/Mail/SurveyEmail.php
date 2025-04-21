<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SurveyEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param array $survey An associative array containing:
     *                      - title: string
     *                      - start_date: string (date format)
     *                      - end_date: string (date format)
     *                      - school_type: string
     *                      - description: string
     */
    public function __construct(private array $survey)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ការវាយតម្លៃរបស់សាលារៀន​ថ្មីបានដាក់ឲ្យបំពេញនៅ​ '
                . formatKhmerDate($this->survey['start_date'])
                . " សម្រាប់សាលារៀន "
                . $this->survey['school_type']
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.survey-email', // Replace with your actual view name
            with: [
                'title' => $this->survey['title'],
                'start_date' => $this->survey['start_date'],
                'end_date' => $this->survey['end_date'],
                'school_type' => $this->survey['school_type'],
                'description' => $this->survey['description'],
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
