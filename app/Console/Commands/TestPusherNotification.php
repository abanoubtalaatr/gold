<?php

namespace App\Console\Commands;

use App\Models\OrderRental;
use App\Services\RealTimeNotificationService;
use App\Events\RentalStatusUpdatedEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Broadcast;

class TestPusherNotification extends Command
{
    protected $signature = 'pusher:test 
                           {--order-id= : Specific order ID to test with}
                           {--user-id= : Specific user ID to test with}
                           {--detailed : Show detailed debug information}';

    protected $description = 'Test Pusher broadcasting with real rental notification';

    protected $realTimeNotificationService;

    public function __construct(RealTimeNotificationService $realTimeNotificationService)
    {
        parent::__construct();
        $this->realTimeNotificationService = $realTimeNotificationService;
    }

    public function handle()
    {
        $this->info('🧪 Testing Pusher Broadcasting...');
        
        // Check Pusher configuration
        $this->checkPusherConfig();
        
        // Get test order
        $orderId = $this->option('order-id');
        $userId = $this->option('user-id');
        
        if ($orderId) {
            $order = OrderRental::find($orderId);
        } else {
            $order = OrderRental::with(['user', 'goldPiece', 'branch'])->first();
        }
        
        if (!$order) {
            $this->error('❌ No rental order found for testing');
            return Command::FAILURE;
        }
        
        $this->info("📦 Testing with Order #{$order->id}");
        $this->info("   Gold Piece: {$order->goldPiece->name}");
        $this->info("   Current Status: {$order->status}");
        $this->info("   User: {$order->user->name} (ID: {$order->user->id})");
        
        if ($order->branch) {
            $this->info("   Branch: {$order->branch->name}");
        }
        
        $this->line('');
        
        // Test 1: Direct broadcast event
        $this->testDirectBroadcast($order);
        
        // Test 2: Notification service
        $this->testNotificationService($order);
        
        // Test 3: Complete workflow
        $this->testCompleteWorkflow($order);
        
        $this->line('');
        $this->info('📊 Now check your Pusher Dashboard at: https://dashboard.pusher.com');
        $this->info('   Look for activity in the last few minutes');
        
        return Command::SUCCESS;
    }
    
    private function checkPusherConfig()
    {
        $this->info('🔧 Checking Pusher Configuration...');
        
        $driver = config('broadcasting.default');
        $pusherConfig = config('broadcasting.connections.pusher');
        
        $this->line("   Broadcast Driver: {$driver}");
        $this->line("   App ID: {$pusherConfig['app_id']}");
        $this->line("   Key: {$pusherConfig['key']}");
        $this->line("   Cluster: {$pusherConfig['options']['cluster']}");
        $this->line("   Encrypted: " . ($pusherConfig['options']['encrypted'] ? 'Yes' : 'No'));
        
        if ($driver !== 'pusher') {
            $this->warn("⚠️  Broadcast driver is not set to 'pusher'");
        }
        
        $this->line('');
    }
    
    private function testDirectBroadcast($order)
    {
        $this->info('🎯 Test 1: Direct Broadcast Event...');
        
        try {
            $event = new RentalStatusUpdatedEvent($order, $order->status, 'test_status', null);
            broadcast($event)->toOthers();
            
            $this->info('   ✅ Direct broadcast sent successfully');
            
            if ($this->option('detailed')) {
                $this->line("   📡 Channels broadcasted to:");
                $this->line("      • notifications.{$order->user->id}");
                if ($order->branch_id) {
                    $this->line("      • branch.{$order->branch_id}");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Direct broadcast failed: {$e->getMessage()}");
            
            if ($this->option('detailed')) {
                $this->line("   Stack trace: {$e->getTraceAsString()}");
            }
        }
        
        $this->line('');
    }
    
    private function testNotificationService($order)
    {
        $this->info('🔔 Test 2: Real-Time Notification Service...');
        
        try {
            // Send test critical notification
            $this->realTimeNotificationService->sendCriticalNotification(
                $order,
                $order->status,
                'service_test_status',
                null
            );
            
            $this->info('   ✅ Notification service test successful');
            
            // Test sound notification
            $this->realTimeNotificationService->sendSoundNotification(
                $order,
                $order->status,
                'sound_test_status',
                null
            );
            
            $this->info('   ✅ Sound notification test successful');
            
        } catch (\Exception $e) {
            $this->error("   ❌ Notification service failed: {$e->getMessage()}");
            
            if ($this->option('detailed')) {
                $this->line("   Stack trace: {$e->getTraceAsString()}");
            }
        }
        
        $this->line('');
    }
    
    private function testCompleteWorkflow($order)
    {
        $this->info('⚙️  Test 3: Complete Workflow Test...');
        
        try {
            // Test different notification types
            $tests = [
                ['type' => 'critical', 'status' => 'approved'],
                ['type' => 'sound', 'status' => 'piece_sent'],
                ['type' => 'standard', 'status' => 'rented'],
            ];
            
            foreach ($tests as $test) {
                switch ($test['type']) {
                    case 'critical':
                        $this->realTimeNotificationService->sendCriticalNotification(
                            $order, $order->status, $test['status'], null
                        );
                        break;
                    case 'sound':
                        $this->realTimeNotificationService->sendSoundNotification(
                            $order, $order->status, $test['status'], null
                        );
                        break;
                    case 'standard':
                        $this->realTimeNotificationService->sendRentalStatusUpdate(
                            $order, $order->status, $test['status'], null
                        );
                        break;
                }
                
                $this->info("   ✅ {$test['type']} notification ({$test['status']}) sent");
                
                // Small delay between tests
                usleep(500000); // 0.5 seconds
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Workflow test failed: {$e->getMessage()}");
        }
        
        // Log the test completion
        Log::info('Pusher test completed', [
            'order_id' => $order->id,
            'user_id' => $order->user->id,
            'timestamp' => now()->toISOString(),
            'test_type' => 'complete_workflow',
        ]);
        
        $this->line('');
    }
} 