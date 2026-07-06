<?php

namespace Tests\Unit\Services;

use App\Services\ProductConversionService;
use PHPUnit\Framework\TestCase;

class ProductConversionServiceTest extends TestCase
{
    private ProductConversionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProductConversionService;
    }

    public function test_weight_to_area_divides_by_conversion_factor(): void
    {
        // 100 kg / 0.2 microns = 500 sqm
        $this->assertSame(500.0, $this->service->calculateConvertedQuantity(100, 'weight_to_area', 0.2));
    }

    public function test_area_to_weight_multiplies_by_conversion_factor(): void
    {
        $this->assertSame(100.0, $this->service->calculateConvertedQuantity(200, 'area_to_weight', 0.5));
    }

    public function test_custom_conversion_multiplies_by_factor(): void
    {
        $this->assertSame(30.0, $this->service->calculateConvertedQuantity(10, 'custom', 3));
    }

    public function test_unknown_conversion_type_returns_original_quantity(): void
    {
        $this->assertSame(42.0, $this->service->calculateConvertedQuantity(42, 'unknown', 5));
    }

    public function test_profit_margin_is_calculated_as_percentage(): void
    {
        // purchase total = 100 * 10 = 1000, sale total = 500 * 3 = 1500
        // margin = (1500 - 1000) / 1000 * 100 = 50%
        $this->assertSame(50.0, $this->service->calculateProfitMargin(100, 10, 500, 3));
    }

    public function test_profit_margin_can_be_negative(): void
    {
        // purchase total = 1000, sale total = 800 -> -20%
        $this->assertSame(-20.0, $this->service->calculateProfitMargin(100, 10, 800, 1));
    }

    public function test_profit_margin_returns_zero_when_purchase_total_is_zero(): void
    {
        $this->assertSame(0.0, $this->service->calculateProfitMargin(0, 10, 500, 3));
    }

    public function test_common_microns_include_custom_option(): void
    {
        $microns = $this->service->getCommonMicrons();

        $this->assertArrayHasKey('0.2', $microns);
        $this->assertArrayHasKey('custom', $microns);
        $this->assertSame('Custom microns', $microns['custom']);
    }

    public function test_conversion_examples_have_expected_structure(): void
    {
        $examples = $this->service->getConversionExamples();

        $this->assertNotEmpty($examples);
        foreach ($examples as $example) {
            $this->assertArrayHasKey('name', $example);
            $this->assertArrayHasKey('example', $example);
            $this->assertArrayHasKey('description', $example);
        }
    }

    public function test_valid_conversion_returns_no_errors(): void
    {
        $errors = $this->service->validateConversion('weight_to_area', 0.2, 'kg', 'sqm');
        $this->assertSame([], $errors);
    }

    public function test_non_positive_conversion_factor_produces_error(): void
    {
        $errors = $this->service->validateConversion('custom', 0, 'kg', 'sqm');
        $this->assertContains('Conversion factor must be greater than 0', $errors);
    }

    public function test_weight_to_area_requires_kg_and_sqm_units(): void
    {
        $errors = $this->service->validateConversion('weight_to_area', 0.2, 'g', 'liters');

        $this->assertContains('Purchase unit should be kg for weight to area conversion', $errors);
        $this->assertContains('Sale unit should be sqm for weight to area conversion', $errors);
    }

    public function test_area_to_weight_requires_sqm_and_kg_units(): void
    {
        $errors = $this->service->validateConversion('area_to_weight', 0.5, 'kg', 'g');

        $this->assertContains('Purchase unit should be sqm for area to weight conversion', $errors);
        $this->assertContains('Sale unit should be kg for area to weight conversion', $errors);
    }
}
