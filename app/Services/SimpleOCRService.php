<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SimpleOCRService
{
    /**
     * Process an image and extract text using OCR
     * This is a fallback service for testing without Google Cloud Vision
     */
    public function extractTextFromImage($imagePath)
    {
        try {
            // For testing purposes, return a sample text
            // In production, this would use a real OCR service
            $sampleTexts = [
                "iPhone 13 Pro\nPrice: $1299.99\nStock: 50 units\nSKU: IPH13PRO-128\nCategory: Electronics\nBrand: Apple",
                "Nike Air Max Shoes\nPrice: $150.00\nStock: 25 pairs\nSKU: NIKE-AIRMAX-42\nCategory: Sports\nBrand: Nike",
                "Samsung Galaxy S21\nPrice: $899.99\nStock: 30 units\nSKU: SAMSUNG-S21-128\nCategory: Electronics\nBrand: Samsung",
                "Adidas Running Shoes\nPrice: $120.00\nStock: 40 pairs\nSKU: ADIDAS-RUN-41\nCategory: Sports\nBrand: Adidas"
            ];
            
            // Use file hash to get consistent results for the same image
            $fileHash = md5_file($imagePath);
            $index = ord($fileHash[0]) % count($sampleTexts);
            
            return $sampleTexts[$index];
            
        } catch (\Exception $e) {
            Log::error('Simple OCR processing failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Parse handwritten product data from OCR text
     */
    public function parseProductData($ocrText)
    {
        $data = [
            'name' => null,
            'price' => null,
            'stock_quantity' => null,
            'description' => null,
            'sku' => null,
            'category' => null,
            'brand' => null,
            'cost_price' => null,
        ];

        if (!$ocrText) {
            return $data;
        }

        // Split text into lines
        $lines = explode("\n", trim($ocrText));
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Extract price (look for currency patterns)
            if (preg_match('/(\d+\.?\d*)\s*(?:USD|$|€|£|₦|KSH|KES)/i', $line, $matches)) {
                $data['price'] = floatval($matches[1]);
            }
            
            // Extract stock quantity (look for "stock", "qty", "quantity" patterns)
            if (preg_match('/(?:stock|qty|quantity|in stock|available)[:\s]*(\d+)/i', $line, $matches)) {
                $data['stock_quantity'] = intval($matches[1]);
            }
            
            // Extract SKU (look for SKU patterns)
            if (preg_match('/(?:sku|product code|item code)[:\s]*([A-Z0-9\-_]+)/i', $line, $matches)) {
                $data['sku'] = $matches[1];
            }
            
            // Extract category (look for category patterns)
            if (preg_match('/(?:category|type|group)[:\s]*([A-Za-z\s]+)/i', $line, $matches)) {
                $data['category'] = trim($matches[1]);
            }
            
            // Extract brand (look for brand patterns)
            if (preg_match('/(?:brand|make|manufacturer)[:\s]*([A-Za-z\s]+)/i', $line, $matches)) {
                $data['brand'] = trim($matches[1]);
            }
            
            // Extract cost price (look for cost patterns)
            if (preg_match('/(?:cost|wholesale|buying price)[:\s]*(\d+\.?\d*)/i', $line, $matches)) {
                $data['cost_price'] = floatval($matches[1]);
            }
        }

        // Try to extract product name (usually the first substantial line)
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Skip lines that are clearly not product names
            if (preg_match('/(?:price|stock|sku|category|brand|cost|total|subtotal|tax)/i', $line)) {
                continue;
            }
            
            // If line contains mostly letters and spaces, it might be a product name
            if (preg_match('/^[A-Za-z\s\-\.]+$/', $line) && strlen($line) > 2) {
                $data['name'] = $line;
                break;
            }
        }

        // If no name found, use the first non-empty line
        if (!$data['name']) {
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line) && !preg_match('/(?:price|stock|sku|category|brand|cost|total|subtotal|tax)/i', $line)) {
                    $data['name'] = $line;
                    break;
                }
            }
        }

        return $data;
    }

    /**
     * Process multiple images and extract product data
     */
    public function processMultipleImages($imageFiles)
    {
        $results = [];
        
        foreach ($imageFiles as $index => $imageFile) {
            try {
                // Store the uploaded file temporarily
                $tempPath = $imageFile->store('temp/ocr', 'local');
                $fullPath = Storage::disk('local')->path($tempPath);
                
                // Extract text from image
                $ocrText = $this->extractTextFromImage($fullPath);
                
                // Parse the extracted text
                $productData = $this->parseProductData($ocrText);
                
                // Add original image path for reference
                $productData['original_image'] = $tempPath;
                $productData['ocr_text'] = $ocrText;
                
                $results[] = $productData;
                
                // Clean up temporary file
                Storage::disk('local')->delete($tempPath);
                
            } catch (\Exception $e) {
                Log::error('Failed to process image ' . $index . ': ' . $e->getMessage());
                $results[] = [
                    'error' => 'Failed to process image: ' . $e->getMessage(),
                    'original_image' => $imageFile->getClientOriginalName()
                ];
            }
        }
        
        return $results;
    }

    /**
     * Validate extracted product data
     */
    public function validateProductData($data)
    {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Product name could not be extracted from the image';
        }
        
        if (empty($data['price']) || $data['price'] <= 0) {
            $errors[] = 'Valid price could not be extracted from the image';
        }
        
        if (empty($data['stock_quantity']) || $data['stock_quantity'] < 0) {
            $errors[] = 'Valid stock quantity could not be extracted from the image';
        }
        
        return $errors;
    }
} 