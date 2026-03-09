<?php

namespace App\Mail;

use App\Models\Testimonial;
use App\Models\Business;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestimonialReceivedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Testimonial $testimonial,
        public Business $business,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Customer Review — ' . $this->business->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.testimonial-received',
            with: [
                'testimonial' => $this->testimonial,
                'business'    => $this->business,
                'approveUrl'  => route('testimonials.owner.approve', $this->testimonial->id),
                'rejectUrl'   => route('testimonials.owner.reject', $this->testimonial->id),
                'manageUrl'   => route('testimonials.owner.index'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
