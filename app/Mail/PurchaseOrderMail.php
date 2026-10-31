<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use App\Models\PurchaseOrder;

class PurchaseOrderMail extends Mailable
{
    public PurchaseOrder $po;

    public function __construct(PurchaseOrder $po)
    {
        $this->po = $po->loadMissing(['supplier', 'items', 'business']);
    }

    public function envelope(): Envelope
    {
        $business = $this->po->business;
        $sender = $business->email ? new Address($business->email, $business->business_name) : null;

        return new Envelope(
            from: $sender,
            replyTo: $sender ? [$sender] : [],
            subject: 'Purchase Order ' . $this->po->po_number . ' — ' . $business->business_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.purchase-order',
            with: [
                'po' => $this->po,
                'business' => $this->po->business,
                'supplier' => $this->po->supplier,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
