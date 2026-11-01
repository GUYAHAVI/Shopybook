<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use App\Models\Quotation;

class QuotationMail extends Mailable
{
    public Quotation $quote;

    public function __construct(Quotation $quote)
    {
        $this->quote = $quote->loadMissing(['customer', 'items', 'business']);
    }

    public function envelope(): Envelope
    {
        $business = $this->quote->business;
        $sender = $business->email ? new Address($business->email, $business->business_name) : null;

        return new Envelope(
            from: $sender,
            replyTo: $sender ? [$sender] : [],
            subject: 'Quotation ' . $this->quote->quote_number . ' — ' . $business->business_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quotation',
            with: [
                'quote' => $this->quote,
                'business' => $this->quote->business,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
