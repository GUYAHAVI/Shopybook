<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenantRegistrationController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BusinessAnalysisController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Auth\PasswordVerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/',[IndexController::class,'index'])->name('index');
Route::get('/business', [BusinessController::class, 'index'])->name('businesses');

// Language Switch Route
Route::get('/language/{language}', function ($language) {
    $allowedLanguages = ['en', 'sw', 'sheng'];
    if (in_array($language, $allowedLanguages)) {
        session(['language' => $language]);
    }
    return redirect()->back();
})->name('language.switch');

Auth::routes();

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard')->middleware('auth');

Route::middleware(['auth', 'verified'])->group(function () {
    // Business Profile Routes
    Route::prefix('business')->group(function () {
        Route::get('/create', [BusinessController::class, 'create'])->name('business.create');
        Route::post('/store', [BusinessController::class, 'store'])->name('business.store');
        Route::get('/edit', [BusinessController::class, 'edit'])->name('business.edit')
            ->middleware('has.business');
        Route::put('/update', [BusinessController::class, 'update'])->name('business.update')
            ->middleware('has.business');

        Route::delete('/{business}', [BusinessController::class, 'destroy'])
            ->name('business.destroy')
            ->middleware(['has.business'])
            ->can('delete', 'business');
            
        Route::post('/password/verify', function (Request $request) {
            try {
                $valid = Hash::check($request->password, auth()->user()->getAuthPassword());

                return response()->json([
                    'valid' => $valid,
                    'message' => $valid ? 'Password verified' : 'Invalid password'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Verification failed'
                ], 500);
            }
        })->name('password.verify');
    });

    // Products Routes
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductsController::class, 'index'])->name('products.index')->middleware('has.business');
        Route::get('/create', [ProductsController::class, 'create'])->name('products.create')->middleware('has.business');
        Route::post('/', [ProductsController::class, 'store'])->name('products.store')->middleware('has.business');
        Route::get('/{product}', [ProductsController::class, 'show'])->name('products.show')->middleware('has.business');
        Route::get('/{product}/edit', [ProductsController::class, 'edit'])->name('products.edit')->middleware('has.business');
        Route::put('/{product}', [ProductsController::class, 'update'])->name('products.update')->middleware('has.business');
        Route::delete('/{product}', [ProductsController::class, 'destroy'])->name('products.destroy')->middleware('has.business');
        
        // Bulk Import Routes
        Route::get('/products/bulk-import', [ProductsController::class, 'bulkImport'])->name('products.bulk-import')->middleware('has.business');
        Route::post('/products/bulk-import/process', [ProductsController::class, 'processBulkImport'])->name('products.bulk-import.process')->middleware('has.business');
        Route::get('/products/bulk-import/template', [ProductsController::class, 'downloadTemplate'])->name('products.bulk-import.template')->middleware('has.business');
        
        // OCR Routes
        Route::post('/products/ocr/preview', [ProductsController::class, 'previewOCRResults'])->name('products.ocr.preview')->middleware('has.business');
        Route::post('/products/ocr/process', [ProductsController::class, 'processOCRImages'])->name('products.ocr.process')->middleware('has.business');
        
        // Inventory Routes
        Route::get('/inventory', [ProductsController::class, 'inventory'])->name('products.inventory')->middleware('has.business');
        Route::post('/{product}/stock', [ProductsController::class, 'updateStock'])->name('products.update-stock')->middleware('has.business');
        Route::get('/inventory/export', [ProductsController::class, 'exportInventory'])->name('products.inventory.export')->middleware('has.business');
    });

    // Sales Routes
    Route::prefix('sales')->group(function () {
        Route::get('/pos', [SalesController::class, 'pos'])->name('sales.pos')->middleware('has.business');
        Route::get('/orders', [SalesController::class, 'orders'])->name('sales.orders')->middleware('has.business');
        Route::get('/orders/{order}', [SalesController::class, 'orderDetails'])->name('sales.order-details')->middleware('has.business');
        Route::post('/orders', [SalesController::class, 'createOrder'])->name('sales.create-order')->middleware('has.business');
        Route::put('/orders/{order}/status', [SalesController::class, 'updateOrderStatus'])->name('sales.update-order-status')->middleware('has.business');
        
        Route::get('/invoices', [SalesController::class, 'invoices'])->name('sales.invoices')->middleware('has.business');
        Route::post('/orders/{order}/invoice', [SalesController::class, 'generateInvoice'])->name('sales.generate-invoice')->middleware('has.business');
        
        Route::get('/customers', [SalesController::class, 'customers'])->name('sales.customers')->middleware('has.business');
        Route::get('/customers/{customer}', [SalesController::class, 'customerDetails'])->name('sales.customer-details')->middleware('has.business');
        Route::post('/customers', [SalesController::class, 'storeCustomer'])->name('sales.store-customer')->middleware('has.business');
        
        Route::get('/report', [SalesController::class, 'salesReport'])->name('sales.report')->middleware('has.business');
    });

    // Marketing Routes
    Route::prefix('marketing')->group(function () {
        // Promotions
        Route::get('/promotions', [MarketingController::class, 'promotions'])->name('marketing.promotions')->middleware('has.business');
        Route::get('/promotions/create', [MarketingController::class, 'createPromotion'])->name('marketing.promotions.create')->middleware('has.business');
        Route::post('/promotions', [MarketingController::class, 'storePromotion'])->name('marketing.promotions.store')->middleware('has.business');
        Route::get('/promotions/{promotion}/edit', [MarketingController::class, 'editPromotion'])->name('marketing.promotions.edit')->middleware('has.business');
        Route::put('/promotions/{promotion}', [MarketingController::class, 'updatePromotion'])->name('marketing.promotions.update')->middleware('has.business');
        Route::delete('/promotions/{promotion}', [MarketingController::class, 'destroyPromotion'])->name('marketing.promotions.destroy')->middleware('has.business');
        
        // Bulk SMS
        Route::get('/sms', [MarketingController::class, 'bulkSms'])->name('marketing.sms')->middleware('has.business');
        Route::post('/sms/send', [MarketingController::class, 'sendBulkSms'])->name('marketing.sms.send')->middleware('has.business');
        
        // Email Marketing
        Route::get('/email', [MarketingController::class, 'emailMarketing'])->name('marketing.email')->middleware('has.business');
        Route::post('/email/send', [MarketingController::class, 'sendBulkEmail'])->name('marketing.email.send')->middleware('has.business');
        
        // Advertising Campaigns
        Route::get('/advertising', [MarketingController::class, 'advertising'])->name('marketing.advertising')->middleware('has.business');
        Route::get('/advertising/create', [MarketingController::class, 'createCampaign'])->name('marketing.advertising.create')->middleware('has.business');
        Route::post('/advertising', [MarketingController::class, 'storeCampaign'])->name('marketing.advertising.store')->middleware('has.business');
        
        // Marketing Report
        Route::get('/report', [MarketingController::class, 'marketingReport'])->name('marketing.report')->middleware('has.business');
    });

    // Business Analysis Routes
    Route::prefix('analysis')->group(function () {
        Route::get('/', [BusinessAnalysisController::class, 'index'])->name('business.analysis.index')->middleware('has.business');
        Route::post('/generate', [BusinessAnalysisController::class, 'generateAnalysis'])->name('business.analysis.generate')->middleware('has.business');
        Route::get('/financial', [BusinessAnalysisController::class, 'financialReport'])->name('business.analysis.financial')->middleware('has.business');
    });

    // Payment Routes
    Route::prefix('payment')->group(function () {
        Route::post('/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout')->middleware('has.business');
        Route::get('/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
        Route::get('/failed', [PaymentController::class, 'paymentFailed'])->name('payment.failed');
        Route::get('/history', [PaymentController::class, 'paymentHistory'])->name('payment.history')->middleware('has.business');
        
        // PayPal callbacks
        Route::get('/paypal/success', [PaymentController::class, 'paypalSuccess'])->name('payment.paypal.success');
        Route::get('/paypal/cancel', [PaymentController::class, 'paypalCancel'])->name('payment.paypal.cancel');
    });

    // API Routes for payment callbacks
    Route::prefix('api')->group(function () {
        Route::post('/mpesa/callback', [PaymentController::class, 'mpesaCallback'])->name('api.mpesa.callback');
    });
});



// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


