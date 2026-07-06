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
use App\Http\Controllers\SalaryAdvanceController;
use App\Http\Controllers\ServiceBookingController;
use App\Http\Controllers\CostController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CommissionPayoutController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\OrderController;

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductConversionController;
use App\Http\Controllers\AIAnalysisController;
use App\Http\Controllers\AIContentController;
use App\Http\Controllers\AIAdviceController;
use App\Http\Controllers\ContinuousKnowledgeController;
use App\Http\Controllers\AICommunicationController;
use App\Http\Controllers\LearningTriggerController;
use App\Http\Controllers\TwoFactorAuthController;
use App\Http\Controllers\PWAController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ReturnsController;
use App\Http\Controllers\OCRController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TeamController;

// Debug routes removed for security

Route::get('/', function (Request $request) {
    $host = $request->getHost();
    if (preg_match('/^([^.]+)\.shopybook\.com$/i', $host, $matches)
        && !in_array(strtolower($host), ['shopybook.com', 'www.shopybook.com'], true)) {
        return app(\App\Http\Controllers\PublicWebsiteController::class)
            ->homepage($request, $matches[1]);
    }

    return app(IndexController::class)->index($request);
})->name('index');

Route::get('/businesses', [BusinessController::class, 'index'])->name('businesses');
Route::get('/docs', function () {
    return view('docs.index');
})->name('docs');
Route::get('/pricing', function () {
    return view('pricing');
})->name('pricing');

use App\Http\Controllers\PublicWebsiteController;
use App\Http\Controllers\TestimonialController;
Route::get('/site/{subdomain}', [PublicWebsiteController::class, 'homepage'])
    ->name('public.website.direct');
Route::get('/site/{subdomain}/{slug}', [PublicWebsiteController::class, 'page'])
    ->name('public.website.direct.page');
Route::post('/site/{subdomain}/contact', [PublicWebsiteController::class, 'submitContact'])
    ->name('public.website.contact');
Route::post('/site/{subdomain}/order', [PublicWebsiteController::class, 'placeOrder'])
    ->name('public.website.order');
Route::post('/site/{subdomain}/testimonial', [TestimonialController::class, 'submitBusiness'])
    ->name('public.website.testimonial');

// Public platform testimonial submission
Route::post('/testimonials', [TestimonialController::class, 'submitPlatform'])
    ->name('testimonials.submit');

// Public website viewer (subdomain-based - *.shopybook.com)
Route::domain('{subdomain}.' . env('APP_DOMAIN', 'shopybook.com'))->group(function () {
    Route::get('/', [PublicWebsiteController::class, 'homepage'])
        ->name('public.website.subdomain');
    Route::get('/{slug}', [PublicWebsiteController::class, 'page'])
        ->name('public.website.subdomain.page');
    Route::post('/contact', [PublicWebsiteController::class, 'submitContact'])
        ->name('public.website.subdomain.contact');
    Route::post('/order', [PublicWebsiteController::class, 'placeOrder'])
        ->name('public.website.subdomain.order');
    Route::post('/testimonial', [TestimonialController::class, 'submitBusiness'])
        ->name('public.website.subdomain.testimonial');
});

Route::post('/orders', [OrderController::class, 'store'])->middleware('throttle:30,1')->name('orders.store');
Route::post('/service-bookings/public', [ServiceBookingController::class, 'storePublic'])->middleware('throttle:30,1')->name('service-bookings.store-public');

// Contact Form Route
use App\Http\Controllers\ContactFormController;
Route::post('/contact', [ContactFormController::class, 'submit'])->middleware('throttle:5,1')->name('contact.submit');

// Chatbot Route
use App\Http\Controllers\ChatbotController;
Route::post('/chatbot/message', [ChatbotController::class, 'message'])->middleware('throttle:10,1')->name('chatbot.message');

// Language Switch Route
Route::get('/language/{language}', function ($language) {
    $allowedLanguages = ['en', 'sw', 'sheng'];
    if (in_array($language, $allowedLanguages)) {
        session(['language' => $language]);
    }
    return redirect()->back();
})->name('language.switch');

Auth::routes(['verify' => true]);

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    \Illuminate\Support\Facades\Log::info('Email verification attempt', [
        'user_id' => $request->route('id'),
        'hash' => $request->route('hash'),
        'signature' => $request->query('signature'),
        'expires' => $request->query('expires'),
        'request_url' => $request->fullUrl(),
        'has_valid_signature' => $request->hasValidSignature(),
    ]);
    
    $request->fulfill();
    return redirect('/business/choose-type');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');



// PUBLIC BUSINESS SHOW ROUTE - Moved AFTER specific business routes to avoid conflicts

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard')->middleware('auth');

Route::get('/business/choose-type', [BusinessController::class, 'chooseType'])->name('business.choose-type')->middleware('auth');
Route::get('/business/create', [BusinessController::class, 'create'])->name('business.create')->middleware('auth');
Route::post('/business/store', [BusinessController::class, 'store'])->name('business.store')->middleware('auth');
Route::post('/business/enhance-description', [BusinessController::class, 'enhanceDescription'])->name('business.enhance-description')->middleware('auth');
Route::post('/business/generate-logo', [BusinessController::class, 'generateLogo'])->name('business.generate-logo')->middleware('auth');

// OCR Data Capture Routes
Route::prefix('ocr')->middleware(['auth', 'has.business', 'permission:products'])->group(function () {
    Route::get('/', [OCRController::class, 'index'])->name('ocr.index');
    Route::post('/extract', [OCRController::class, 'extract'])->name('ocr.extract');
    Route::post('/save', [OCRController::class, 'save'])->name('ocr.save');
});

// PUBLIC BUSINESS SHOW ROUTE - Moved to after ALL business routes

// REMOVED: This route will be moved after specific business routes to avoid conflicts

// Social Media Connections (available to all authenticated users)
Route::prefix('social')->middleware('auth')->group(function () {
    Route::get('/connect/{platform}', [SocialMediaController::class, 'connect'])->name('social.connect');
    Route::get('/callback/{platform}', [SocialMediaController::class, 'callback'])->name('social.callback');
    Route::delete('/disconnect/{account}', [SocialMediaController::class, 'disconnect'])->name('social.disconnect');
    Route::post('/refresh/{account}', [SocialMediaController::class, 'refreshToken'])->name('social.refresh');
});

