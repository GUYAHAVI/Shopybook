<?php

namespace App\Services;

use App\Models\Product;
use App\Models\TaxSetting;

/**
 * Single source of truth for sales tax maths.
 *
 * Each line is ['product' => Product|null, 'quantity' => float, 'price' => float, 'product_id' => int|null].
 * Returns subtotal (net of tax), tax, total (gross) and per-line breakdown.
 */
class TaxCalculator
{
    public const INCLUSIVE = 'inclusive';

    public const EXCLUSIVE = 'exclusive';

    public const EXEMPT = 'exempt';

    public function calculate(iterable $lines, ?TaxSetting $settings): array
    {
        $enabled = $settings && $settings->tax_enabled && (float) $settings->tax_rate > 0;
        $rate = $enabled ? (float) $settings->tax_rate : 0.0;
        $defaultTreatment = ($settings && $settings->tax_inclusive) ? self::INCLUSIVE : self::EXCLUSIVE;

        $subtotal = 0.0;
        $tax = 0.0;
        $total = 0.0;
        $result = [];

        foreach ($lines as $line) {
            $product = $line['product'] ?? null;
            $qty = (float) ($line['quantity'] ?? 0);
            $price = (float) ($line['price'] ?? 0);
            $gross = round($price * $qty, 2);

            $treatment = $this->resolveTreatment($product, $line['product_id'] ?? null, $settings, $defaultTreatment);

            if (! $enabled || $treatment === self::EXEMPT) {
                $lineTax = 0.0;
                $net = $gross;
                $lineGross = $gross;
            } elseif ($treatment === self::INCLUSIVE) {
                $lineTax = round($gross * $rate / (100 + $rate), 2);
                $net = round($gross - $lineTax, 2);
                $lineGross = $gross;
            } else {
                $net = $gross;
                $lineTax = round($net * $rate / 100, 2);
                $lineGross = round($net + $lineTax, 2);
            }

            $subtotal += $net;
            $tax += $lineTax;
            $total += $lineGross;

            $result[] = [
                'product_id' => $product->id ?? ($line['product_id'] ?? null),
                'quantity' => $qty,
                'price' => $price,
                'net' => $net,
                'tax' => $lineTax,
                'gross' => $lineGross,
                'treatment' => $enabled ? $treatment : null,
                'rate' => $enabled && $treatment !== self::EXEMPT ? $rate : 0.0,
                'unit' => $product->measurement_unit ?? null,
            ];
        }

        return [
            'subtotal' => round($subtotal, 2),
            'tax' => round($tax, 2),
            'total' => round($total, 2),
            'tax_enabled' => $enabled,
            'tax_rate' => $rate,
            'tax_inclusive' => $defaultTreatment === self::INCLUSIVE,
            'tax_name' => $settings->tax_name ?? 'Tax',
            'tax_type' => $settings->tax_type ?? null,
            'lines' => $result,
        ];
    }

    protected function resolveTreatment(?Product $product, $productId, ?TaxSetting $settings, string $default): string
    {
        $id = $product->id ?? $productId;
        if ($settings && $id && $settings->isProductExempt($id)) {
            return self::EXEMPT;
        }

        $treatment = $product->tax_treatment ?? null;

        return in_array($treatment, [self::INCLUSIVE, self::EXCLUSIVE, self::EXEMPT], true) ? $treatment : $default;
    }
}
