<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $product;
    public $business;

    /**
     * Create a new message instance.
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
        $this->business = $product->business;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $status = $this->product->stock_quantity <= 0 ? 'Out of Stock' : 'Low Stock';
        
        return new Envelope(
            subject: "⚠️ {$status} Alert: {$this->product->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.low-stock-alert',
            with: [
                'productName' => $this->product->name,
                'sku' => $this->product->sku,
                'currentStock' => $this->product->stock_quantity,
                'lowStockThreshold' => $this->product->low_stock_threshold ?? 10,
                'category' => $this->product->category ?? 'Uncategorized',
                'brand' => $this->product->brand,
                'businessName' => $this->business->name,
                'isOutOfStock' => $this->product->stock_quantity <= 0,
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