// Marketing & Social Media Routes (available to all authenticated users with business)
Route::prefix('marketing')->middleware(['auth', 'has.business', 'permission:marketing'])->group(function () {
    Route::get('/social-media', [MarketingPostController::class, 'index'])->name('marketing.social-media');
    
    // Marketing Posts
    Route::prefix('posts')->group(function () {
        Route::get('/', [MarketingPostController::class, 'postsIndex'])->name('marketing.posts.index');
        Route::post('/', [MarketingPostController::class, 'store'])->name('marketing.posts.store');
        Route::get('/{post}', [MarketingPostController::class, 'show'])->name('marketing.posts.show');
        Route::get('/{post}/edit', [MarketingPostController::class, 'edit'])->name('marketing.posts.edit');
        Route::put('/{post}', [MarketingPostController::class, 'update'])->name('marketing.posts.update');
        Route::delete('/{post}', [MarketingPostController::class, 'destroy'])->name('marketing.posts.destroy');
        Route::post('/{post}/publish', [MarketingPostController::class, 'publish'])->name('marketing.posts.publish');
        Route::get('/{post}/analytics', [MarketingPostController::class, 'analytics'])->name('marketing.posts.analytics');
        Route::post('/{post}/duplicate', [MarketingPostController::class, 'duplicate'])->name('marketing.posts.duplicate');
        
        // AI-powered features
        Route::post('/ai/generate-content', [MarketingPostController::class, 'generateContent'])->name('marketing.posts.ai.generate');
        Route::post('/ai/enhance-content', [MarketingPostController::class, 'enhanceContent'])->name('marketing.posts.ai.enhance');
        Route::post('/ai/generate-image-prompts', [MarketingPostController::class, 'generateImagePrompts'])->name('marketing.posts.ai.image-prompts');
        Route::post('/ai/enhance-image-prompt', [MarketingPostController::class, 'enhanceImagePrompt'])->name('marketing.posts.ai.enhance-prompt');
        Route::post('/ai/generate-image', [MarketingPostController::class, 'generateImage'])->name('marketing.posts.ai.generate-image');
        Route::post('/ai/generate-video-prompts', [MarketingPostController::class, 'generateVideoPrompts'])->name('marketing.posts.ai.video-prompts');
        Route::post('/ai/enhance-video-prompt', [MarketingPostController::class, 'enhanceVideoPrompt'])->name('marketing.posts.ai.enhance-video-prompt');
    });
});

