<?php

namespace App\Console\Commands;

use App\Models\OrderRental;
use Illuminate\Console\Command;

class RandomizeRentalStatuses extends Command
{
    protected $signature = 'rentals:randomize-statuses {--show-changes : Show status changes}';
    protected $description = 'Randomize rental statuses for testing purposes';

    public function handle()
    {
        $statuses = [
            OrderRental::STATUS_PENDING_APPROVAL,
            OrderRental::STATUS_APPROVED,
            OrderRental::STATUS_REJECTED,
            OrderRental::STATUS_PIECE_SENT,
            OrderRental::STATUS_RENTED,
            OrderRental::STATUS_AVAILABLE,
        ];

        $orders = OrderRental::all();
        $showChanges = $this->option('show-changes');

        $this->info("🎲 Randomizing statuses for {$orders->count()} rental orders...");

        foreach ($orders as $order) {
            $oldStatus = $order->status;
            $newStatus = $statuses[array_rand($statuses)];
            
            $order->update(['status' => $newStatus]);

            if ($showChanges) {
                $this->line("📦 Order #{$order->id}: {$oldStatus} → {$newStatus}");
            }
        }

        $this->info("✅ All rental statuses have been randomized!");
        $this->info("💡 Now you can test status transitions and notifications.");
        
        // Show distribution
        $this->newLine();
        $this->info("📊 Current status distribution:");
        foreach ($statuses as $status) {
            $count = OrderRental::where('status', $status)->count();
            $this->line("   {$status}: {$count} orders");
        }

        return Command::SUCCESS;
    }
} 