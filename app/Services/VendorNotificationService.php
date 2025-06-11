<?php

namespace App\Services;

use App\Events\NewOrderForVendorEvent;
use App\Notifications\Vendor\NewOrderNotification;
use App\Models\OrderRental;
use App\Models\OrderSale;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class VendorNotificationService
{
    /**
     * Send new order notification to vendor with real-time broadcasting
     */
    public function notifyVendorOfNewOrder($order, string $orderType = 'rental'): void
    {
        try {
            $order->load(['user', 'goldPiece', 'branch.vendor']);
            
            if (!$order->branch || !$order->branch->vendor) {
                Log::warning('Cannot notify vendor: branch or vendor not found', [
                    'order_id' => $order->id,
                    'order_type' => $orderType,
                ]);
                return;
            }

            $vendor = $order->branch->vendor;
            $branch = $order->branch;

            // Check if this vendor has multiple orders for the same gold piece
            $goldPieceId = $order->goldPiece->id;
            $vendorId = $vendor->id;
            
            // Count how many branches this vendor has orders for this gold piece
            $branchCount = $this->getVendorBranchCountForGoldPiece($vendorId, $goldPieceId, $orderType);

            // 1. Send database notification with email
            $vendor->notify(new NewOrderNotification($order, $branch, $orderType, $branchCount));

            // 2. Broadcast real-time event
            // broadcast(new NewOrderForVendorEvent($order, $vendor->id, $orderType))->toOthers();

            // 3. Mark vendor as having new notifications
            $this->markVendorHasNewNotifications($vendor->id);

            Log::info('Vendor notification sent successfully', [
                'vendor_id' => $vendor->id,
                'vendor_name' => $vendor->name,
                'order_id' => $order->id,
                'order_type' => $orderType,
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
                'branch_count' => $branchCount,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send vendor notification', [
                'order_id' => $order->id,
                'order_type' => $orderType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Get the count of branches for a vendor that have orders for a specific gold piece
     */
    private function getVendorBranchCountForGoldPiece(int $vendorId, int $goldPieceId, string $orderType): int
    {
        try {
            if ($orderType === 'rental') {
                return OrderRental::whereHas('branch', function ($query) use ($vendorId) {
                    $query->where('vendor_id', $vendorId);
                })
                ->where('gold_piece_id', $goldPieceId)
                ->distinct('branch_id')
                ->count();
            } else {
                return OrderSale::whereHas('branch', function ($query) use ($vendorId) {
                    $query->where('vendor_id', $vendorId);
                })
                ->where('gold_piece_id', $goldPieceId)
                ->distinct('branch_id')
                ->count();
            }
        } catch (\Exception $e) {
            Log::error('Failed to get vendor branch count', [
                'vendor_id' => $vendorId,
                'gold_piece_id' => $goldPieceId,
                'order_type' => $orderType,
                'error' => $e->getMessage(),
            ]);
            return 1; // Default to 1 if we can't determine the count
        }
    }

    /**
     * Notify vendors of new gold piece available for rental/sale
     */
    public function notifyVendorsOfNewGoldPiece($goldPiece, $branches): void
    {
        try {
            $goldPiece->load('user');
            
            // Get unique vendors from branches
            $vendors = collect($branches)
                ->pluck('vendor')
                ->unique('id')
                ->filter();

            foreach ($vendors as $vendor) {
                // Create a fake order-like structure for the notification
                $vendorBranches = $branches->where('vendor_id', $vendor->id);
                
                foreach ($vendorBranches as $branch) {
                    // Send notification about new gold piece availability
                    $this->sendGoldPieceAvailabilityNotification($vendor, $goldPiece, $branch);
                }
            }

            Log::info('Gold piece availability notifications sent', [
                'gold_piece_id' => $goldPiece->id,
                'vendors_count' => $vendors->count(),
                'branches_count' => $branches->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send gold piece availability notifications', [
                'gold_piece_id' => $goldPiece->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send notification about new gold piece availability
     */
    private function sendGoldPieceAvailabilityNotification($vendor, $goldPiece, $branch): void
    {
        try {
            $typeAr = $goldPiece->type === 'for_rent' ? 'للإيجار' : 'للبيع';
            $typeEn = $goldPiece->type === 'for_rent' ? 'for rent' : 'for sale';

            // Store in database
            $vendor->notifications()->create([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\\Notifications\\Vendor\\NewGoldPieceAvailableNotification',
                'data' => [
                    'title' => [
                        'ar' => 'قطعة ذهب جديدة متاحة',
                        'en' => 'New Gold Piece Available'
                    ],
                    'message' => [
                        'ar' => "قطعة ذهب جديدة {$typeAr}: {$goldPiece->name} - الوزن: {$goldPiece->weight} جرام في فرع {$branch->name}",
                        'en' => "New gold piece {$typeEn}: {$goldPiece->name} - Weight: {$goldPiece->weight} grams for {$branch->name} branch"
                    ],
                    'type' => 'new_gold_piece_available',
                    'priority' => 'medium',
                    'sound_enabled' => true,
                    'data' => [
                        'gold_piece_id' => $goldPiece->id,
                        'branch_id' => $branch->id,
                        'action_url' => route('vendor.gold-pieces.index'),
                        'timestamp' => now()->toISOString(),
                    ]
                ],
                'read_at' => null,
            ]);

            // Broadcast real-time notification
            broadcast(new \App\Events\NewGoldPieceEvent($goldPiece, $branch->id))->toOthers();

            $this->markVendorHasNewNotifications($vendor->id);

        } catch (\Exception $e) {
            Log::error('Failed to send gold piece availability notification', [
                'vendor_id' => $vendor->id,
                'gold_piece_id' => $goldPiece->id,
                'branch_id' => $branch->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Mark vendor as having new notifications for real-time UI updates
     */
    private function markVendorHasNewNotifications(int $vendorId): void
    {
        Cache::put("vendor_new_notifications_{$vendorId}", true, 3600); // 1 hour
        Cache::increment("vendor_notification_count_{$vendorId}");
    }

    /**
     * Check if vendor has new notifications
     */
    public function vendorHasNewNotifications(int $vendorId): bool
    {
        return Cache::has("vendor_new_notifications_{$vendorId}");
    }

    /**
     * Get vendor notification count
     */
    public function getVendorNotificationCount(int $vendorId): int
    {
        return Cache::get("vendor_notification_count_{$vendorId}", 0);
    }

    /**
     * Clear vendor new notifications flag
     */
    public function clearVendorNewNotifications(int $vendorId): void
    {
        Cache::forget("vendor_new_notifications_{$vendorId}");
        Cache::put("vendor_notification_count_{$vendorId}", 0);
    }

    /**
     * Send urgent notification for critical vendor events
     */
    public function sendUrgentVendorNotification($vendor, string $title, string $message, array $data = []): void
    {
        try {
            // Store urgent notification in database
            $vendor->notifications()->create([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\\Services\\VendorNotificationService',
                'data' => [
                    'title' => $title,
                    'message' => $message,
                    'type' => 'urgent',
                    'priority' => 'urgent',
                    'sound_enabled' => true,
                    'data' => array_merge($data, [
                        'timestamp' => now()->toISOString(),
                        'is_urgent' => true,
                    ])
                ],
                'read_at' => null,
            ]);

            // Broadcast urgent notification
            broadcast(new \Illuminate\Notifications\Events\BroadcastNotificationCreated(
                $vendor,
                'urgent.vendor.notification',
                [
                    'title' => $title,
                    'message' => $message,
                    'type' => 'urgent',
                    'sound_enabled' => true,
                    'data' => $data,
                    'timestamp' => now()->toISOString(),
                ]
            ))->toOthers();

            $this->markVendorHasNewNotifications($vendor->id);

            Log::info('Urgent vendor notification sent', [
                'vendor_id' => $vendor->id,
                'title' => $title,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send urgent vendor notification', [
                'vendor_id' => $vendor->id,
                'title' => $title,
                'error' => $e->getMessage(),
            ]);
        }
    }
} 