// Business Show Route (Public) — defined AFTER the authenticated /business group to avoid conflicts

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Routes are now properly ordered - debug routes removed

    // Business Profile Routes (for users with businesses)
    Route::prefix('business')->middleware('has.business')->group(function () {
        Route::get('/profile', [BusinessController::class, 'edit'])->name('business.profile');
        Route::get('/edit', [BusinessController::class, 'edit'])->name('business.edit');
        Route::put('/update', [BusinessController::class, 'update'])->name('business.update');
        
        // Business deletion with email verification
        Route::post('/deletion/send-code', [BusinessController::class, 'sendDeletionCode'])->name('business.deletion.send-code');
        Route::post('/deletion/verify', [BusinessController::class, 'verifyAndDelete'])->name('business.deletion.verify');
        
        // Legacy deletion routes (kept for backward compatibility)
        Route::post('/initiate-deletion', [BusinessController::class, 'initiateDeletion'])->name('business.initiate-deletion');
        Route::delete('/{business}', [BusinessController::class, 'destroy'])
            ->name('business.destroy')
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

  

    // Two-Factor Authentication Routes
    Route::prefix('2fa')->middleware('auth')->group(function () {
        Route::get('/verify', [TwoFactorAuthController::class, 'showVerificationForm'])->name('2fa.verify.form');
        Route::post('/send', [TwoFactorAuthController::class, 'sendCode'])->name('2fa.send');
        Route::post('/verify', [TwoFactorAuthController::class, 'verifyCode'])->name('2fa.verify');
        Route::post('/resend', [TwoFactorAuthController::class, 'resendCode'])->name('2fa.resend');
            Route::post('/cancel', [TwoFactorAuthController::class, 'cancelVerification'])->name('2fa.cancel');
});

// PWA Routes
Route::prefix('pwa')->middleware(['auth'])->group(function () {
    Route::get('/status', [PWAController::class, 'getStatus'])->name('pwa.status');
    Route::post('/sync', [PWAController::class, 'syncOfflineData'])->name('pwa.sync');
    Route::get('/offline-data', [PWAController::class, 'getOfflineData'])->name('pwa.offline-data');
    Route::post('/store-action', [PWAController::class, 'storeOfflineAction'])->name('pwa.store-action');
    Route::get('/offline-actions', [PWAController::class, 'getOfflineActions'])->name('pwa.offline-actions');
    Route::post('/clear-actions', [PWAController::class, 'clearOfflineActions'])->name('pwa.clear-actions');
    Route::post('/update-cache', [PWAController::class, 'updateCache'])->name('pwa.update-cache');
});

// PWA Install Guide Route
Route::get('/pwa/install-guide', function () {
    return view('pwa.install-guide');
})->name('pwa.install-guide')->middleware('auth');

// PWA Debug Route
Route::get('/pwa/debug', function () {
    return view('pwa.debug');
})->name('pwa.debug')->middleware('auth');

// Notifications Routes
Route::prefix('notifications')->name('notifications.')->middleware(['auth', 'has.business'])->group(function () {
    Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
    Route::get('/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('unread-count');
    Route::patch('/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::patch('/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
});

// ── Team / RBAC Routes ──────────────────────────────────────────────────────
Route::prefix('team')->name('team.')->middleware(['auth', 'has.business'])->group(function () {
    Route::get('/',                          [TeamController::class, 'index'])->name('index');
    Route::post('/',                         [TeamController::class, 'store'])->name('store');
    Route::put('/{member}',                  [TeamController::class, 'update'])->name('update');
    Route::delete('/{member}',               [TeamController::class, 'destroy'])->name('destroy');
    Route::patch('/{member}/toggle-status',  [TeamController::class, 'toggleStatus'])->name('toggleStatus');
    Route::get('/role-defaults/{role}',      [TeamController::class, 'roleDefaults'])->name('roleDefaults');
    Route::get('/logs',                      [TeamController::class, 'logs'])->name('logs');
});


    // Products Routes
    Route::prefix('products')->middleware(['auth', 'has.business', 'permission:products'])->group(function () {
        Route::get('/', [ProductsController::class, 'index'])->name('products.index');
        
        // Specific routes MUST come before dynamic {product} routes
        Route::get('/create', [ProductsController::class, 'create'])->name('products.create');
        Route::get('/quick-create', [ProductsController::class, 'quickCreate'])->name('products.quick-create');
        
        // AI Enhancement Route
        Route::post('/enhance-description', [ProductsController::class, 'enhanceDescription'])->name('products.enhance-description');
        
        // Bulk Import Routes
        Route::get('/bulk-import', [ProductsController::class, 'bulkImport'])->name('products.bulk-import');
        Route::post('/bulk-import/process', [ProductsController::class, 'processBulkImport'])->name('products.bulk-import.process');
        Route::get('/bulk-import/template', [ProductsController::class, 'downloadTemplate'])->name('products.bulk-import.template');
        
        // OCR Routes
        Route::post('/ocr/preview', [ProductsController::class, 'previewOCRResults'])->name('products.ocr.preview');
        Route::post('/ocr/process', [ProductsController::class, 'processOCRImages'])->name('products.ocr.process');
        
        // Inventory Routes
        Route::get('/inventory', [ProductsController::class, 'inventory'])->name('products.inventory');
        Route::get('/inventory/export', [ProductsController::class, 'exportInventory'])->name('products.inventory.export');
        
        // Product Receiving Routes (BEFORE {product} routes)
        Route::get('/receive', [ProductsController::class, 'showReceiveForm'])->name('products.receive');
        Route::post('/receive/process', [ProductsController::class, 'processReceive'])->name('products.receive.process');
        Route::get('/receive/history', [ProductsController::class, 'receiptHistory'])->name('products.receive.history');
        Route::get('/receive/{receipt}', [ProductsController::class, 'showReceipt'])->name('products.receive.show');
        
        // POST routes
        Route::post('/', [ProductsController::class, 'store'])->name('products.store');
        Route::post('/quick-store', [ProductsController::class, 'quickStore'])->name('products.quick-store');
        Route::post('/{product}/stock', [ProductsController::class, 'updateStock'])->name('products.update-stock');
        
        // Dynamic {product} routes MUST come LAST
        Route::get('/{product}', [ProductsController::class, 'show'])->name('products.show');
        Route::get('/{product}/edit', [ProductsController::class, 'edit'])->name('products.edit');
        Route::put('/{product}', [ProductsController::class, 'update'])->name('products.update');
        Route::delete('/{product}', [ProductsController::class, 'destroy'])->name('products.destroy');
    });

    // Sales Routes
    Route::prefix('sales')->middleware(['auth', 'has.business', 'permission:pos,orders,customers'])->group(function () {
        Route::get('/pos', [SalesController::class, 'pos'])->name('sales.pos');
        Route::get('/orders', [SalesController::class, 'orders'])->name('sales.orders');
        Route::get('/orders/{order}/details', [SalesController::class, 'orderDetails'])->name('sales.order-details');
        Route::put('/orders/{order}/status', [SalesController::class, 'updateOrderStatus'])->name('sales.update-order-status');
        Route::get('/orders/{order}/receipt', [SalesController::class, 'printReceipt'])->name('sales.print-receipt');
        Route::get('/orders/{order}/invoice', [SalesController::class, 'generateInvoice'])->name('sales.generate-invoice');
        Route::get('/orders/{order}/invoice/view', [SalesController::class, 'viewInvoice'])->name('sales.view-invoice');
        Route::post('/orders/{order}/record-payment', [SalesController::class, 'recordPayment'])->name('sales.record-payment');
        
        // Credit Note Routes
        Route::get('/orders/{order}/credit-note/create', [SalesController::class, 'createCreditNote'])->name('sales.credit-note.create');
        Route::post('/orders/{order}/credit-note', [SalesController::class, 'storeCreditNote'])->name('sales.credit-note.store');
        Route::post('/credit-notes/{creditNote}/send-otp', [SalesController::class, 'sendCreditNoteOtp'])->name('sales.credit-note.send-otp');
        Route::post('/credit-notes/{creditNote}/verify-otp', [SalesController::class, 'verifyCreditNoteOtp'])->name('sales.credit-note.verify-otp');
        Route::get('/credit-notes', [SalesController::class, 'listCreditNotes'])->name('sales.credit-notes.index');
        Route::get('/credit-notes/{creditNote}', [SalesController::class, 'viewCreditNote'])->name('sales.credit-note.view');
        
        // Archive Orders
        Route::post('/orders/{order}/archive', [SalesController::class, 'archiveOrder'])->name('sales.archive-order');
        Route::post('/orders/bulk-archive', [SalesController::class, 'bulkArchiveOrders'])->name('sales.bulk-archive-orders');
        Route::get('/orders/archived', [SalesController::class, 'archivedOrders'])->name('sales.archived-orders');
        
        // Debt Management
        Route::get('/debts/customers', [SalesController::class, 'customerDebts'])->name('sales.customer-debts');
        Route::get('/debts/suppliers', [SalesController::class, 'supplierDebts'])->name('sales.supplier-debts');
        Route::post('/debts/suppliers', [SalesController::class, 'storeSupplierDebt'])->name('sales.supplier-debts.store');
        Route::post('/debts/suppliers/{debt}/payment', [SalesController::class, 'recordSupplierPayment'])->name('sales.supplier-debts.payment');
        
Route::get('/receipts/{receiptNumber}/reprint', [SalesController::class, 'reprintReceipt'])->name('sales.reprint-receipt');
Route::get('/receipts/search', [SalesController::class, 'searchReceipts'])->name('sales.search-receipts');
Route::post('/orders', [SalesController::class, 'createOrder'])->name('sales.create-order');
        Route::post('/calculate-dynamic-conversion', [SalesController::class, 'calculateDynamicConversion'])->name('sales.calculate-dynamic-conversion');
        Route::get('/customers', [SalesController::class, 'customers'])->name('sales.customers');
        Route::get('/customers/create', [SalesController::class, 'createCustomer'])->name('sales.customers.create');
        Route::get('/customers/{customer}', [SalesController::class, 'customerDetails'])->name('sales.customer-details');
        Route::get('/customers/{type}/{id}', [SalesController::class, 'showCustomer'])->name('sales.customers.show');
        Route::post('/customers', [SalesController::class, 'storeCustomer'])->name('sales.store-customer');
        Route::put('/customers/{customer}', [SalesController::class, 'updateCustomer'])->name('sales.update-customer');
        Route::delete('/customers/{customer}', [SalesController::class, 'deleteCustomer'])->name('sales.delete-customer');
        
        // Organization customers
        Route::get('/organization-customers/create', [SalesController::class, 'createOrganizationCustomer'])->name('sales.organization-customers.create');
        Route::post('/organization-customers', [SalesController::class, 'storeOrganizationCustomer'])->name('sales.store-organization-customer');
    });

    // Contact Import Routes
    Route::prefix('contacts')->middleware(['auth', 'has.business', 'permission:marketing'])->group(function () {
        Route::get('/', [\App\Http\Controllers\ContactImportController::class, 'index'])->name('contacts.index');
        Route::get('/create-group', [\App\Http\Controllers\ContactImportController::class, 'createGroup'])->name('contacts.create-group');
        Route::post('/store-group', [\App\Http\Controllers\ContactImportController::class, 'storeGroup'])->name('contacts.store-group');
        Route::get('/{id}', [\App\Http\Controllers\ContactImportController::class, 'show'])->name('contacts.show');
        Route::get('/{id}/import', [\App\Http\Controllers\ContactImportController::class, 'showImport'])->name('contacts.import');
        Route::post('/{id}/import-csv', [\App\Http\Controllers\ContactImportController::class, 'importCsv'])->name('contacts.import-csv');
        Route::post('/{id}/import-vcf', [\App\Http\Controllers\ContactImportController::class, 'importVcf'])->name('contacts.import-vcf');
        Route::get('/{id}/google-import', [\App\Http\Controllers\ContactImportController::class, 'initiateGoogleImport'])->name('contacts.google-import');
        Route::get('/google/callback', [\App\Http\Controllers\ContactImportController::class, 'handleGoogleCallback'])->name('contacts.google-callback');
        Route::post('/{id}/sync-customers', [\App\Http\Controllers\ContactImportController::class, 'syncCustomers'])->name('contacts.sync-customers');
        Route::post('/{id}/sync-employees', [\App\Http\Controllers\ContactImportController::class, 'syncEmployees'])->name('contacts.sync-employees');
        Route::delete('/{id}', [\App\Http\Controllers\ContactImportController::class, 'destroyGroup'])->name('contacts.destroy-group');
        Route::delete('/{groupId}/contact/{contactId}', [\App\Http\Controllers\ContactImportController::class, 'destroyContact'])->name('contacts.destroy-contact');
        Route::get('/template/download', [\App\Http\Controllers\ContactImportController::class, 'downloadTemplate'])->name('contacts.download-template');
    });

    // Marketing Routes
    Route::prefix('marketing')->middleware(['has.business', 'permission:marketing'])->group(function () {
        // Promotions
        Route::get('/promotions', [MarketingController::class, 'promotions'])->name('marketing.promotions')->middleware('has.business');
        Route::get('/promotions/create', [MarketingController::class, 'createPromotion'])->name('marketing.promotions.create')->middleware('has.business');
        Route::get('/create-promotion', [MarketingController::class, 'createPromotion'])->name('marketing.create-promotion')->middleware('has.business');
        Route::post('/promotions', [MarketingController::class, 'storePromotion'])->name('marketing.promotions.store')->middleware('has.business');
        Route::get('/promotions/{promotion}/edit', [MarketingController::class, 'editPromotion'])->name('marketing.promotions.edit')->middleware('has.business');
        Route::put('/promotions/{promotion}', [MarketingController::class, 'updatePromotion'])->name('marketing.promotions.update')->middleware('has.business');
        Route::delete('/promotions/{promotion}', [MarketingController::class, 'destroyPromotion'])->name('marketing.promotions.destroy')->middleware('has.business');
        
        // Bulk SMS
        Route::get('/sms', [MarketingController::class, 'bulkSms'])->name('marketing.sms')->middleware('has.business');
        Route::get('/bulk-sms', [MarketingController::class, 'bulkSms'])->name('marketing.bulk-sms')->middleware('has.business');
        Route::post('/sms/send', [MarketingController::class, 'sendBulkSms'])->name('marketing.sms.send')->middleware('has.business');
        
        // Email Marketing
        Route::get('/email', [MarketingController::class, 'emailMarketing'])->name('marketing.email')->middleware('has.business');
        Route::post('/email/send', [MarketingController::class, 'sendBulkEmail'])->name('marketing.email.send')->middleware('has.business');
        
        // Advertising Campaigns
        Route::get('/advertising', [MarketingController::class, 'advertising'])->name('marketing.advertising')->middleware('has.business');
        Route::get('/advertising/create', [MarketingController::class, 'createCampaign'])->name('marketing.advertising.create')->middleware('has.business');
        Route::get('/create-campaign', [MarketingController::class, 'createCampaign'])->name('marketing.create-campaign')->middleware('has.business');
        Route::post('/advertising', [MarketingController::class, 'storeCampaign'])->name('marketing.advertising.store')->middleware('has.business');
        
        // Marketing Report
        Route::get('/report', [MarketingController::class, 'marketingReport'])->name('marketing.report')->middleware('has.business');
        
        // Video Generation Routes
        Route::post('/video/generate', [MarketingController::class, 'generateVideo'])->name('marketing.video.generate')->middleware('has.business');
        Route::get('/video/styles', [MarketingController::class, 'getVideoStyles'])->name('marketing.video.styles')->middleware('has.business');
        Route::post('/video/preview-prompt', [MarketingController::class, 'previewVideoPrompt'])->name('marketing.video.preview-prompt')->middleware('has.business');
        Route::delete('/video/cleanup', [MarketingController::class, 'cleanupVideos'])->name('marketing.video.cleanup')->middleware('has.business');
    });

    // Business Analysis Routes
    Route::prefix('analysis')->middleware(['has.business', 'permission:reports'])->group(function () {
        Route::get('/', [BusinessAnalysisController::class, 'index'])->name('business.analysis.index');
        Route::post('/generate', [BusinessAnalysisController::class, 'generateAnalysis'])->name('business.analysis.generate');
        Route::get('/financial', [BusinessAnalysisController::class, 'financialReport'])->name('business.analysis.financial');
    });

    // Enhanced AI Business Analysis Routes (Canadian Model)
    Route::prefix('ai-analysis')->middleware(['has.business', 'permission:reports'])->group(function () {
        Route::get('/', [\App\Http\Controllers\EnhancedBusinessAnalysisController::class, 'index'])->name('business.ai-analysis.index');
        Route::post('/generate', [\App\Http\Controllers\EnhancedBusinessAnalysisController::class, 'generateEnhancedAnalysis'])->name('business.ai-analysis.generate');
        Route::get('/financial', [\App\Http\Controllers\EnhancedBusinessAnalysisController::class, 'getFinancialAnalysis'])->name('business.ai-analysis.financial');
        Route::get('/operational', [\App\Http\Controllers\EnhancedBusinessAnalysisController::class, 'getOperationalAnalysis'])->name('business.ai-analysis.operational');
        Route::get('/growth', [\App\Http\Controllers\EnhancedBusinessAnalysisController::class, 'getGrowthPredictions'])->name('business.ai-analysis.growth');
        Route::get('/benchmarks', [\App\Http\Controllers\EnhancedBusinessAnalysisController::class, 'getBenchmarkComparison'])->name('business.ai-analysis.benchmarks');
        Route::post('/export', [\App\Http\Controllers\EnhancedBusinessAnalysisController::class, 'exportAnalysisReport'])->name('business.ai-analysis.export');
    });

    // Tax Management Routes
    Route::prefix('tax')->middleware(['has.business', 'permission:expenses'])->group(function () {
        Route::get('/settings', [TaxController::class, 'settings'])->name('tax.settings');
        Route::put('/settings', [TaxController::class, 'updateSettings'])->name('tax.settings.update');
        Route::get('/reports', [TaxController::class, 'reports'])->name('tax.reports');
        Route::get('/export', [TaxController::class, 'exportReport'])->name('tax.export');
        Route::get('/dashboard', [TaxController::class, 'dashboard'])->name('tax.dashboard');
    });

    // Comprehensive Reports Routes
    Route::prefix('reports')->middleware(['has.business', 'permission:reports'])->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('reports.index');
        Route::get('/sales', [ReportsController::class, 'salesReport'])->name('reports.sales');
        Route::get('/products', [ReportsController::class, 'productReport'])->name('reports.products');
        Route::get('/customers', [ReportsController::class, 'customerReport'])->name('reports.customers');
        Route::get('/inventory', [ReportsController::class, 'inventoryReport'])->name('reports.inventory');
        Route::get('/profit-loss', [ReportsController::class, 'profitLossReport'])->name('reports.profit-loss');
        Route::get('/export-pdf', [ReportsController::class, 'exportPdf'])->name('reports.export-pdf');
    });

    // Comprehensive Settings Routes
    Route::prefix('settings')->middleware(['has.business', 'permission:settings'])->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/general', [SettingsController::class, 'updateGeneral'])->name('settings.update.general');
        Route::put('/pos', [SettingsController::class, 'updatePOS'])->name('settings.update.pos');
        Route::put('/inventory', [SettingsController::class, 'updateInventory'])->name('settings.update.inventory');
        Route::put('/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.update.notifications');
        Route::put('/invoice', [SettingsController::class, 'updateInvoice'])->name('settings.update.invoice');
        Route::put('/display', [SettingsController::class, 'updateDisplay'])->name('settings.update.display');
        Route::put('/business-hours', [SettingsController::class, 'updateBusinessHours'])->name('settings.update.business-hours');
        Route::put('/security', [SettingsController::class, 'updateSecurity'])->name('settings.update.security');
        Route::put('/payments', [SettingsController::class, 'updatePayments'])->name('settings.update.payments');
        Route::post('/reset-defaults', [SettingsController::class, 'resetToDefaults'])->name('settings.reset-defaults');
    });

    // Subscription Routes
    Route::prefix('subscription')->middleware(['auth', 'has.business'])->group(function () {
        Route::post('/upgrade', [App\Http\Controllers\SubscriptionController::class, 'upgrade'])->name('subscription.upgrade');
        Route::post('/check-payment-status', [App\Http\Controllers\SubscriptionController::class, 'checkPaymentStatus'])->name('subscription.check.status');
    });
    
    // Paystack Webhook (no auth required - called by Paystack)
    Route::post('/subscription/paystack/webhook', [App\Http\Controllers\SubscriptionController::class, 'paystackWebhook'])->name('subscription.paystack.webhook');

    // Returns & Refunds Routes
    Route::prefix('returns')->middleware(['auth', 'has.business', 'permission:returns'])->group(function () {
        Route::get('/', [ReturnsController::class, 'index'])->name('returns.index');
        Route::get('/create', [ReturnsController::class, 'create'])->name('returns.create');
        Route::post('/', [ReturnsController::class, 'store'])->name('returns.store');
        Route::get('/{return}', [ReturnsController::class, 'show'])->name('returns.show');
        Route::post('/{return}/approve', [ReturnsController::class, 'approve'])->name('returns.approve');
        Route::post('/{return}/reject', [ReturnsController::class, 'reject'])->name('returns.reject');
        Route::post('/{return}/complete', [ReturnsController::class, 'complete'])->name('returns.complete');
        Route::get('/stats/json', [ReturnsController::class, 'stats'])->name('returns.stats');
    });

    // Services Management Routes
    Route::prefix('services')->middleware(['has.business', 'permission:services'])->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/create', [ServiceController::class, 'create'])->name('services.create');
        Route::post('/', [ServiceController::class, 'store'])->name('services.store');
        
        // AI Enhancement Route
        Route::post('/enhance-description', [ServiceController::class, 'enhanceDescription'])->name('services.enhance-description');
        Route::get('/{service}', [ServiceController::class, 'show'])->name('services.show');
        Route::get('/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
    });

    // Staff Management Routes
    Route::prefix('staff')->middleware(['has.business', 'permission:staff'])->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('staff.index');
        Route::get('/create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('/', [StaffController::class, 'store'])->name('staff.store');
        
        // Salary calculation routes - must come before wildcard routes
        Route::get('/salary-calculations', [StaffController::class, 'salaryCalculations'])->name('staff.salary-calculations');
        
        // Wildcard routes - must come after specific routes
        Route::get('/{staff}', [StaffController::class, 'show'])->name('staff.show');
        Route::get('/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
        Route::put('/{staff}', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
        Route::get('/{staff}/salary-details', [StaffController::class, 'salaryDetails'])->name('staff.salary-details');
    });

    // Salary Advance Routes
    Route::prefix('salary-advances')->middleware(['has.business', 'permission:staff'])->group(function () {
        Route::get('/', [SalaryAdvanceController::class, 'index'])->name('salary-advances.index');
        Route::get('/create', [SalaryAdvanceController::class, 'create'])->name('salary-advances.create');
        Route::post('/', [SalaryAdvanceController::class, 'store'])->name('salary-advances.store');
        Route::get('/staff-summary', [SalaryAdvanceController::class, 'staffSummary'])->name('salary-advances.staff-summary');
        Route::get('/{salaryAdvance}', [SalaryAdvanceController::class, 'show'])->name('salary-advances.show');
        Route::delete('/{salaryAdvance}', [SalaryAdvanceController::class, 'destroy'])->name('salary-advances.destroy');
        
        // Additional salary advance actions
        Route::post('/{salaryAdvance}/approve', [SalaryAdvanceController::class, 'approve'])->name('salary-advances.approve');
        Route::post('/{salaryAdvance}/mark-as-paid', [SalaryAdvanceController::class, 'markAsPaid'])->name('salary-advances.mark-as-paid');
        Route::post('/{salaryAdvance}/cancel', [SalaryAdvanceController::class, 'cancel'])->name('salary-advances.cancel');
    });

    // Service Bookings Routes
    Route::prefix('service-bookings')->middleware(['has.business', 'permission:services'])->group(function () {
        Route::get('/', [ServiceBookingController::class, 'index'])->name('service-bookings.index');
        Route::get('/create', [ServiceBookingController::class, 'create'])->name('service-bookings.create');
        Route::post('/', [ServiceBookingController::class, 'store'])->name('service-bookings.store');
        
        // Bulk entry routes
        Route::get('/bulk-create', [ServiceBookingController::class, 'bulkCreate'])->name('service-bookings.bulk-create');
        Route::post('/bulk-store', [ServiceBookingController::class, 'bulkStore'])->name('service-bookings.bulk-store');
        
        Route::get('/{serviceBooking}', [ServiceBookingController::class, 'show'])->name('service-bookings.show');
        Route::get('/{serviceBooking}/edit', [ServiceBookingController::class, 'edit'])->name('service-bookings.edit');
        Route::put('/{serviceBooking}', [ServiceBookingController::class, 'update'])->name('service-bookings.update');
        Route::delete('/{serviceBooking}', [ServiceBookingController::class, 'destroy'])->name('service-bookings.destroy');
        
        // Additional service booking routes
        Route::post('/{serviceBooking}/add-service', [ServiceBookingController::class, 'addService'])->name('service-bookings.add-service');
        Route::delete('/service-item/{serviceItem}', [ServiceBookingController::class, 'removeService'])->name('service-bookings.remove-service');
        Route::put('/{serviceBooking}/complete', [ServiceBookingController::class, 'complete'])->name('service-bookings.complete');
        Route::post('/assign-staff', [ServiceBookingController::class, 'assignStaff'])->name('service-bookings.assign-staff');
        
        // Reports routes
        Route::get('/reports/daily', [ServiceBookingController::class, 'dailyReport'])->name('service-bookings.reports.daily');
        Route::get('/reports/pdf', [ServiceBookingController::class, 'exportPDF'])->name('service-bookings.reports.pdf');
        Route::get('/reports/excel', [ServiceBookingController::class, 'exportExcel'])->name('service-bookings.reports.excel');
    });

    // Commission Reports Route
    Route::get('/commission-reports', [StaffController::class, 'commissionReports'])->middleware(['has.business', 'permission:staff'])->name('commission-reports');

    // Costs Routes
    Route::prefix('costs')->middleware(['has.business', 'permission:expenses'])->group(function () {
        Route::get('/', [CostController::class, 'index'])->name('costs.index');
        Route::get('/create', [CostController::class, 'create'])->name('costs.create');
        Route::post('/', [CostController::class, 'store'])->name('costs.store');
        Route::get('/{cost}', [CostController::class, 'show'])->name('costs.show');
        Route::get('/{cost}/edit', [CostController::class, 'edit'])->name('costs.edit');
        Route::put('/{cost}', [CostController::class, 'update'])->name('costs.update');
        Route::delete('/{cost}', [CostController::class, 'destroy'])->name('costs.destroy');
    });

    // Inventory Management Routes
    Route::prefix('inventory')->middleware(['has.business', 'permission:products'])->group(function () {
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
    Route::prefix('commissions')->middleware(['has.business', 'permission:staff'])->group(function () {
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
    Route::prefix('suppliers')->middleware(['auth', 'has.business', 'permission:suppliers'])->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
        Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    });

    // Employees Routes
    Route::prefix('employees')->middleware(['has.business', 'permission:staff'])->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });

    // Categories Routes  
    Route::prefix('categories')->middleware(['has.business', 'permission:products'])->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/{category}', [CategoryController::class, 'show'])->name('categories.show');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // Brands Routes
    Route::prefix('brands')->middleware(['has.business', 'permission:products'])->group(function () {
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
        // Paystack M-Pesa STK push (POS)
        Route::post('/paystack/mpesa/charge', [PaymentController::class, 'paystackMpesaCharge'])->name('payment.paystack.mpesa.charge')->middleware('has.business');
        Route::get('/paystack/status/{reference}', [PaymentController::class, 'paystackChargeStatus'])->name('payment.paystack.status')->middleware('has.business')->where('reference', '[A-Za-z0-9_\-]+');
    });

    // Billing Routes
    Route::prefix('billing')->group(function () {
        Route::get('/upgrade', [BillingController::class, 'upgrade'])->name('billing.upgrade');
        Route::post('/upgrade', [BillingController::class, 'processUpgrade'])->name('billing.process-upgrade');
        Route::get('/dashboard', [BillingController::class, 'dashboard'])->name('billing.dashboard');
        Route::post('/cancel', [BillingController::class, 'cancel'])->name('billing.cancel');
    });

    // Product Conversions (for specialized businesses like greenhouse materials)
    Route::prefix('product-conversions')->name('product-conversions.')->middleware(['has.business', 'permission:products'])->group(function () {
        Route::get('/', [ProductConversionController::class, 'index'])->name('index');
        Route::get('/create', [ProductConversionController::class, 'create'])->name('create');
        Route::post('/', [ProductConversionController::class, 'store'])->name('store');
        Route::get('/{conversion}', [ProductConversionController::class, 'show'])->name('show');
        Route::get('/{conversion}/edit', [ProductConversionController::class, 'edit'])->name('edit');
        Route::put('/{conversion}', [ProductConversionController::class, 'update'])->name('update');
        Route::delete('/{conversion}', [ProductConversionController::class, 'destroy'])->name('destroy');
        Route::post('/calculate', [ProductConversionController::class, 'calculate'])->name('calculate');
        Route::get('/product/{product}/conversions', [ProductConversionController::class, 'productConversions'])->name('product');
        
        // Dynamic conversion routes
        Route::get('/product/{product}/dynamic-calculator', [ProductConversionController::class, 'dynamicCalculator'])->name('dynamic-calculator');
        Route::post('/product/{product}/calculate-dynamic', [ProductConversionController::class, 'calculateDynamic'])->name('calculate-dynamic');
        Route::post('/product/{product}/suggested-price', [ProductConversionController::class, 'getSuggestedPrice'])->name('suggested-price');
        Route::get('/product/{product}/conversion-options', [ProductConversionController::class, 'getConversionOptions'])->name('conversion-options');
        Route::get('/product/{product}/conversions', [ProductConversionController::class, 'getProductConversions'])->name('product-conversions');
    });

    // API Routes for payment callbacks
    Route::prefix('api')->group(function () {
        Route::post('/mpesa/callback', [BillingController::class, 'mpesaCallback'])->name('api.mpesa.callback');
    });

    // AI Analysis Routes
    Route::prefix('ai')->name('ai.')->middleware(['has.business', 'permission:ai'])->group(function () {
        Route::get('/dashboard', [AIAnalysisController::class, 'dashboard'])->name('dashboard');
        Route::get('/analysis/comprehensive', [AIAnalysisController::class, 'comprehensiveAnalysis'])->name('analysis.comprehensive');
        Route::get('/analysis/specific', [AIAnalysisController::class, 'specificAnalysis'])->name('analysis.specific');
        Route::get('/marketing', [AIAnalysisController::class, 'marketingContent'])->name('marketing');
        Route::get('/recommendations', [AIAnalysisController::class, 'recommendations'])->name('recommendations');
        Route::get('/loyalty', [AIAnalysisController::class, 'loyaltyProgram'])->name('loyalty');
        Route::get('/packages', [AIAnalysisController::class, 'servicePackages'])->name('packages');
        Route::get('/pricing', [AIAnalysisController::class, 'pricingStrategy'])->name('pricing');
        Route::post('/video', [AIAnalysisController::class, 'createVideo'])->name('video.create');
        Route::get('/export', [AIAnalysisController::class, 'exportReport'])->name('export');
        Route::get('/status', [AIAnalysisController::class, 'systemStatus'])->name('status');
        Route::post('/cache/clear', [AIAnalysisController::class, 'clearCache'])->name('cache.clear');
        
        // Widgets
        Route::get('/widgets/insights', [AIAnalysisController::class, 'insightsWidget'])->name('widgets.insights');
        Route::get('/widgets/recommendations', [AIAnalysisController::class, 'recommendationsWidget'])->name('widgets.recommendations');
        Route::get('/widgets/marketing', [AIAnalysisController::class, 'marketingWidget'])->name('widgets.marketing');
    });

    // AI Content Enhancement Routes
    Route::prefix('ai-content')->name('ai-content.')->middleware(['has.business', 'permission:ai'])->group(function () {
        Route::get('/', [AIContentController::class, 'index'])->name('index');
        Route::post('/enhance', [AIContentController::class, 'enhance'])->name('enhance');
        Route::post('/generate', [AIContentController::class, 'generate'])->name('generate');
        Route::post('/seo', [AIContentController::class, 'optimizeSEO'])->name('seo');
        Route::post('/variations', [AIContentController::class, 'variations'])->name('variations');
        Route::get('/options', [AIContentController::class, 'getOptions'])->name('options');
        Route::get('/status', [AIContentController::class, 'status'])->name('status');
    });

    // AI Advice Routes
    Route::prefix('ai-advice')->name('ai-advice.')->middleware(['has.business', 'permission:ai'])->group(function () {
        Route::get('/', [AIAdviceController::class, 'index'])->name('index');
        Route::post('/ai-learning/trigger', [AIAdviceController::class, 'triggerLearning'])->name('ai-learning.trigger');
        Route::put('/ai-settings/update', [AIAdviceController::class, 'updateSettings'])->name('ai-settings.update');
        Route::post('/{adviceId}/mark-read', [AIAdviceController::class, 'markAsRead'])->name('mark-read');
        Route::get('/competitor-insights', [AIAdviceController::class, 'getCompetitorInsights'])->name('competitor-insights');
        Route::get('/trending-topics', [AIAdviceController::class, 'getTrendingTopics'])->name('trending-topics');
        Route::get('/unread-count', [AIAdviceController::class, 'getUnreadCount'])->name('unread-count');
        Route::post('/generate-performance', [AIAdviceController::class, 'generatePerformanceAdvice'])->name('generate-performance');
    });

    // Continuous Knowledge System — disabled (internal system, not a business module)

    // AI Communication System Routes
    Route::prefix('ai-communication')->name('ai-comm.')->middleware(['has.business', 'permission:ai'])->group(function () {
        Route::get('/chat', [AICommunicationController::class, 'chat'])->name('chat');
        Route::post('/process-message', [AICommunicationController::class, 'processMessage'])->name('process-message');
        Route::get('/history', [AICommunicationController::class, 'getHistory'])->name('history');
        Route::post('/clear-history', [AICommunicationController::class, 'clearHistory'])->name('clear-history');
        Route::post('/suggestions', [AICommunicationController::class, 'getSuggestions'])->name('suggestions');
        Route::get('/status', [AICommunicationController::class, 'getStatus'])->name('status');
        Route::post('/quick-insights', [AICommunicationController::class, 'getQuickInsights'])->name('quick-insights');
    });

    // Learning system routes — disabled (internal system)
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

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/testimonials', [TestimonialController::class, 'adminIndex'])->name('testimonials.index');
    Route::post('/testimonials/{testimonial}/approve', [TestimonialController::class, 'approve'])->name('testimonials.approve');
    Route::delete('/testimonials/{testimonial}', [TestimonialController::class, 'destroy'])->name('testimonials.destroy');

    Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsDashboardController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/users/{user}', [\App\Http\Controllers\Admin\AnalyticsDashboardController::class, 'user'])->name('analytics.user');
});

