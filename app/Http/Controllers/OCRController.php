<?php

namespace App\Http\Controllers;

use App\Services\ClaudeAPIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OCRController extends Controller
{
    protected $claudeService;

    public function __construct(ClaudeAPIService $claudeService)
    {
        $this->claudeService = $claudeService;
    }

    /**
     * Show OCR upload page
     */
    public function index()
    {
        return view('ocr.index');
    }

    /**
     * Process uploaded image and extract records
     */
    public function extract(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240', // Max 10MB
            'record_type' => 'required|in:inventory,sales,services'
        ]);

        try {
            $business = Auth::user()->business;
            
            if (!$business) {
                return response()->json([
                    'success' => false,
                    'message' => 'No business associated with your account'
                ], 400);
            }

            // Store the uploaded image temporarily
            $image = $request->file('image');
            $path = $image->store('ocr-uploads', 'local');
            $fullPath = storage_path('app/' . $path);

            Log::info('OCR image uploaded', [
                'business_id' => $business->id,
                'record_type' => $request->record_type,
                'file_size' => $image->getSize()
            ]);

            // Extract data from image
            $extractedData = $this->claudeService->extractRecordsFromImage(
                $fullPath,
                $request->record_type
            );

            // Clean up temporary file
            Storage::disk('local')->delete($path);

            if (!$extractedData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $extractedData['error'] ?? 'Failed to extract data from image'
                ], 400);
            }

            // Return extracted data for review
            return response()->json([
                'success' => true,
                'data' => $extractedData,
                'message' => 'Data extracted successfully. Please review before saving.'
            ]);

        } catch (\Exception $e) {
            Log::error('OCR extraction failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save extracted records to database
     */
    public function save(Request $request)
    {
        $request->validate([
            'record_type' => 'required|in:inventory,sales,services',
            'records' => 'required|array'
        ]);

        try {
            $business = Auth::user()->business;
            
            if (!$business) {
                return response()->json([
                    'success' => false,
                    'message' => 'No business associated with your account'
                ], 400);
            }

            $recordType = $request->record_type;
            $ocrData = [
                'success' => true,
                'records' => $request->records,
                'total_amount' => $request->total_amount ?? 0
            ];

            $result = match($recordType) {
                'inventory' => $this->claudeService->createProductsFromOCR($ocrData, $business->id),
                'sales' => $this->claudeService->createSalesFromOCR($ocrData, $business->id),
                'services' => $this->createServicesFromOCR($ocrData, $business->id),
                default => ['success' => false, 'message' => 'Invalid record type']
            };

            Log::info('OCR records saved', [
                'business_id' => $business->id,
                'record_type' => $recordType,
                'result' => $result
            ]);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Failed to save OCR records', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save records: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create service bookings from OCR data
     */
    private function createServicesFromOCR($ocrData, $businessId)
    {
        if (!$ocrData['success']) {
            return [
                'success' => false,
                'message' => $ocrData['error'] ?? 'Failed to extract data'
            ];
        }

        $created = 0;
        $failed = 0;
        $errors = [];

        foreach ($ocrData['records'] as $record) {
            try {
                // Find or create customer
                $customer = \App\Models\Customer::firstOrCreate([
                    'business_id' => $businessId,
                    'name' => $record['customer_name']
                ], [
                    'phone' => $record['phone'] ?? null,
                    'email' => null
                ]);

                // Find or create service
                $service = \App\Models\Service::firstOrCreate([
                    'business_id' => $businessId,
                    'name' => $record['service_name']
                ], [
                    'duration' => $record['duration'] ?? 60,
                    'price' => $record['price'] ?? 0,
                    'active' => true
                ]);

                // Create service booking
                $bookingDate = !empty($record['date']) ? $record['date'] : now()->format('Y-m-d');
                $bookingTime = !empty($record['time']) ? $record['time'] : '09:00';

                \App\Models\ServiceBooking::create([
                    'business_id' => $businessId,
                    'customer_id' => $customer->id,
                    'service_id' => $service->id,
                    'booking_date' => $bookingDate,
                    'booking_time' => $bookingTime,
                    'duration' => $record['duration'] ?? 60,
                    'total_amount' => $record['price'] ?? 0,
                    'status' => 'confirmed',
                    'payment_status' => 'pending',
                    'notes' => $record['notes'] ?? 'Added via OCR'
                ]);

                $created++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Failed to create booking for {$record['customer_name']}: " . $e->getMessage();
                Log::warning('Failed to create service booking from OCR', [
                    'customer' => $record['customer_name'],
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'success' => true,
            'created' => $created,
            'failed' => $failed,
            'errors' => $errors,
            'message' => "Successfully created {$created} service bookings" . ($failed > 0 ? ", {$failed} failed" : '')
        ];
    }
}
