<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\SystemSetting;

class TransactionsService
{
    public function addTransactionForVendorAndPlatform($order, $orderType = 'sale', $platformAmount, $vendorId, $type)
    {
        $vendor = User::find($vendorId);

        $systemSetting = SystemSetting::first();

        $platformCommission = $systemSetting->platform_commission_percentage;

        if ($platformCommission > 0) {
            $platformCommission = ($order->total_price) *  $platformCommission / 100;
            $vendor->update(['debt' => $vendor->debt + $platformCommission]);

            $vendor->transactions()->create([
                'amount' => $platformCommission,
                'description' => 'Platform commission for order #' . $order->id,
                'type' => $type,
                'order_sale_id' => $orderType == 'sale' ? $order->id : null,
                'order_rental_id' => $orderType == 'rental' ? $order->id : null,
            ]);
        }

        $admin = User::where('email', 'admin@admin.com')->first();

        $admin->transactions()->create([
            'amount' => $platformAmount,
            'description' => 'Platform commission for order #' . $order->id,
            'type' => $type,
            'order_sale_id' => $orderType == 'sale' ? $order->id : null,
            'order_rental_id' => $orderType == 'rental' ? $order->id : null,
        ]);
    }
}
