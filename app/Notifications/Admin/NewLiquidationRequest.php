<?php

namespace App\Notifications\Admin;

use App\Models\LiquidationRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewLiquidationRequest extends Notification
{
    use Queueable;

    protected $liquidationRequest;
    protected $user;

    public function __construct(LiquidationRequest $liquidationRequest)
    {
        $this->liquidationRequest = $liquidationRequest;
        $this->user = $liquidationRequest->user;
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
                'en' => 'New Liquidation Request'
            ],
            'message' => [
                'ar' => "طلب تصفية جديد من {$this->user->name}",
                'en' => "New liquidation request from {$this->user->name}"
            ],
            'type' => 'new_liquidation_request',
            'data' => [
                'request_id' => $this->liquidationRequest->id,
                'user_id' => $this->user->id,
                'amount' => $this->liquidationRequest->amount ?? null,
                // 'link' => route('admin.liquidation-requests.show', $this->liquidationRequest->id),
            ],
        ];
    }
}
