<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use App\Models\GoldPiece;
use App\Models\OrderSale;
use App\Models\OrderRental;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class VendorOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the vendor user
        $vendor = User::where('email', 'vendor@gold-station.com')->first();
        
        if (!$vendor) {
            $this->command->error('Vendor user with email vendor@gold-station.com not found!');
            return;
        }

        // Get vendor's branches
        $branches = Branch::where('vendor_id', $vendor->id)->get();
        
        if ($branches->isEmpty()) {
            $this->command->error('No branches found for vendor!');
            return;
        }

        // Get some random gold pieces to use in orders
        $goldPieces = GoldPiece::limit(20)->get();
        
        if ($goldPieces->isEmpty()) {
            $this->command->error('No gold pieces found! Please run GoldPieceSeeder first.');
            return;
        }

        // Get some random users (customers)
        $customers = User::where('id', '!=', $vendor->id)->limit(10)->get();
        
        if ($customers->isEmpty()) {
            $this->command->error('No customer users found!');
            return;
        }

        $this->command->info("Creating orders for vendor: {$vendor->name} ({$vendor->email})");
        $this->command->info("Vendor has {$branches->count()} branch(es)");

        // Create OrderRental records
        $this->createRentalOrders($customers, $goldPieces, $branches);
        
        // Create OrderSale records
        $this->createSaleOrders($customers, $goldPieces, $branches);

        $this->command->info('Vendor orders seeded successfully!');
    }

    /**
     * Create rental orders for the vendor
     */
    private function createRentalOrders($customers, $goldPieces, $branches): void
    {
        $this->command->info('Creating rental orders...');

        // Create 18 rental orders with different types and statuses (increased to accommodate rejected status)
        for ($i = 0; $i < 18; $i++) {
            $customer = $customers->random();
            $goldPiece = $goldPieces->random();
            $branch = $branches->random();

            // Mix of rent type (user renting out) and lease type (user renting from store)
            $type = $i % 2 == 0 ? OrderRental::RENT_TYPE : OrderRental::LEASE_TYPE;
            
            // Distribute statuses realistically
            $status = match(true) {
                $i < 4 => OrderRental::STATUS_PENDING_APPROVAL,
                $i < 7 => OrderRental::STATUS_APPROVED,
                $i < 9 => OrderRental::STATUS_PIECE_SENT,
                $i < 13 => OrderRental::STATUS_RENTED,
                $i < 15 => OrderRental::STATUS_AVAILABLE,
                $i < 17 => OrderRental::STATUS_SOLD,
                default => OrderRental::STATUS_REJECTED
            };

            $startDate = now()->subDays(rand(1, 30));
            $endDate = $startDate->copy()->addDays(rand(7, 60));

            OrderRental::create([
                'user_id' => $customer->id,
                'gold_piece_id' => $goldPiece->id,
                'branch_id' => $branch->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_price' => round(rand(100, 2000) + (rand(0, 99) / 100), 2),
                'status' => $status,
                'type' => $type,
                'is_suspended' => rand(0, 100) < 10, // 10% chance of suspension
            ]);
        }

        $this->command->info('Created 18 rental orders');
    }

    /**
     * Create sale orders for the vendor
     */
    private function createSaleOrders($customers, $goldPieces, $branches): void
    {
        $this->command->info('Creating sale orders...');

        // Create 15 sale orders with different statuses (increased from 12 to accommodate rejected status)
        for ($i = 0; $i < 15; $i++) {
            $customer = $customers->random();
            $goldPiece = $goldPieces->random();
            $branch = $branches->random();

            // Distribute statuses realistically
            $status = match(true) {
                $i < 4 => OrderSale::STATUS_PENDING_APPROVAL,
                $i < 7 => OrderSale::STATUS_APPROVED,
                $i < 10 => OrderSale::STATUS_SOLD,
                default => OrderSale::STATUS_REJECTED
            };

            OrderSale::create([
                'user_id' => $customer->id,
                'gold_piece_id' => $goldPiece->id,
                'branch_id' => $branch->id,
                'total_price' => round(rand(200, 1500) + (rand(0, 99) / 100), 2),
                'status' => $status,
                'is_suspended' => rand(0, 100) < 5, // 5% chance of suspension
            ]);
        }

        $this->command->info('Created 15 sale orders');
    }
} 