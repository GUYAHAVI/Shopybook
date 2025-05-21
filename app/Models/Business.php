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
      use GeneratesIds; // This will handle UUID generation
    
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