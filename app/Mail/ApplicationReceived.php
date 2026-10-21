<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** To the volunteer: "We received your application" + tracking code. */
class ApplicationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Submission $submission)
    {
        $this->submission->loadMissing('event.organization');
    }

    public function envelope(): Envelope
    {
        $organization = $this->submission->event->organization;

        return new Envelope(
            subject: 'Application received: '.$this->submission->event->title,
            replyTo: [new Address($organization->org_email, $organization->org_name)],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.application-received',
            with: [
                'trackUrl' => route('track.index', ['code' => $this->submission->tracking_code]),
            ],
        );
    }
}