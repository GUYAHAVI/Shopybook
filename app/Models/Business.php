<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Stancl\Tenancy\Database\Concerns\GeneratesIds;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\DatabaseConfig;
use App\Models\ServiceRecord;

class Business extends BaseTenant implements TenantWithDatabase
{
    use GeneratesIds, CentralConnection;
    
    public function getIncrementing()
    {
        return false;
    }
    
    public function getKeyType()
    {
        return 'string';
    }
 

    protected $table = 'businesses'; 
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'email',
        'kra_pin',
        'phone',
        'business_type',
        'business_category',
        'description',
        'logo_path',
        'cover_path',
        'business_hours',
        'address',
        'city',
        'country',
        'plan',
        'upgraded_at',
        'cancelled_at',
        'active',
        'website_enabled',
        'website_created_at',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'linkedin_url',
        'youtube_url',
        'whatsapp_number',
    ];

    protected $casts = [
        'business_hours' => 'array',
        'upgraded_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'website_enabled' => 'boolean',
        'website_created_at' => 'datetime',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'user_id',
            'name',
            'slug',
            'email',
            'phone',
            'business_type',
            'business_category',
            'description',
            'logo_path',
            'cover_path',
            'business_hours',
            'address',
            'city',
            'country',
            'created_at',
            'updated_at',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function taxSettings()
    {
        return $this->hasOne(TaxSetting::class);
    }

    /**
     * Get or create tax settings for this business
     */
    public function getTaxSettingsOrCreate()
    {
        if (!$this->taxSettings) {
            return TaxSetting::create([
                'business_id' => $this->id,
                'tax_enabled' => false,
                'tax_rate' => 16.00, // Default Kenya VAT rate
                'tax_type' => 'VAT',
                'tax_name' => 'VAT',
            ]);
        }

        return $this->taxSettings;
    }

    public function settings()
    {
        return $this->hasOne(BusinessSettings::class);
    }

    /**
     * Get or create settings for this business
     */
    public function getSettingsOrCreate()
    {
        if (!$this->settings) {
            return BusinessSettings::create(array_merge(
                ['business_id' => $this->id],
                BusinessSettings::defaults()
            ));
        }

        return $this->settings;
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function brands()
    {
        return $this->hasMany(Brand::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }

    public function advertisingCampaigns()
    {
        return $this->hasMany(AdvertisingCampaign::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Order::class);
    }

    public function organizationCustomers()
    {
        return $this->hasMany(OrganizationCustomer::class);
    }

    /**
     * Services-related relationships
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function serviceRecords()
    {
        return $this->hasMany(ServiceRecord::class);
    }

    public function costs()
    {
        return $this->hasMany(Cost::class);
    }

    public function commissionPayouts()
    {
        return $this->hasMany(CommissionPayout::class);
    }

    public function socialMediaAccounts()
    {
        return $this->hasMany(SocialMediaAccount::class);
    }

    public function marketingPosts()
    {
        return $this->hasMany(MarketingPost::class);
    }

    /**
     * Website builder relationship
     */
    public function website()
    {
        return $this->hasOne(Website::class);
    }

    /**
     * Check if business has premium subscription
     */
    public function isPremium(): bool
    {
        return in_array($this->plan ?? 'free', ['premium', 'basic']);
    }

    /**
     * Check if business has enterprise subscription
     */
    public function isEnterprise(): bool
    {
        return $this->plan === 'enterprise';
    }

    /**
     * Check if this business is eligible for Dynamic Conversion System
     */
    public function isEligibleForDynamicConversions(): bool
    {
        // Check if this is Havi's Greenhouse Materials business
        return $this->name === "Havi's Greenhouse Materials" || 
               $this->email === 'harveyelvis23@gmail.com';
    }

    /**
     * Calculate total salary costs for the business
     */
    public function getTotalSalaryCostsAttribute()
    {
        // Get automatic staff salaries
        $staffSalaries = $this->staff()->sum('salary') ?? 0;
        
        // Get manual salary costs recorded in the costs table
        $manualSalaryCosts = $this->costs()
            ->where('type', 'salary')
            ->sum('amount') ?? 0;
            
        return $staffSalaries + $manualSalaryCosts;
    }

    /**
     * Calculate total salary costs for a specific month
     */
    public function getSalaryCostsForMonth($year, $month)
    {
        // Get automatic staff salaries (monthly)
        $staffSalaries = $this->staff()->sum('salary') ?? 0;
        
        // Get manual salary costs for the specific month
        $manualSalaryCosts = $this->costs()
            ->where('type', 'salary')
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('amount') ?? 0;
            
        return $staffSalaries + $manualSalaryCosts;
    }

    /**
     * Calculate total inventory purchase costs (stock receipts)
     */
    public function getTotalInventoryCostsAttribute()
    {
        return \App\Models\StockReceipt::where('business_id', $this->id)
            ->sum('total_cost') ?? 0;
    }

    /**
     * Calculate inventory costs for a specific date range
     */
    public function getInventoryCostsForDateRange($startDate, $endDate)
    {
        return \App\Models\StockReceipt::where('business_id', $this->id)
            ->whereBetween('receipt_date', [$startDate, $endDate])
            ->sum('total_cost') ?? 0;
    }

    /**
     * Calculate inventory costs for a specific month
     */
    public function getInventoryCostsForMonth($year, $month)
    {
        return \App\Models\StockReceipt::where('business_id', $this->id)
            ->whereYear('receipt_date', $year)
            ->whereMonth('receipt_date', $month)
            ->sum('total_cost') ?? 0;
    }

    /**
     * Calculate inventory costs for today
     */
    public function getTodayInventoryCosts()
    {
        return \App\Models\StockReceipt::where('business_id', $this->id)
            ->whereDate('receipt_date', today())
            ->sum('total_cost') ?? 0;
    }

    /**
     * Get total returns/refunds amount
     */
    public function getTotalReturnsAttribute()
    {
        return \App\Models\OrderReturn::where('business_id', $this->id)
            ->where('status', 'completed')
            ->sum('refund_amount') ?? 0;
    }

    /**
     * Get today's returns/refunds
     */
    public function getTodayReturns()
    {
        return \App\Models\OrderReturn::where('business_id', $this->id)
            ->where('status', 'completed')
            ->whereDate('refund_processed_at', today())
            ->sum('refund_amount') ?? 0;
    }

    /**
     * Get returns for a specific month
     */
    public function getReturnsForMonth($month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        return \App\Models\OrderReturn::where('business_id', $this->id)
            ->where('status', 'completed')
            ->whereMonth('refund_processed_at', $month)
            ->whereYear('refund_processed_at', $year)
            ->sum('refund_amount') ?? 0;
    }

    /**
     * Get returns for date range
     */
    public function getReturnsForDateRange($startDate, $endDate)
    {
        return \App\Models\OrderReturn::where('business_id', $this->id)
            ->where('status', 'completed')
            ->whereBetween('refund_processed_at', [$startDate, $endDate])
            ->sum('refund_amount') ?? 0;
    }

    /**
     * Normalize phone number for comparison
     */
    private function normalizePhoneNumber(?string $phone): string
    {
        if (!$phone) return '';
        
        // Remove all non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Handle Kenyan numbers
        if (strlen($phone) === 9 && substr($phone, 0, 1) === '7') {
            $phone = '254' . $phone;
        }
        
        if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            $phone = '254' . substr($phone, 1);
        }
        
        return $phone;
    }

    /**
     * Get the database configuration for the tenant.
     */
    public function database(): DatabaseConfig
    {
        return tenancy()->database();
    }

    /**
     * Generate a unique database name for the tenant.
     */
    protected function databaseName(): string
    {
        return 'tenant_' . $this->id;
    }
    
    /**
     * Get the business category (product, service, hybrid)
     */
    public function getBusinessCategoryAttribute()
    {
        return $this->getRawOriginal('business_category') ?? 'product';
    }
    
    /**
     * Check if business is product-based
     */
    public function isProductBusiness()
    {
        return $this->business_category === 'product';
    }
    
    /**
     * Check if business is service-based
     */
    public function isServiceBusiness()
    {
        return $this->business_category === 'service';
    }
    
    /**
     * Check if business is hybrid
     */
    public function isHybridBusiness()
    {
        return $this->business_category === 'hybrid';
    }
}