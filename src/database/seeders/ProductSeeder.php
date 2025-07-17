<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'name' => 'Laptop',
                'description' => 'High-performance laptop',
                'price' => 999.99,
                'quantity' => 50,
                'sku' => 'LAP-001',
                'is_active' => true,
            ],
            [
                'name' => 'Mouse',
                'description' => 'Wireless mouse',
                'price' => 29.99,
                'quantity' => 200,
                'sku' => 'MOU-001',
                'is_active' => true,
            ],
            // Add more products as needed
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
