<?php

namespace App\Imports;

use App\Models\Product;
use App\Services\Accounting\Ledger;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsUpdateImport implements ToCollection, WithHeadingRow
{
    public int $updated = 0;
    public int $skipped = 0;
    public array $errors = [];

    protected $businessId;
    protected $ledger;

    public function __construct($businessId)
    {
        $this->businessId = $businessId;
        $this->ledger = app(Ledger::class);
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +1 for heading row, +1 for 1-based sheet rows
            $this->processRow($row->toArray(), $rowNumber);
        }
    }

    protected function processRow(array $row, int $rowNumber): void
    {
        // Ignore completely blank rows
        if (count(array_filter($row, fn ($v) => $v !== null && $v !== '')) === 0) {
            return;
        }

        $id = $row['id'] ?? null;
        if ($id === null || $id === '' || !is_numeric($id)) {
            $this->skipped++;
            $this->errors[] = "Row {$rowNumber}: skipped — missing or invalid 'id' (only exported product files can be uploaded here).";
            return;
        }

        $product = Product::where('business_id', $this->businessId)->find((int) $id);
        if (!$product) {
            $this->skipped++;
            $this->errors[] = "Row {$rowNumber}: skipped — no product with id {$id} in this business.";
            return;
        }

        $name = trim((string) ($row['name'] ?? ''));
        if ($name === '') {
            $this->errors[] = "Row {$rowNumber} ({$product->name}): name is required.";
            return;
        }

        $price = $row['price'] ?? null;
        if ($price === null || $price === '' || !is_numeric($price) || $price < 0) {
            $this->errors[] = "Row {$rowNumber} ({$name}): price must be a number ≥ 0.";
            return;
        }

        $sku = $this->nullableString($row['sku'] ?? null);
        if ($sku && Product::where('business_id', $this->businessId)->where('sku', $sku)->where('id', '!=', $product->id)->exists()) {
            $this->errors[] = "Row {$rowNumber} ({$name}): sku '{$sku}' is already used by another product.";
            return;
        }

        $barcode = $this->nullableString($row['barcode'] ?? null);
        if ($barcode && Product::where('business_id', $this->businessId)->where('barcode', $barcode)->where('id', '!=', $product->id)->exists()) {
            $this->errors[] = "Row {$rowNumber} ({$name}): barcode '{$barcode}' is already used by another product.";
            return;
        }

        // stock_quantity: blank keeps the current value; a different value books a
        // stock adjustment through the ledger before the product row is changed.
        $stockQty = $row['stock_quantity'] ?? null;
        $newStock = null;
        if ($stockQty !== null && $stockQty !== '') {
            if (!is_numeric($stockQty) || $stockQty < 0) {
                $this->errors[] = "Row {$rowNumber} ({$name}): stock_quantity must be a number ≥ 0.";
                return;
            }
            $newStock = (float) $stockQty;
        }

        try {
            DB::transaction(function () use ($product, $row, $name, $price, $sku, $barcode, $newStock) {
                if ($newStock !== null) {
                    $this->ledger->postStockAdjustment($product, $newStock, $product);
                    $product->stock_quantity = $newStock;
                }

                $product->fill([
                    'name' => $name,
                    'description' => $this->nullableString($row['description'] ?? null),
                    'price' => (float) $price,
                    'cost_price' => $this->nullableNumber($row['cost_price'] ?? null),
                    'sku' => $sku,
                    'barcode' => $barcode,
                    'category' => $this->nullableString($row['category'] ?? null),
                    'brand' => $this->nullableString($row['brand'] ?? null),
                    'measurement_unit' => $this->normaliseUnit($row['measurement_unit'] ?? null) ?: $product->measurement_unit,
                    'tax_treatment' => $this->normaliseTaxTreatment($row['tax_treatment'] ?? null),
                    'low_stock_threshold' => $this->nullableNumber($row['low_stock_threshold'] ?? null),
                    'weight' => $this->nullableNumber($row['weight'] ?? null),
                    'dimensions' => $this->nullableString($row['dimensions'] ?? null),
                    'is_active' => $this->toBool($row['is_active'] ?? null, $product->is_active),
                    'is_featured' => $this->toBool($row['is_featured'] ?? null, $product->is_featured),
                ]);

                $product->save();
            });
        } catch (\Throwable $e) {
            $this->errors[] = "Row {$rowNumber} ({$name}): {$e->getMessage()}";
            return;
        }

        $this->updated++;
    }

    protected function nullableString($value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    protected function nullableNumber($value)
    {
        return ($value === null || $value === '' || !is_numeric($value)) ? null : $value + 0;
    }

    protected function toBool($value, bool $default): bool
    {
        if ($value === null || $value === '') {
            return $default;
        }
        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'y'], true);
    }

    protected function normaliseUnit($value): ?string
    {
        $unit = strtolower(trim((string) $value));
        if ($unit === '') {
            return null;
        }
        $aliases = ['pcs' => 'piece', 'pc' => 'piece', 'pieces' => 'piece', 'kgs' => 'kg', 'kilogram' => 'kg', 'litre' => 'l', 'liter' => 'l', 'ltr' => 'l', 'grams' => 'g', 'metre' => 'm', 'meter' => 'm'];
        $unit = $aliases[$unit] ?? $unit;
        return array_key_exists($unit, Product::UNITS) ? $unit : null;
    }

    protected function normaliseTaxTreatment($value): ?string
    {
        $treatment = strtolower(trim((string) $value));
        return array_key_exists($treatment, Product::TAX_TREATMENTS) ? $treatment : null;
    }
}
