<?php

namespace App\Console\Commands;

use App\Events\RentalStatusUpdatedEvent;
use App\Models\OrderRental;
use Illuminate\Console\Command;

class TestEventChannels extends Command
{
    protected $signature = 'test:event-channels';
    protected $description = 'Test what channels our rental event broadcasts to';

    public function handle()
    {
        $order = OrderRental::with(['user', 'branch'])->first();
        
        if (!$order) {
            $this->error('No rental orders found!');
            return;
        }

        $this->info("🧪 Testing RentalStatusUpdatedEvent channels...");
        $this->line("Order ID: {$order->id}");
        $this->line("User ID: {$order->user_id}");
        $this->line("Branch ID: {$order->branch_id}");

        // Create the event
        $event = new RentalStatusUpdatedEvent($order, 'old_status', 'new_status', null);
        
        // Get the channels
        $channels = $event->broadcastOn();
        
        $this->info("📡 Broadcast channels:");
        foreach ($channels as $channel) {
            $this->line("   - Channel: {$channel->name}");
        }
        
        // Test broadcast
        $this->info("🚀 Testing actual broadcast...");
        broadcast($event)->toOthers();
        $this->info("✅ Broadcast sent!");
        
        return Command::SUCCESS;
    }
} 