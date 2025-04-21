<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SchoolEvaluationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(private array $schoolEvaluation)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ទទួលបានការវាយតម្លៃពី ' . $this->schoolEvaluation['school_type_kh'] . $this->schoolEvaluation['school_name_kh'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.school-evaluation-email',
            with: [
                'survey_title' => $this->schoolEvaluation['survey_title'],
                'start_date' => $this->schoolEvaluation['start_date'],
                'end_date' => $this->schoolEvaluation['end_date'],
                'school_name' => $this->schoolEvaluation['school_name_kh'],
                'school_type' => $this->schoolEvaluation['school_type_kh'],
                'province' => $this->schoolEvaluation['province_kh'],
                'score' => $this->schoolEvaluation['score'],
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
