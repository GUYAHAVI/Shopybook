<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockReceipt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\SimpleOCRService;
use App\Imports\OCRProductImport;
use App\Services\ClaudeAPIService;

class ProductsController extends Controller
{
    protected $claudeService;

    public function __construct(ClaudeAPIService $claudeService)
    {
        $this->claudeService = $claudeService;
    }
    public function index()
    {
        $products = auth()->user()->business->products()->latest()->paginate(12);
        return view('business.products.index', compact('products'));
    }

    public function create()
    {
        return view('business.products.create');
    }

    public function quickCreate()
    {
        return view('business.products.quick-create');
    }

    public function store(Request $request)
    {
        $businessId = auth()->user()->business->id;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'sku' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->where('business_id', $businessId)
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products', 'barcode')->where('business_id', $businessId)
            ],
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

    public function quickStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product = new Product();
        $product->business_id = auth()->user()->business->id;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->cost_price = $request->cost_price;
        $product->stock_quantity = $request->stock_quantity;
        $product->category = $request->category;
        $product->description = $request->description;
        $product->is_active = true; // Default to active
        $product->is_featured = false; // Default to not featured
        
        // Handle single image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $product->images = [$path]; // Store as array for consistency
        }

        $product->save();

        // Check if user wants to add another product
        if ($request->has('add_another')) {
            return redirect()->route('products.quick-create')
                ->with('success', 'Product added successfully! Add another one below.');
        }

        return redirect()->route('products.index')
            ->with('success', 'Product "' . $product->name . '" created successfully!');
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
        
        $businessId = auth()->user()->business->id;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'sku' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->where('business_id', $businessId)->ignore($product->id)
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products', 'barcode')->where('business_id', $businessId)->ignore($product->id)
            ],
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
        $business = auth()->user()->business;
        
        $products = $business->products()
            ->with(['category', 'brand'])
            ->orderBy('stock_quantity', 'asc')
            ->paginate(20);
        
        // Calculate total stock value (at cost price for accurate valuation)
        $allProducts = $business->products()->get();
        $totalValue = $allProducts->sum(function($product) {
            return $product->stock_quantity * ($product->cost_price ?? $product->price);
        });
        
        // Count low stock items
        $lowStockCount = $allProducts->filter(function($product) {
            return $product->stock_quantity > 0 && $product->stock_quantity <= $product->low_stock_threshold;
        })->count();
        
        // Count out of stock items
        $outOfStockCount = $allProducts->where('stock_quantity', 0)->count();
        
        // Total products
        $totalProducts = $allProducts->count();
            
        return view('business.products.inventory', compact('products', 'totalValue', 'lowStockCount', 'outOfStockCount', 'totalProducts'));
    }

    public function updateStock(Request $request, Product $product)
    {
        $this->authorize('update', $product);
        
        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'adjustment_reason' => 'nullable|string|max:255',
        ]);

        $product->stock_quantity = $request->stock_quantity;
        $product->save();
        
        // Check for low stock and send notification if needed
        try {
            $notificationService = new \App\Services\NotificationService();
            $notificationService->checkAndNotifyLowStock($product);
        } catch (\Exception $e) {
            \Log::error('Failed to check low stock after manual update: ' . $e->getMessage());
        }

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

    /**
     * Show the form for receiving products
     */
    public function showReceiveForm()
    {
        $products = auth()->user()->business->products()
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'sku', 'stock_quantity', 'cost_price']);
            
        return view('business.products.receive', compact('products'));
    }

    /**
     * Process product receiving
     */
    public function processReceive(Request $request)
    {
        $request->validate([
            'receipt_type' => 'required|in:existing_product,new_product',
            'receipt_date' => 'required|date',
            'supplier' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            
            // For existing products
            'product_id' => 'required_if:receipt_type,existing_product|nullable|exists:products,id',
            'quantity_received' => 'required_if:receipt_type,existing_product|nullable|integer|min:1',
            'unit_cost' => 'nullable|numeric|min:0',
            
            // For new products
            'new_product_name' => 'required_if:receipt_type,new_product|nullable|string|max:255',
            'new_quantity' => 'required_if:receipt_type,new_product|nullable|integer|min:1',
            'new_unit_cost' => 'required_if:receipt_type,new_product|nullable|numeric|min:0',
            'selling_price' => 'required_if:receipt_type,new_product|nullable|numeric|min:0',
            'sku' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'low_stock_threshold' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $business = auth()->user()->business;
            
            if ($request->receipt_type === 'existing_product') {
                // Handle receiving for existing product
                $product = Product::findOrFail($request->product_id);
                
                // Verify product belongs to user's business
                if ($product->business_id !== $business->id) {
                    return back()->withErrors(['product_id' => 'Product not found.'])->withInput();
                }
                
                $quantity = $request->quantity_received;
                $unitCost = $request->unit_cost ?? $product->cost_price ?? 0;
                $totalCost = $quantity * $unitCost;
                
                // Create stock receipt record
                $receipt = StockReceipt::create([
                    'business_id' => $business->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'supplier' => $request->supplier,
                    'quantity_received' => $quantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => $totalCost,
                    'receipt_date' => $request->receipt_date,
                    'invoice_number' => $request->invoice_number,
                    'notes' => $request->notes,
                    'received_by' => auth()->id(),
                    'receipt_type' => 'existing_product',
                ]);
                
                // Update product stock
                $product->stock_quantity += $quantity;
                
                // Update cost price if provided
                if ($request->unit_cost) {
                    $product->cost_price = $unitCost;
                }
                
                $product->save();
                
                DB::commit();
                
                return redirect()->route('products.receive.history')
                    ->with('success', "Successfully received {$quantity} units of '{$product->name}'. Receipt #{$receipt->receipt_number}");
                
            } else {
                // Handle receiving for new product
                $quantity = $request->new_quantity;
                $unitCost = $request->new_unit_cost;
                $totalCost = $quantity * $unitCost;
                
                // Create the new product
                $product = new Product();
                $product->business_id = $business->id;
                $product->name = $request->new_product_name;
                $product->description = $request->description;
                $product->price = $request->selling_price;
                $product->cost_price = $unitCost;
                $product->sku = $request->sku;
                $product->barcode = $request->barcode;
                $product->category = $request->category;
                $product->brand = $request->brand;
                $product->stock_quantity = $quantity;
                $product->low_stock_threshold = $request->low_stock_threshold ?? 10;
                $product->is_active = true;
                $product->save();
                
                // Create stock receipt record
                $receipt = StockReceipt::create([
                    'business_id' => $business->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'supplier' => $request->supplier,
                    'quantity_received' => $quantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => $totalCost,
                    'receipt_date' => $request->receipt_date,
                    'invoice_number' => $request->invoice_number,
                    'notes' => $request->notes,
                    'received_by' => auth()->id(),
                    'receipt_type' => 'new_product',
                    'additional_data' => [
                        'sku' => $request->sku,
                        'barcode' => $request->barcode,
                        'category' => $request->category,
                        'brand' => $request->brand,
                    ],
                ]);
                
                DB::commit();
                
                return redirect()->route('products.receive.history')
                    ->with('success', "Successfully received new product '{$product->name}' with {$quantity} units. Receipt #{$receipt->receipt_number}");
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to process receipt: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show stock receipt history
     */
    public function receiptHistory(Request $request)
    {
        $query = StockReceipt::where('business_id', auth()->user()->business->id)
            ->with(['product', 'receivedBy'])
            ->orderBy('receipt_date', 'desc')
            ->orderBy('created_at', 'desc');
        
        // Filter by date range if provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }
        
        // Filter by receipt type if provided
        if ($request->has('receipt_type') && $request->receipt_type !== 'all') {
            $query->where('receipt_type', $request->receipt_type);
        }
        
        // Search by receipt number, product name, or supplier
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%")
                  ->orWhere('supplier', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%");
            });
        }
        
        $receipts = $query->paginate(20);
        
        // Calculate summary statistics
        $totalReceived = StockReceipt::where('business_id', auth()->user()->business->id)
            ->sum('quantity_received');
        
        $totalValue = StockReceipt::where('business_id', auth()->user()->business->id)
            ->sum('total_cost');
        
        $recentReceipts = StockReceipt::where('business_id', auth()->user()->business->id)
            ->where('receipt_date', '>=', now()->subDays(30))
            ->count();
        
        return view('business.products.receipt-history', compact('receipts', 'totalReceived', 'totalValue', 'recentReceipts'));
    }

    /**
     * Show individual receipt details
     */
    public function showReceipt(StockReceipt $receipt)
    {
        // Verify receipt belongs to user's business
        if ($receipt->business_id !== auth()->user()->business->id) {
            abort(403, 'Unauthorized access to receipt.');
        }
        
        $receipt->load(['product', 'receivedBy']);
        
        return view('business.products.receipt-show', compact('receipt'));
    }

    /**
     * Enhance product description using AI
     */
    public function enhanceDescription(Request $request)
    {
        $request->validate([
            'description' => 'required|string|min:10',
            'product_name' => 'required|string',
            'category' => 'nullable|string',
        ]);

        try {
            $enhancedDescription = $this->claudeService->enhanceProductDescription(
                $request->description,
                $request->product_name,
                $request->category
            );

            return response()->json([
                'success' => true,
                'enhanced_description' => $enhancedDescription,
                'original_description' => $request->description,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to enhance description: ' . $e->getMessage(),
            ], 500);
        }
    }
}