// Owner testimonial management routes
Route::middleware(['auth', 'has.business'])->prefix('testimonials')->name('testimonials.owner.')->group(function () {
    Route::get('/',                                [TestimonialController::class, 'ownerIndex'])->name('index');
    Route::patch('/{testimonial}/approve',         [TestimonialController::class, 'ownerApprove'])->name('approve');
    Route::patch('/{testimonial}/reject',          [TestimonialController::class, 'ownerReject'])->name('reject');
    Route::delete('/{testimonial}',                [TestimonialController::class, 'ownerDelete'])->name('delete');
    Route::patch('/{id}/restore',                  [TestimonialController::class, 'ownerRestore'])->name('restore');
    Route::delete('/{id}/force-delete',            [TestimonialController::class, 'ownerForceDelete'])->name('force-delete');
});



/*
|--------------------------------------------------------------------------
| Website Builder Routes
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\WebsiteBuilderController;
use App\Http\Controllers\WebsiteConfiguratorController;

// Website Configurator Routes (Odoo-style step-by-step setup)
Route::middleware(['auth'])->prefix('website-configurator')->name('website-configurator.')->group(function () {
    Route::get('/step1', [WebsiteConfiguratorController::class, 'step1'])->name('step1')->middleware(['has.business', 'permission:website']);
    Route::post('/step1', [WebsiteConfiguratorController::class, 'step1Submit'])->name('step1.submit')->middleware(['has.business', 'permission:website']);
    Route::get('/step2', [WebsiteConfiguratorController::class, 'step2View'])->name('step2')->middleware(['has.business', 'permission:website']);
    Route::post('/step2', [WebsiteConfiguratorController::class, 'step2'])->name('step2.submit')->middleware(['has.business', 'permission:website']);
    Route::post('/generate-website-description', [WebsiteConfiguratorController::class, 'generateWebsiteDescription'])->name('generate-website-description')->middleware(['has.business', 'permission:website']);
    Route::get('/step3', [WebsiteConfiguratorController::class, 'step3View'])->name('step3')->middleware(['has.business', 'permission:website']);
    Route::post('/step3', [WebsiteConfiguratorController::class, 'step3'])->name('step3.submit')->middleware(['has.business', 'permission:website']);
    Route::get('/step4', [WebsiteConfiguratorController::class, 'step4View'])->name('step4')->middleware(['has.business', 'permission:website']);
    Route::post('/build', [WebsiteConfiguratorController::class, 'build'])->name('build')->middleware(['has.business', 'permission:website']);
    Route::post('/process', [WebsiteConfiguratorController::class, 'process'])->name('process')->middleware(['has.business', 'permission:website']);
});

Route::middleware(['auth', 'has.business', 'permission:website'])->prefix('website-builder')->name('website.builder.')->group(function () {
    Route::get('/', [WebsiteBuilderController::class, 'index'])->name('index');
    Route::get('/setup', [WebsiteBuilderController::class, 'showSetup'])->name('setup');
    Route::post('/create', [WebsiteBuilderController::class, 'create'])->name('create');
    
    // Website settings
    Route::post('/settings', [WebsiteBuilderController::class, 'updateSettings'])->name('settings.update');
    Route::post('/customize', [WebsiteBuilderController::class, 'updateCustomization'])->name('customize');
    Route::post('/publish', [WebsiteBuilderController::class, 'publish'])->name('publish');
    Route::post('/unpublish', [WebsiteBuilderController::class, 'unpublish'])->name('unpublish');
    Route::post('/change-theme', [WebsiteBuilderController::class, 'changeTheme'])->name('theme.change');
    Route::delete('/delete', [WebsiteBuilderController::class, 'deleteWebsite'])->name('delete');
    Route::get('/preview', [WebsiteBuilderController::class, 'preview'])->name('preview');
    Route::get('/preview/page/{page}', [WebsiteBuilderController::class, 'previewPage'])->name('preview.page');
    Route::post('/preview-theme', [WebsiteBuilderController::class, 'previewTheme'])->name('preview-theme');
    Route::get('/preview-theme/{themeId}', [WebsiteBuilderController::class, 'previewThemeGet'])->name('preview-theme.get');
    
    // Page management
    Route::get('/pages/{page}/edit', [WebsiteBuilderController::class, 'editPage'])->name('pages.edit');
    Route::post('/pages', [WebsiteBuilderController::class, 'storePage'])->name('pages.store');
    Route::put('/pages/{page}', [WebsiteBuilderController::class, 'updatePage'])->name('pages.update');
    Route::delete('/pages/{page}', [WebsiteBuilderController::class, 'deletePage'])->name('pages.delete');
    Route::post('/pages/{page}/duplicate', [WebsiteBuilderController::class, 'duplicatePage'])->name('pages.duplicate');
    
    // Section management
    Route::post('/pages/{page}/sections', [WebsiteBuilderController::class, 'storeSection'])->name('sections.store');
    Route::put('/sections/{section}', [WebsiteBuilderController::class, 'updateSection'])->name('sections.update');
    Route::delete('/sections/{section}', [WebsiteBuilderController::class, 'deleteSection'])->name('sections.delete');
    Route::post('/pages/{page}/sections/reorder', [WebsiteBuilderController::class, 'reorderSections'])->name('sections.reorder');
    Route::post('/sections/{section}/move-up', [WebsiteBuilderController::class, 'moveSectionUp'])->name('sections.move-up');
    Route::post('/sections/{section}/move-down', [WebsiteBuilderController::class, 'moveSectionDown'])->name('sections.move-down');
    
    // AI-powered features
    Route::post('/ai/recommend-theme', [WebsiteBuilderController::class, 'recommendTheme'])->name('ai.recommend-theme');
    Route::post('/ai/generate-content', [WebsiteBuilderController::class, 'generateSectionContent'])->name('ai.generate-content');
    Route::post('/ai/generate-seo', [WebsiteBuilderController::class, 'generateSEO'])->name('ai.generate-seo');
    Route::get('/ai/guidance', [WebsiteBuilderController::class, 'getGuidance'])->name('ai.guidance');
    Route::post('/ai/suggest-images', [WebsiteBuilderController::class, 'suggestImages'])->name('ai.suggest-images');
    Route::post('/ai/generate-section-image', [WebsiteBuilderController::class, 'generateSectionImage'])->name('ai.generate-section-image');
    Route::post('/sections/{section}/upload-image', [WebsiteBuilderController::class, 'uploadSectionImage'])->name('sections.upload-image');
    Route::post('/upload-logo', [WebsiteBuilderController::class, 'uploadLogo'])->name('upload-logo');
    Route::post('/ai/generate-logo', [WebsiteBuilderController::class, 'generateLogo'])->name('ai.generate-logo');
    
    // Enterprise AI Feature: Auto-build complete website
    Route::post('/ai/auto-build', [WebsiteBuilderController::class, 'autoBuildWebsite'])->name('ai.auto-build');
});

// App Management Routes
Route::middleware(['auth'])->prefix('apps')->name('apps.')->group(function () {
    Route::get('/', [App\Http\Controllers\AppManagementController::class, 'index'])->name('index');
    Route::post('/toggle', [App\Http\Controllers\AppManagementController::class, 'toggle'])->name('toggle');
    Route::post('/update-order', [App\Http\Controllers\AppManagementController::class, 'updateOrder'])->name('update-order');
    Route::get('/enabled', [App\Http\Controllers\AppManagementController::class, 'getEnabledApps'])->name('enabled');
});

// PUBLIC BUSINESS SHOW ROUTE
// IMPORTANT: This MUST be at the end of the file to avoid catching specific routes like /business/profile
// It will match any /business/{slug} that wasn't matched by specific routes above
Route::get('/business/{slug}', [BusinessController::class, 'show'])->name('business.show');


