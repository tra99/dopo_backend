<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportTrackingEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(private array $trackingReport)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $schools = $this->trackingReport['schools'];
        if (count($schools) > 1) {
            $lastSchool = array_pop($schools);
            $schoolsString = implode(', ', $schools);
            $schoolsString .= ' និង ' . $lastSchool;
        } else {
            $schoolsString = $schools[0];
        }

        return new Envelope(
            subject: 'ទទួលបានរបាយការណ៏នៃការចុះគាំទ្រសាលារៀនពី ' . $schoolsString,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $schools = $this->trackingReport['schools'];
        if (count($schools) > 1) {
            $lastSchool = array_pop($schools);
            $schoolsString = implode(', ', $schools);
            $schoolsString .= ' និង ' . $lastSchool;
        } else {
            $schoolsString = $schools[0];
        }
        return new Content(
            view: 'mail.report-tracking-email',
            with: [
                'mission_purpose' => $this->trackingReport['mission_purpose'],
                'start_date' => $this->trackingReport['start_date'],
                'end_date' => $this->trackingReport['end_date'],
                'schools' => $schoolsString,
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
