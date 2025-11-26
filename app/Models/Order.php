<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'business_id',
        'customer_id',
        'order_number',
        'status',
        'payment_method',
        'total_amount',
        'notes',
        // Public order fields
        'customer_name',
        'customer_phone',
        'customer_email',
        'delivery_address',
        'quantity',
        'unit_price',
        'total_price',
        'order_type',
        'payment_status',
        'product_id',
        // Tax fields
        'subtotal',
        'tax',
        'tax_rate',
        'tax_inclusive',
        'tax_type',
        // Invoice fields
        'invoice_number',
        'invoice_generated_at',
        // Archive fields
        'is_archived',
        'archived_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_inclusive' => 'boolean',
        'is_archived' => 'boolean',
        'invoice_generated_at' => 'datetime',
        'archived_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }

    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case 'pending':
                return 'warning';
            case 'processing':
                return 'info';
            case 'completed':
                return 'success';
            case 'cancelled':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    public function getStatusTextAttribute()
    {
        return ucfirst($this->status);
    }

    public function getFormattedTotalAttribute()
    {
        return 'KSh ' . number_format((float) $this->total_amount, 2);
    }

    public function getFormattedSubtotalAttribute()
    {
        return 'KSh ' . number_format((float) $this->subtotal, 2);
    }

    public function getFormattedTaxAttribute()
    {
        return 'KSh ' . number_format((float) $this->tax, 2);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(10));
            }

            if (empty($order->total_amount) && ($order->order_type === 'public_order') && !empty($order->total_price)) {
                $order->total_amount = $order->total_price;
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Get the credit notes for this order.
     */
    public function creditNotes()
    {
        return $this->hasMany(CreditNote::class);
    }
}
