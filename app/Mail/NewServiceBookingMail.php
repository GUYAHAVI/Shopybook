<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\ServiceBooking;

class NewServiceBookingMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public ServiceBooking $serviceBooking;

    /**
     * Create a new message instance.
     */
    public function __construct(ServiceBooking $serviceBooking)
    {
        $this->serviceBooking = $serviceBooking;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Service Booking - ' . $this->serviceBooking->business->business_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-service-booking',
            with: [
                'serviceBooking' => $this->serviceBooking,
                'business' => $this->serviceBooking->business,
                'customer' => $this->serviceBooking->customer,
                'serviceItems' => $this->serviceBooking->serviceItems()->with('service', 'staff')->get(),
            ],
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