<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'tax_enabled',
        'tax_type',
        'tax_rate',
        'tax_number',
        'tax_name',
        'tax_inclusive',
        'tax_period',
        'tax_exemption_items',
        'show_tax_on_receipt',
        'separate_tax_column',
    ];

    protected $casts = [
        'tax_enabled' => 'boolean',
        'tax_rate' => 'decimal:2',
        'tax_inclusive' => 'boolean',
        'tax_exemption_items' => 'array',
        'show_tax_on_receipt' => 'boolean',
        'separate_tax_column' => 'boolean',
    ];

    /**
     * Get the business that owns the tax settings
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Calculate tax amount from subtotal
     */
    public function calculateTax($subtotal, $taxableAmount = null)
    {
        if (!$this->tax_enabled) {
            return 0;
        }

        $amount = $taxableAmount ?? $subtotal;
        
        if ($this->tax_inclusive) {
            // If tax is inclusive, extract tax from total
            // Tax = (Total * Tax Rate) / (100 + Tax Rate)
            return ($amount * $this->tax_rate) / (100 + $this->tax_rate);
        } else {
            // If tax is exclusive, calculate tax on subtotal
            // Tax = Subtotal * (Tax Rate / 100)
            return $amount * ($this->tax_rate / 100);
        }
    }

    /**
     * Get total amount including tax
     */
    public function getTotalWithTax($subtotal)
    {
        if (!$this->tax_enabled) {
            return $subtotal;
        }

        if ($this->tax_inclusive) {
            // If tax inclusive, total is same as subtotal
            return $subtotal;
        } else {
            // If tax exclusive, add tax to subtotal
            return $subtotal + $this->calculateTax($subtotal);
        }
    }

    /**
     * Get subtotal without tax (if tax inclusive)
     */
    public function getSubtotalWithoutTax($total)
    {
        if (!$this->tax_enabled || !$this->tax_inclusive) {
            return $total;
        }

        // Subtotal = Total / (1 + Tax Rate/100)
        return $total / (1 + ($this->tax_rate / 100));
    }

    /**
     * Check if product is tax exempt
     */
    public function isProductExempt($productId)
    {
        if (!$this->tax_exemption_items) {
            return false;
        }

        return in_array($productId, $this->tax_exemption_items);
    }

    /**
     * Get formatted tax rate
     */
    public function getFormattedTaxRateAttribute()
    {
        return number_format($this->tax_rate, 2) . '%';
    }

    /**
     * Get tax period label
     */
    public function getTaxPeriodLabelAttribute()
    {
        return ucfirst($this->tax_period);
    }
}
