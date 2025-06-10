<?php

namespace App\Services;

use App\Models\OrderSale;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesWorkflowService
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Approve a sale order
     */
    public function approve(OrderSale $order, int $branchId, User $vendor): bool
    {
        return DB::transaction(function () use ($order, $branchId, $vendor) {
            $order->update([
                'branch_id' => $branchId,
                'status' => OrderSale::STATUS_APPROVED,
            ]);

            Log::info('Sale order approved', [
                'order_id' => $order->id,
                'vendor_id' => $vendor->id,
                'branch_id' => $branchId
            ]);

            return true;
        });
    }

    /**
     * Reject a sale order
     */
    public function reject(OrderSale $order, User $vendor): bool
    {
        return DB::transaction(function () use ($order, $vendor) {
            $order->update([
                'status' => OrderSale::STATUS_REJECTED,
            ]);

            Log::info('Sale order rejected', [
                'order_id' => $order->id,
                'vendor_id' => $vendor->id
            ]);

            return true;
        });
    }

    /**
     * Mark order as sent to client
     */
    public function markAsSent(OrderSale $order, User $vendor): bool
    {
        return DB::transaction(function () use ($order, $vendor) {
            $order->update([
                'status' => OrderSale::STATUS_PIECE_SENT,
            ]);

            return true;
        });
    }

    /**
     * Mark order as sold and calculate wallet transactions
     */
    public function markAsSold(OrderSale $order, User $vendor): array
    {
        return DB::transaction(function () use ($order, $vendor) {
            // Update order status
            $order->update([
                'status' => OrderSale::STATUS_SOLD,
            ]);

            // Calculate and create wallet transactions
            $commissionData = $this->calculateWalletTransactions($order, $vendor);

            Log::info('Sale order marked as sold with wallet transactions', [
                'order_id' => $order->id,
                'vendor_id' => $vendor->id,
                'total_price' => $order->total_price,
                'vendor_commission_amount' => $commissionData['vendor_commission'],
                'platform_commission_amount' => $commissionData['platform_commission']
            ]);

            return [
                'success' => true,
                'commission_amount' => $commissionData['vendor_commission'],
                'vendor_commission' => $commissionData['vendor_commission'],
                'platform_commission' => $commissionData['platform_commission'],
                'total_price' => $order->total_price
            ];
        });
    }

    /**
     * Calculate wallet transactions based on commission settings
     */
    protected function calculateWalletTransactions(OrderSale $order, User $vendor): array
    {
        // Get commission percentages from system settings
        $platformCommissionPercentage = SystemSetting::value('platform_commission_percentage') ?? 0;
        $merchantCommissionPercentage = SystemSetting::value('merchant_commission_percentage') ?? 0;

        $totalPrice = $order->total_price;

        // Calculate commission amounts
        $platformCommissionAmount = ($totalPrice * $platformCommissionPercentage) / 100;
        $vendorCommissionAmount = ($totalPrice * $merchantCommissionPercentage) / 100;

        // Create or get vendor wallet and credit commission
        if ($vendorCommissionAmount > 0) {
            $vendorWallet = $this->walletService->getWallet($vendor);
            $vendorTransaction = $vendorWallet->credit(
                $vendorCommissionAmount,
                "Earnings from sale order #{$order->id}",
                'completed'
            );

            // Link transaction to the order
            $vendorTransaction->update([
                'transactionable_type' => OrderSale::class,
                'transactionable_id' => $order->id,
            ]);
        }

        // Create or get admin wallet and credit platform commission
        if ($platformCommissionAmount > 0) {
            $admin = $this->getAdminUser();
            if ($admin) {
                $adminWallet = $this->walletService->getWallet($admin);
                $adminTransaction = $adminWallet->credit(
                    $platformCommissionAmount,
                    "Platform commission from sale order #{$order->id}",
                    'completed'
                );

                // Link transaction to the order
                $adminTransaction->update([
                    'transactionable_type' => OrderSale::class,
                    'transactionable_id' => $order->id,
                ]);
            }
        }

        Log::info('Wallet transactions calculated for sale order', [
            'order_id' => $order->id,
            'total_price' => $totalPrice,
            'platform_commission_percentage' => $platformCommissionPercentage,
            'merchant_commission_percentage' => $merchantCommissionPercentage,
            'platform_commission_amount' => $platformCommissionAmount,
            'vendor_commission_amount' => $vendorCommissionAmount,
            'vendor_id' => $vendor->id,
            'admin_id' => $admin->id ?? null
        ]);

        return [
            'vendor_commission' => $vendorCommissionAmount,
            'platform_commission' => $platformCommissionAmount
        ];
    }

    /**
     * Get the admin/super admin user for platform commission
     */
    protected function getAdminUser(): ?User
    {
        // Try to find a user with super admin role first
        $admin = User::role('superadmin')->first();

        // If no super admin, try admin role
        if (!$admin) {
            $admin = User::role('admin')->first();
        }

        // If still no admin found, get the first user (fallback)
        if (!$admin) {
            $admin = User::first();
        }

        return $admin;
    }

    /**
     * Get allowed status transitions for a given current status
     */
    public function getAllowedTransitions(string $currentStatus): array
    {
        return match ($currentStatus) {
            OrderSale::STATUS_PENDING_APPROVAL => [
                OrderSale::STATUS_APPROVED,
                OrderSale::STATUS_REJECTED
            ],
            OrderSale::STATUS_APPROVED => [
                OrderSale::STATUS_PIECE_SENT
            ],
            OrderSale::STATUS_PIECE_SENT => [
                OrderSale::STATUS_SOLD
            ],
            default => []
        };
    }

    /**
     * Get available actions for an order based on its current status
     */
    public function getAvailableActions(OrderSale $order): array
    {
        $allowedTransitions = $this->getAllowedTransitions($order->status);
        $actions = [];

        foreach ($allowedTransitions as $transition) {
            switch ($transition) {
                case OrderSale::STATUS_APPROVED:
                    if ($order->status === OrderSale::STATUS_PENDING_APPROVAL) {
                        $actions[] = 'approve';
                    }
                    break;
                case OrderSale::STATUS_REJECTED:
                    if ($order->status === OrderSale::STATUS_PENDING_APPROVAL) {
                        $actions[] = 'reject';
                    }
                    break;
                case OrderSale::STATUS_PIECE_SENT:
                    if ($order->status === OrderSale::STATUS_APPROVED) {
                        $actions[] = 'mark_as_sent';
                    }
                    break;
                case OrderSale::STATUS_SOLD:
                    if ($order->status === OrderSale::STATUS_PIECE_SENT) {
                        $actions[] = 'mark_as_sold';
                    }
                    break;
            }
        }

        return $actions;
    }
}