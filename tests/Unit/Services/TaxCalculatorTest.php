<?php

namespace Tests\Unit\Services;

use App\Models\Product;
use App\Models\TaxSetting;
use App\Services\TaxCalculator;
use PHPUnit\Framework\TestCase;

class TaxCalculatorTest extends TestCase
{
    private TaxCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new TaxCalculator;
    }

    private function settings(array $overrides = []): TaxSetting
    {
        $s = new TaxSetting;
        $s->forceFill(array_merge([
            'tax_enabled' => true,
            'tax_rate' => 16,
            'tax_inclusive' => false,
            'tax_name' => 'VAT',
            'tax_type' => 'VAT',
            'tax_exemption_items' => null,
        ], $overrides));

        return $s;
    }

    private function product(int $id, ?string $treatment = null, ?string $unit = null): Product
    {
        $p = new Product;
        $p->forceFill(['tax_treatment' => $treatment, 'measurement_unit' => $unit]);
        $p->id = $id;

        return $p;
    }

    private function line(Product $p, float $qty, float $price): array
    {
        return ['product' => $p, 'product_id' => $p->id, 'quantity' => $qty, 'price' => $price];
    }

    public function test_tax_disabled_never_adds_tax(): void
    {
        $result = $this->calculator->calculate(
            [$this->line($this->product(1), 2, 100)],
            $this->settings(['tax_enabled' => false])
        );

        $this->assertSame(200.0, $result['subtotal']);
        $this->assertSame(0.0, $result['tax']);
        $this->assertSame(200.0, $result['total']);
        $this->assertFalse($result['tax_enabled']);
    }

    public function test_null_settings_means_no_tax(): void
    {
        $result = $this->calculator->calculate([$this->line($this->product(1), 1, 100)], null);
        $this->assertSame(0.0, $result['tax']);
        $this->assertSame(100.0, $result['total']);
    }

    public function test_exclusive_adds_tax_on_top(): void
    {
        $result = $this->calculator->calculate([$this->line($this->product(1), 1, 100)], $this->settings());

        $this->assertSame(100.0, $result['subtotal']);
        $this->assertSame(16.0, $result['tax']);
        $this->assertSame(116.0, $result['total']);
    }

    public function test_inclusive_extracts_tax_without_changing_total(): void
    {
        $result = $this->calculator->calculate(
            [$this->line($this->product(1), 1, 116)],
            $this->settings(['tax_inclusive' => true])
        );

        $this->assertSame(100.0, $result['subtotal']);
        $this->assertSame(16.0, $result['tax']);
        $this->assertSame(116.0, $result['total']);
        $this->assertTrue($result['tax_inclusive']);
    }

    public function test_per_product_override_beats_business_default(): void
    {
        // Business default is exclusive, but this product's price already includes tax
        $result = $this->calculator->calculate(
            [$this->line($this->product(1, 'inclusive'), 1, 116)],
            $this->settings()
        );

        $this->assertSame(116.0, $result['total']);
        $this->assertSame(16.0, $result['tax']);
        $this->assertSame('inclusive', $result['lines'][0]['treatment']);
    }

    public function test_exempt_product_in_mixed_cart(): void
    {
        $result = $this->calculator->calculate([
            $this->line($this->product(1), 1, 100),            // exclusive -> 16 tax
            $this->line($this->product(2, 'exempt'), 3, 50),   // no tax
        ], $this->settings());

        $this->assertSame(250.0, $result['subtotal']);
        $this->assertSame(16.0, $result['tax']);
        $this->assertSame(266.0, $result['total']);
        $this->assertSame('exempt', $result['lines'][1]['treatment']);
    }

    public function test_business_exemption_list_is_honoured(): void
    {
        $result = $this->calculator->calculate(
            [$this->line($this->product(7), 1, 100)],
            $this->settings(['tax_exemption_items' => [7]])
        );

        $this->assertSame(0.0, $result['tax']);
    }

    public function test_unit_is_carried_through_to_lines(): void
    {
        $result = $this->calculator->calculate([$this->line($this->product(1, null, 'kg'), 2, 50)], null);
        $this->assertSame('kg', $result['lines'][0]['unit']);
    }
}
