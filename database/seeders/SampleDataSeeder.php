<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Business;
use App\Models\Product;
use App\Models\Service;
use App\Models\Order;
use App\Models\Customer;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting to seed sample data for AI testing...');

        // Get all businesses
        $businesses = Business::all();

        if ($businesses->isEmpty()) {
            $this->command->error('No businesses found. Please create businesses first.');
            return;
        }

        foreach ($businesses as $business) {
            $this->command->info("Seeding data for business: {$business->name}");

            // Seed products for retail/electronics businesses
            if (in_array($business->business_type, ['retail', 'hybrid', 'electronics'])) {
                $this->seedProducts($business);
                $this->seedSales($business);
            }

            // Seed services for service businesses
            if (in_array($business->business_type, ['service', 'hybrid', 'other_service', 'salon', 'beauty_service'])) {
                $this->seedServices($business);
            }

            // Seed customers for all businesses
            $this->seedCustomers($business);
        }

        $this->command->info('Sample data seeding completed successfully!');
    }

    /**
     * Seed products for retail businesses
     */
    private function seedProducts(Business $business)
    {
        $products = [
            [
                'name' => 'Premium Coffee Beans',
                'description' => 'High-quality Arabica coffee beans',
                'price' => 25.99,
                'stock_quantity' => 50,
                'category' => 'Beverages',
                'sku' => 'COF-001'
            ],
            [
                'name' => 'Organic Tea Selection',
                'description' => 'Assorted organic herbal teas',
                'price' => 18.50,
                'stock_quantity' => 30,
                'category' => 'Beverages',
                'sku' => 'TEA-002'
            ],
            [
                'name' => 'Artisan Bread',
                'description' => 'Freshly baked sourdough bread',
                'price' => 8.99,
                'stock_quantity' => 20,
                'category' => 'Bakery',
                'sku' => 'BRD-003'
            ],
            [
                'name' => 'Gourmet Cheese Platter',
                'description' => 'Assorted premium cheeses',
                'price' => 32.99,
                'stock_quantity' => 15,
                'category' => 'Dairy',
                'sku' => 'CHS-004'
            ],
            [
                'name' => 'Fresh Vegetables Bundle',
                'description' => 'Seasonal organic vegetables',
                'price' => 15.99,
                'stock_quantity' => 25,
                'category' => 'Produce',
                'sku' => 'VEG-005'
            ]
        ];

        foreach ($products as $productData) {
            Product::updateOrCreate(
                [
                    'business_id' => $business->id,
                    'name' => $productData['name']
                ],
                array_merge($productData, [
                    'business_id' => $business->id,
                    'is_active' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ])
            );
        }

        $this->command->info("Created " . count($products) . " products for {$business->name}");
    }

    /**
     * Seed orders data
     */
    private function seedSales(Business $business)
    {
        $products = Product::where('business_id', $business->id)->get();
        
        if ($products->isEmpty()) {
            $this->command->warn("No products found for {$business->name}, skipping orders seeding");
            return;
        }

        // Create customers if they don't exist
        $customers = $this->seedCustomers($business);

        // Generate orders for the last 30 days
        for ($i = 0; $i < 50; $i++) {
            $product = $products->random();
            $customer = $customers->random();
            $quantity = rand(1, 3);
            $unitPrice = $product->price;
            $totalAmount = $quantity * $unitPrice;
            $orderDate = Carbon::now()->subDays(rand(0, 30))->addMinutes(rand(0, 1440)); // Add random minutes to make each order unique

            Order::create([
                'business_id' => $business->id,
                'customer_id' => $customer->id,
                'order_number' => 'ORD-' . strtoupper(substr($business->id, 0, 8)) . '-' . time() . '-' . ($i + 1),
                'total_amount' => $totalAmount,
                'status' => 'completed',
                'payment_method' => ['cash', 'card', 'mobile_money'][rand(0, 2)],
                'created_at' => $orderDate,
                'updated_at' => $orderDate
            ]);
        }

        $this->command->info("Created 50 orders for {$business->name}");
    }

    /**
     * Seed services for service businesses
     */
    private function seedServices(Business $business)
    {
        $services = [
            [
                'name' => 'Haircut & Styling',
                'description' => 'Professional haircut and styling service',
                'price' => 45.00,
                'duration' => 45,
                'commission_rate' => 20.0
            ],
            [
                'name' => 'Beard Trim',
                'description' => 'Professional beard trimming and shaping',
                'price' => 25.00,
                'duration' => 20,
                'commission_rate' => 15.0
            ],
            [
                'name' => 'Hair Coloring',
                'description' => 'Professional hair coloring service',
                'price' => 80.00,
                'duration' => 90,
                'commission_rate' => 25.0
            ],
            [
                'name' => 'Facial Treatment',
                'description' => 'Relaxing facial treatment',
                'price' => 60.00,
                'duration' => 60,
                'commission_rate' => 18.0
            ],
            [
                'name' => 'Manicure',
                'description' => 'Professional nail care and polish',
                'price' => 35.00,
                'duration' => 30,
                'commission_rate' => 15.0
            ],
            [
                'name' => 'Pedicure',
                'description' => 'Professional foot care and polish',
                'price' => 45.00,
                'duration' => 45,
                'commission_rate' => 15.0
            ],
            [
                'name' => 'Massage Therapy',
                'description' => 'Relaxing full body massage',
                'price' => 75.00,
                'duration' => 60,
                'commission_rate' => 20.0
            ],
            [
                'name' => 'Consultation',
                'description' => 'Professional consultation and advice',
                'price' => 30.00,
                'duration' => 30,
                'commission_rate' => 10.0
            ]
        ];

        foreach ($services as $serviceData) {
            Service::updateOrCreate(
                [
                    'business_id' => $business->id,
                    'name' => $serviceData['name']
                ],
                array_merge($serviceData, [
                    'business_id' => $business->id,
                    'is_active' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ])
            );
        }

        $this->command->info("Created " . count($services) . " services for {$business->name}");
    }

    /**
     * Seed customers
     */
    private function seedCustomers(Business $business)
    {
        $customers = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@email.com',
                'phone' => '+254700123456',
                'address' => '123 Main Street, Nairobi'
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@email.com',
                'phone' => '+254700123457',
                'address' => '456 Oak Avenue, Mombasa'
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@email.com',
                'phone' => '+254700123458',
                'address' => '789 Pine Road, Kisumu'
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@email.com',
                'phone' => '+254700123459',
                'address' => '321 Elm Street, Nakuru'
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david.wilson@email.com',
                'phone' => '+254700123460',
                'address' => '654 Maple Drive, Eldoret'
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa.anderson@email.com',
                'phone' => '+254700123461',
                'address' => '987 Cedar Lane, Thika'
            ],
            [
                'name' => 'Robert Taylor',
                'email' => 'robert.taylor@email.com',
                'phone' => '+254700123462',
                'address' => '147 Birch Court, Nyeri'
            ],
            [
                'name' => 'Jennifer Martinez',
                'email' => 'jennifer.martinez@email.com',
                'phone' => '+254700123463',
                'address' => '258 Spruce Way, Kericho'
            ]
        ];

        $createdCustomers = collect();

        foreach ($customers as $customerData) {
            $customer = Customer::updateOrCreate(
                [
                    'business_id' => $business->id,
                    'email' => $customerData['email']
                ],
                array_merge($customerData, [
                    'business_id' => $business->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ])
            );

            $createdCustomers->push($customer);
        }

        $this->command->info("Created " . count($customers) . " customers for {$business->name}");

        return $createdCustomers;
    }
}
