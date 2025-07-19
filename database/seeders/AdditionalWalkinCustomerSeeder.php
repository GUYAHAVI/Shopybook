<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business;
use App\Models\Customer;

class AdditionalWalkinCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $businesses = Business::all();
        foreach ($businesses as $business) {
            Customer::firstOrCreate([
                'business_id' => $business->id,
                'name' => 'Walk-in Customer',
            ], [
                'phone' => 'N/A',
                'email' => null,
                'address' => null,
                'city' => null,
                'country' => 'Kenya',
            ]);
        }
    }
} 