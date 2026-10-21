<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** To the NPO: a volunteer just applied. */
class NewApplicationAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Submission $submission)
    {
        $this->submission->loadMissing('event.organization');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New volunteer application: '.$this->submission->event->title,
            replyTo: [new Address($this->submission->volunteer_email, $this->submission->volunteer_name)],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new-application',
            with: [
                'reviewUrl' => route('npo.applications.show', $this->submission),
            ],
        );
    }
}