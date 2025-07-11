<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class OCRProductImport
{
    protected $businessId;
    protected $skipDuplicates;
    protected $results = [];
    protected $errors = [];

    public function __construct($businessId, $skipDuplicates = false)
    {
        $this->businessId = $businessId;
        $this->skipDuplicates = $skipDuplicates;
    }

    /**
     * Import products from OCR data
     */
    public function import($ocrData)
    {
        foreach ($ocrData as $index => $data) {
            try {
                // Skip if there was an error processing the image
                if (isset($data['error'])) {
                    $this->errors[] = [
                        'row' => $index + 1,
                        'error' => $data['error']
                    ];
                    continue;
                }

                // Validate required fields
                $validationErrors = $this->validateData($data);
                if (!empty($validationErrors)) {
                    $this->errors[] = [
                        'row' => $index + 1,
                        'error' => implode(', ', $validationErrors)
                    ];
                    continue;
                }

                // Check for duplicates if requested
                if ($this->skipDuplicates && !empty($data['sku'])) {
                    if (Product::where('business_id', $this->businessId)->where('sku', $data['sku'])->exists()) {
                        $this->errors[] = [
                            'row' => $index + 1,
                            'error' => 'Product with SKU ' . $data['sku'] . ' already exists'
                        ];
                        continue;
                    }
                }

                // Create the product
                $product = $this->createProduct($data);
                
                $this->results[] = [
                    'row' => $index + 1,
                    'product' => $product,
                    'status' => 'success'
                ];

            } catch (\Exception $e) {
                Log::error('Failed to import OCR product at row ' . ($index + 1) . ': ' . $e->getMessage());
                $this->errors[] = [
                    'row' => $index + 1,
                    'error' => 'Import failed: ' . $e->getMessage()
                ];
            }
        }

        return [
            'success_count' => count($this->results),
            'error_count' => count($this->errors),
            'results' => $this->results,
            'errors' => $this->errors
        ];
    }

    /**
     * Validate OCR data
     */
    protected function validateData($data)
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'Product name is required';
        }

        if (empty($data['price']) || !is_numeric($data['price']) || $data['price'] <= 0) {
            $errors[] = 'Valid price is required';
        }

        if (empty($data['stock_quantity']) || !is_numeric($data['stock_quantity']) || $data['stock_quantity'] < 0) {
            $errors[] = 'Valid stock quantity is required';
        }

        return $errors;
    }

    /**
     * Create product from OCR data
     */
    protected function createProduct($data)
    {
        $product = new Product([
            'business_id' => $this->businessId,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'cost_price' => $data['cost_price'] ?? null,
            'sku' => $data['sku'] ?? null,
            'barcode' => $data['barcode'] ?? null,
            'category' => $data['category'] ?? null,
            'brand' => $data['brand'] ?? null,
            'stock_quantity' => $data['stock_quantity'],
            'low_stock_threshold' => $data['low_stock_threshold'] ?? null,
            'weight' => $data['weight'] ?? null,
            'dimensions' => $data['dimensions'] ?? null,
            'is_active' => true,
            'is_featured' => false,
        ]);

        $product->slug = Str::slug($data['name']);
        $product->save();

        return $product;
    }

    /**
     * Get import results
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Get import errors
     */
    public function getErrors()
    {
        return $this->errors;
    }
} 