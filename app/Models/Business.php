<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Stancl\Tenancy\Database\Concerns\GeneratesIds;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\DatabaseConfig;

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
        'phone',
        'business_type',
        'description',
        'logo_path',
        'cover_path',
        'business_hours',
        'address',
        'city',
        'country',
    ];

    protected $casts = [
        'business_hours' => 'array',
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

    /**
     * Get the database configuration for the tenant.
     */
    public function database(): DatabaseConfig
    {
        return DatabaseConfig::fromArray([
            'driver' => 'mysql',
            'host' => env('TENANT_DB_HOST', '127.0.0.1'),
            'port' => env('TENANT_DB_PORT', '3306'),
            'database' => $this->databaseName(),
            'username' => env('TENANT_DB_USERNAME', 'root'),
            'password' => env('TENANT_DB_PASSWORD', ''),
            'prefix' => '',
        ]);
    }

    /**
     * Generate a unique database name for the tenant.
     */
    protected function databaseName(): string
    {
        return 'tenant_' . $this->id;
    }
    
}