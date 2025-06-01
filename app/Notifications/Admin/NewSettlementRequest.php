<?php

namespace App\Notifications\Admin;

use App\Models\SettlementRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewSettlementRequest extends Notification
{
    use Queueable;

    protected $settlementRequest;
    protected $vendor;

    public function __construct(SettlementRequest $settlementRequest)
    {
        $this->settlementRequest = $settlementRequest;
        $this->vendor = $settlementRequest->wallet->user;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => [
                'ar' => 'طلب تصفية جديد',
                'en' => 'New Settlement Request'
            ],
            'message' => [
                'ar' => "طلب تصفية جديد من {$this->vendor->store_name_ar} بقيمة {$this->settlementRequest->amount} ريال",
                'en' => "New settlement request from {$this->vendor->store_name_en} for {$this->settlementRequest->amount} SAR"
            ],
            'type' => 'new_settlement_request',
            'data' => [
                'request_id' => $this->settlementRequest->id,
                'vendor_id' => $this->vendor->id,
                'amount' => $this->settlementRequest->amount,
                // 'link' => route('admin.settlements.show', $this->settlementRequest->id),
            ],
        ];
    }
}
