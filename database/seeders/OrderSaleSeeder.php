<?php

namespace Database\Seeders;

use App\Models\OrderRental;
use App\Models\OrderSale;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrderSale::factory()->count(10)->create();
    }
}
