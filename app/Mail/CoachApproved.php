<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CoachApproved extends Mailable
{
    public function __construct(public User $coach)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your coach account has been approved');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.coach-approved');
    }
}