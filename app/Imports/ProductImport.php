<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;

class ProductImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable;

    protected $businessId;
    protected $skipDuplicates;

    public function __construct($businessId, $skipDuplicates = false)
    {
        $this->businessId = $businessId;
        $this->skipDuplicates = $skipDuplicates;
    }

    public function model(array $row)
    {
        // Skip duplicate SKUs if requested
        if ($this->skipDuplicates && !empty($row['sku'])) {
            if (Product::where('business_id', $this->businessId)->where('sku', $row['sku'])->exists()) {
                return null;
            }
        }

        return new Product([
            'business_id' => $this->businessId,
            'name' => $row['name'] ?? '',
            'description' => $row['description'] ?? null,
            'price' => $row['price'] ?? 0,
            'cost_price' => $row['cost_price'] ?? null,
            'sku' => $row['sku'] ?? null,
            'barcode' => $row['barcode'] ?? null,
            'category' => $row['category'] ?? null,
            'brand' => $row['brand'] ?? null,
            'stock_quantity' => $row['stock_quantity'] ?? 0,
            'low_stock_threshold' => $row['low_stock_threshold'] ?? null,
            'weight' => $row['weight'] ?? null,
            'dimensions' => $row['dimensions'] ?? null,
            'is_active' => isset($row['is_active']) ? (bool)$row['is_active'] : true,
            'is_featured' => isset($row['is_featured']) ? (bool)$row['is_featured'] : false,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => [
                'nullable',
                'string',
                'max:100',
            ],
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        // Handle the failures as needed (e.g., log, collect, etc.)
    }
} 