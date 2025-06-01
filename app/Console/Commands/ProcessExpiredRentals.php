<?php

namespace App\Console\Commands;

use App\Models\OrderRental;
use App\Services\RentalWorkflowService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessExpiredRentals extends Command
{
    protected $signature = 'rentals:process-expired 
                           {--dry-run : Show what would be processed without making changes}
                           {--show-details : Show detailed information about each rental}';

    protected $description = 'Process expired rentals and mark them as available with real-time notifications';

    protected $rentalWorkflowService;

    public function __construct(RentalWorkflowService $rentalWorkflowService)
    {
        parent::__construct();
        $this->rentalWorkflowService = $rentalWorkflowService;
    }

    public function handle()
    {
        $this->info('🔄 Processing expired rentals with real-time notifications...');
        
        $isDryRun = $this->option('dry-run');
        $isVerbose = $this->option('show-details');
        
        // Find expired rentals
        $expiredRentals = OrderRental::where('status', OrderRental::STATUS_RENTED)
            ->where('end_date', '<', Carbon::now())
            ->with(['user', 'goldPiece', 'branch'])
            ->get();

        if ($expiredRentals->isEmpty()) {
            $this->info('✅ No expired rentals found.');
            return Command::SUCCESS;
        }

        $this->info("📋 Found {$expiredRentals->count()} expired rental(s)");
        
        if ($isDryRun) {
            $this->warn('🧪 DRY RUN MODE - No changes will be made');
        }

        $processed = 0;
        $failed = 0;

        foreach ($expiredRentals as $rental) {
            try {
                $daysOverdue = Carbon::parse($rental->end_date)->diffInDays(Carbon::now());
                
                if ($isVerbose || $isDryRun) {
                    $this->line("📦 Order #{$rental->id} - {$rental->goldPiece->name} - {$daysOverdue} day(s) overdue");
                }

                if (!$isDryRun) {
                    // Process the rental expiration with enhanced notifications
                    $this->rentalWorkflowService->completeRental($rental);
                    
                    if ($isVerbose) {
                        $this->info("  ✅ Marked as available with real-time notification sent");
                    }
                }
                
                $processed++;

            } catch (\Exception $e) {
                $failed++;
                $this->error("  ❌ Failed to process rental #{$rental->id}: {$e->getMessage()}");
                
                Log::error('Failed to process expired rental', [
                    'rental_id' => $rental->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        // Summary
        if (!$isDryRun) {
            $this->info("✅ Processing completed!");
            $this->info("  📊 Processed: {$processed}");
            if ($failed > 0) {
                $this->warn("  ⚠️  Failed: {$failed}");
            }
            
            Log::info('Expired rentals processing completed', [
                'processed' => $processed,
                'failed' => $failed,
                'total_found' => $expiredRentals->count(),
            ]);
        } else {
            $this->info("📝 Would process {$processed} rental(s) if not in dry-run mode");
        }

        return Command::SUCCESS;
    }
} 