<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\SimpleOCRService;
use App\Imports\OCRProductImport;

class ProductsController extends Controller
{
    public function index()
    {
        $products = auth()->user()->business->products()->latest()->paginate(12);
        return view('business.products.index', compact('products'));
    }

    public function create()
    {
        return view('business.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:100|unique:products,barcode',
            'category' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $product = new Product($request->all());
        $product->business_id = auth()->user()->business->id;
        $product->slug = Str::slug($request->name);
        
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = $path;
            }
            $product->images = $images;
        }

        $product->save();

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    public function show(Product $product)
    {
        $this->authorize('view', $product);
        return view('business.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        return view('business.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . $product->id,
            'category' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $product->fill($request->all());
        $product->slug = Str::slug($request->name);
        
        if ($request->hasFile('images')) {
            // Delete old images
            if ($product->images) {
                foreach ($product->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
            
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = $path;
            }
            $product->images = $images;
        }

        $product->save();

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        
        // Delete images
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        
        $product->delete();
        
        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }

    public function bulkImport()
    {
        return view('business.products.bulk-import');
    }

    public function processBulkImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xlsx|max:2048',
        ]);

        $hasHeader = $request->has('has_header');
        $skipDuplicates = $request->has('skip_duplicates');
        $file = $request->file('csv_file');

        try {
            Excel::import(new \App\Imports\ProductImport(auth()->user()->business->id, $skipDuplicates), $file);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            return back()->withErrors(['csv_file' => 'Some rows failed to import.'])->with('failures', $failures);
        }

        return redirect()->route('products.index')->with('success', 'Products imported successfully!');
    }

    /**
     * Process OCR images and extract product data
     */
    public function processOCRImages(Request $request)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
        ]);

        $skipDuplicates = $request->has('skip_duplicates');
        $imageFiles = $request->file('images');

        try {
            $ocrService = new SimpleOCRService();
            $ocrData = $ocrService->processMultipleImages($imageFiles);

            // Import the extracted data
            $import = new OCRProductImport(auth()->user()->business->id, $skipDuplicates);
            $result = $import->import($ocrData);

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$result['success_count']} products. {$result['error_count']} errors occurred.",
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OCR processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview OCR results before importing
     */
    public function previewOCRResults(Request $request)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $imageFiles = $request->file('images');

        try {
            $ocrService = new SimpleOCRService();
            $ocrData = $ocrService->processMultipleImages($imageFiles);

            return response()->json([
                'success' => true,
                'data' => $ocrData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OCR processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function inventory()
    {
        $products = auth()->user()->business->products()
            ->with(['category', 'brand'])
            ->orderBy('stock_quantity', 'asc')
            ->paginate(20);
            
        return view('business.products.inventory', compact('products'));
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'adjustment_reason' => 'nullable|string|max:255',
        ]);

        $product->stock_quantity = $request->stock_quantity;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully',
            'new_quantity' => $product->stock_quantity
        ]);
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="products_template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'name', 'description', 'price', 'cost_price', 'sku', 'barcode', 
                'category', 'brand', 'stock_quantity', 'low_stock_threshold', 
                'weight', 'dimensions', 'is_active', 'is_featured'
            ]);
            
            // Add sample data
            fputcsv($file, [
                'iPhone 13 Pro', 'Latest iPhone model with advanced features', '129999.00', '100000.00',
                'IPH13PRO-128', '1234567890123', 'Electronics', 'Apple', '50', '10',
                '0.2', '10x5x2 cm', '1', '0'
            ]);
            
            fputcsv($file, [
                'Nike Air Max', 'Comfortable running shoes', '15000.00', '10000.00',
                'NIKE-AIRMAX-42', '9876543210987', 'Sports', 'Nike', '25', '5',
                '0.5', '30x20x10 cm', '1', '1'
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadExcelTemplate()
    {
        $headers = [
            'name', 'description', 'price', 'cost_price', 'sku', 'barcode', 
            'category', 'brand', 'stock_quantity', 'low_stock_threshold', 
            'weight', 'dimensions', 'is_active', 'is_featured'
        ];

        $sampleData = [
            [
                'iPhone 13 Pro', 'Latest iPhone model with advanced features', '129999.00', '100000.00',
                'IPH13PRO-128', '1234567890123', 'Electronics', 'Apple', '50', '10',
                '0.2', '10x5x2 cm', '1', '0'
            ],
            [
                'Nike Air Max', 'Comfortable running shoes', '15000.00', '10000.00',
                'NIKE-AIRMAX-42', '9876543210987', 'Sports', 'Nike', '25', '5',
                '0.5', '30x20x10 cm', '1', '1'
            ]
        ];

        return Excel::download(new \App\Exports\ProductTemplateExport($headers, $sampleData), 'products_template.xlsx');
    }

    public function exportInventory()
    {
        $products = auth()->user()->business->products()
            ->orderBy('stock_quantity', 'asc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Product Name', 'SKU', 'Category', 'Brand', 'Current Stock', 
                'Low Stock Threshold', 'Price', 'Stock Value', 'Status'
            ]);
            
            // Add data
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->name,
                    $product->sku ?? 'N/A',
                    $product->category ?? 'Uncategorized',
                    $product->brand ?? 'No Brand',
                    $product->stock_quantity,
                    $product->low_stock_threshold ?? 'Not Set',
                    $product->price,
                    $product->stock_quantity * $product->price,
                    $product->stock_status_text
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
