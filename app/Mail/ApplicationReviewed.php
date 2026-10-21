<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** To the volunteer: the NPO approved or rejected the application. */
class ApplicationReviewed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Submission $submission)
    {
        $this->submission->loadMissing('event.organization');
    }

    public function envelope(): Envelope
    {
        $organization = $this->submission->event->organization;
        $approved = $this->submission->review_status === Submission::STATUS_APPROVED;

        return new Envelope(
            subject: ($approved ? 'You’re in! ' : 'Update on your application: ').$this->submission->event->title,
            replyTo: [new Address($organization->org_email, $organization->org_name)],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.application-reviewed',
            with: [
                'approved' => $this->submission->review_status === Submission::STATUS_APPROVED,
                'trackUrl' => route('track.index', ['code' => $this->submission->tracking_code]),
            ],
        );
    }
}