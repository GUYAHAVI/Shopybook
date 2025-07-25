<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenantRegistrationController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\MarketingPostController;
use App\Http\Controllers\SocialMediaController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BusinessAnalysisController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Auth\PasswordVerificationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ServiceBookingController;
use App\Http\Controllers\CostController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CommissionPayoutController;
use App\Http\Controllers\SupplierController;

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
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

// Test route for flash messages
Route::get('/test-flash', function () {
    return redirect()->route('dashboard')->with('success', 'Flash message test successful! This message should appear immediately.');
})->middleware('auth');

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard')->middleware('auth');

Route::middleware(['auth', 'verified'])->group(function () {
    // Business Profile Routes
    Route::prefix('business')->group(function () {
        Route::get('/choose-type', [BusinessController::class, 'chooseType'])->name('business.choose-type');
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
                $valid = Hash::check($request->input('password'), auth()->user()->getAuthPassword());

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
        Route::get('/customers/{type}/{id}', [SalesController::class, 'showCustomer'])->name('sales.customers.show')->middleware('has.business');
        Route::post('/customers', [SalesController::class, 'storeCustomer'])->name('sales.store-customer')->middleware('has.business');
        Route::get('/customers/create', [SalesController::class, 'createCustomer'])->name('sales.customers.create')->middleware('has.business');
        
        Route::get('/report', [SalesController::class, 'salesReport'])->name('sales.report')->middleware('has.business');
        Route::get('/organization-customers', [SalesController::class, 'organizationCustomers'])->name('sales.organization-customers')->middleware('has.business');
        Route::get('/organization-customers/create', [SalesController::class, 'createOrganizationCustomer'])->name('sales.organization-customers.create')->middleware('has.business');
        Route::post('/organization-customers', [SalesController::class, 'storeOrganizationCustomer'])->name('sales.organization-customers.store')->middleware('has.business');
        Route::get('/organization-customers/{organizationCustomer}', [SalesController::class, 'showOrganizationCustomer'])->name('sales.organization-customers.show')->middleware('has.business');
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

    // Services Management Routes
    Route::prefix('services')->middleware('has.business')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/create', [ServiceController::class, 'create'])->name('services.create');
        Route::post('/', [ServiceController::class, 'store'])->name('services.store');
        Route::get('/{service}', [ServiceController::class, 'show'])->name('services.show');
        Route::get('/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
    });

    // Staff Management Routes
    Route::prefix('staff')->middleware('has.business')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('staff.index');
        Route::get('/create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('/', [StaffController::class, 'store'])->name('staff.store');
        Route::get('/{staff}', [StaffController::class, 'show'])->name('staff.show');
        Route::get('/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
        Route::put('/{staff}', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
    });

    // Service Bookings Routes
    Route::prefix('service-bookings')->middleware('has.business')->group(function () {
        Route::get('/', [ServiceBookingController::class, 'index'])->name('service-bookings.index');
        Route::get('/create', [ServiceBookingController::class, 'create'])->name('service-bookings.create');
        Route::post('/', [ServiceBookingController::class, 'store'])->name('service-bookings.store');
        Route::get('/{serviceBooking}', [ServiceBookingController::class, 'show'])->name('service-bookings.show');
        Route::get('/{serviceBooking}/edit', [ServiceBookingController::class, 'edit'])->name('service-bookings.edit');
        Route::put('/{serviceBooking}', [ServiceBookingController::class, 'update'])->name('service-bookings.update');
        Route::delete('/{serviceBooking}', [ServiceBookingController::class, 'destroy'])->name('service-bookings.destroy');
        
        // Additional service booking routes
        Route::post('/{serviceBooking}/add-service', [ServiceBookingController::class, 'addService'])->name('service-bookings.add-service');
        Route::delete('/service-item/{serviceItem}', [ServiceBookingController::class, 'removeService'])->name('service-bookings.remove-service');
        Route::put('/{serviceBooking}/complete', [ServiceBookingController::class, 'complete'])->name('service-bookings.complete');
    });

    // Commission Reports Route
    Route::get('/commission-reports', [StaffController::class, 'commissionReports'])->middleware('has.business')->name('commission-reports');

    // Costs Routes
    Route::prefix('costs')->middleware('has.business')->group(function () {
        Route::get('/', [CostController::class, 'index'])->name('costs.index');
        Route::get('/create', [CostController::class, 'create'])->name('costs.create');
        Route::post('/', [CostController::class, 'store'])->name('costs.store');
        Route::get('/{cost}', [CostController::class, 'show'])->name('costs.show');
        Route::get('/{cost}/edit', [CostController::class, 'edit'])->name('costs.edit');
        Route::put('/{cost}', [CostController::class, 'update'])->name('costs.update');
        Route::delete('/{cost}', [CostController::class, 'destroy'])->name('costs.destroy');
    });

    // Inventory Management Routes
    Route::prefix('inventory')->middleware('has.business')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/create', [InventoryController::class, 'create'])->name('inventory.create');
        Route::post('/', [InventoryController::class, 'store'])->name('inventory.store');
        Route::get('/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
        Route::get('/reports', [InventoryController::class, 'reports'])->name('inventory.reports');
        Route::get('/{inventory}', [InventoryController::class, 'show'])->name('inventory.show');
        Route::get('/{inventory}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
        Route::put('/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');
        Route::delete('/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
        Route::post('/{inventory}/adjust', [InventoryController::class, 'adjustQuantity'])->name('inventory.adjust');
    });

    // Commissions Routes
    Route::prefix('commissions')->middleware('has.business')->group(function () {
        Route::get('/', [CommissionPayoutController::class, 'index'])->name('commissions.index');
        Route::get('/create', [CommissionPayoutController::class, 'create'])->name('commissions.create');
        Route::post('/', [CommissionPayoutController::class, 'store'])->name('commissions.store');
        Route::get('/{commission}', [CommissionPayoutController::class, 'show'])->name('commissions.show');
        Route::get('/{commission}/edit', [CommissionPayoutController::class, 'edit'])->name('commissions.edit');
        Route::put('/{commission}', [CommissionPayoutController::class, 'update'])->name('commissions.update');
        Route::delete('/{commission}', [CommissionPayoutController::class, 'destroy'])->name('commissions.destroy');
        
        // Commission calculation and payout routes
        Route::post('/calculate', [CommissionPayoutController::class, 'calculateCommissions'])->name('commissions.calculate');
        Route::post('/{commission}/payout', [CommissionPayoutController::class, 'payout'])->name('commissions.payout');
    });

    // Suppliers Routes
    Route::prefix('suppliers')->middleware('has.business')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
        Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    });

    // Employees Routes
    Route::prefix('employees')->middleware('has.business')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });

    // Categories Routes  
    Route::prefix('categories')->middleware('has.business')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/{category}', [CategoryController::class, 'show'])->name('categories.show');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // Brands Routes
    Route::prefix('brands')->middleware('has.business')->group(function () {
        Route::get('/', [BrandController::class, 'index'])->name('brands.index');
        Route::get('/create', [BrandController::class, 'create'])->name('brands.create');
        Route::post('/', [BrandController::class, 'store'])->name('brands.store');
        Route::get('/{brand}', [BrandController::class, 'show'])->name('brands.show');
        Route::get('/{brand}/edit', [BrandController::class, 'edit'])->name('brands.edit');
        Route::put('/{brand}', [BrandController::class, 'update'])->name('brands.update');
        Route::delete('/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');
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

    // Marketing & Social Media Routes
    Route::prefix('marketing')->middleware('has.business')->group(function () {
        Route::get('/social-media', [MarketingPostController::class, 'index'])->name('marketing.social-media');
        
        // Marketing Posts
        Route::prefix('posts')->group(function () {
            Route::post('/', [MarketingPostController::class, 'store'])->name('marketing.posts.store');
            Route::get('/{post}', [MarketingPostController::class, 'show'])->name('marketing.posts.show');
            Route::get('/{post}/edit', [MarketingPostController::class, 'edit'])->name('marketing.posts.edit');
            Route::put('/{post}', [MarketingPostController::class, 'update'])->name('marketing.posts.update');
            Route::delete('/{post}', [MarketingPostController::class, 'destroy'])->name('marketing.posts.destroy');
            Route::post('/{post}/publish', [MarketingPostController::class, 'publish'])->name('marketing.posts.publish');
            Route::get('/{post}/analytics', [MarketingPostController::class, 'analytics'])->name('marketing.posts.analytics');
            Route::post('/{post}/duplicate', [MarketingPostController::class, 'duplicate'])->name('marketing.posts.duplicate');
        });
        
        // Social Media Connections
        Route::prefix('social')->group(function () {
            Route::get('/connect/{platform}', [SocialMediaController::class, 'connect'])->name('social.connect');
            Route::get('/callback/{platform}', [SocialMediaController::class, 'callback'])->name('social.callback');
            Route::delete('/disconnect/{account}', [SocialMediaController::class, 'disconnect'])->name('social.disconnect');
            Route::post('/refresh/{account}', [SocialMediaController::class, 'refreshToken'])->name('social.refresh');
        });

        // Billing Routes
        Route::prefix('billing')->group(function () {
            Route::get('/upgrade', [BillingController::class, 'upgrade'])->name('billing.upgrade');
            Route::post('/upgrade', [BillingController::class, 'processUpgrade'])->name('billing.process-upgrade');
            Route::get('/dashboard', [BillingController::class, 'dashboard'])->name('billing.dashboard');
            Route::post('/cancel', [BillingController::class, 'cancel'])->name('billing.cancel');
        });
    });

    // API Routes for payment callbacks
    Route::prefix('api')->group(function () {
        Route::post('/mpesa/callback', [PaymentController::class, 'mpesaCallback'])->name('api.mpesa.callback');
    });
});

// Legal and Compliance Routes (Public - No Authentication Required)
Route::get('/privacy-policy', function () {
    return view('legal.privacy-policy');
})->name('privacy-policy');

Route::get('/terms-of-service', function () {
    return view('legal.terms-of-service');
})->name('terms-of-service');

Route::get('/data-deletion', function () {
    return view('legal.data-deletion');
})->name('data-deletion');

Route::get('/help', function () {
    return view('help.index');
})->name('help');

Route::get('/deauthorize-callback', function () {
    return view('legal.deauthorize-callback');
})->name('deauthorize-callback');

// Facebook App Compliance Routes
Route::post('/auth/facebook/deauthorize', [SocialMediaController::class, 'facebookDeauthorize'])->name('facebook.deauthorize');
Route::get('/auth/facebook/data-deletion', function () {
    return redirect()->route('data-deletion');
})->name('facebook.data-deletion');



