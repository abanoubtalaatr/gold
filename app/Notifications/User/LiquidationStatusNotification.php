<?php

namespace App\Notifications\User;

use App\Models\LiquidationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LiquidationStatusNotification extends Notification
{
    use Queueable;

    protected $liquidationRequest;
    protected $status;
    protected $adminNotes;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(LiquidationRequest $liquidationRequest, string $status, ?string $adminNotes = null)
    {
        $this->liquidationRequest = $liquidationRequest;
        $this->status = $status;
        $this->adminNotes = $adminNotes;
    }

    /**
     * Get the notification channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification for database.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $isApproved = $this->status === 'approved';
        
        return [
            'title' => [
                'ar' => $isApproved ? 'تمت الموافقة على طلب التصفية' : 'تم رفض طلب التصفية',
                'en' => $isApproved ? 'Liquidation Request Approved' : 'Liquidation Request Rejected'
            ],
            'message' => [
                'ar' => $isApproved 
                    ? "تمت الموافقة على طلب التصفية بقيمة {$this->liquidationRequest->amount} ريال وتم تحويل المبلغ."
                    : "تم رفض طلب التصفية بقيمة {$this->liquidationRequest->amount} ريال." . ($this->adminNotes ? " السبب: {$this->adminNotes}" : ''),
                'en' => $isApproved 
                    ? "Your liquidation request for {$this->liquidationRequest->amount} SAR has been approved and processed."
                    : "Your liquidation request for {$this->liquidationRequest->amount} SAR has been rejected." . ($this->adminNotes ? " Reason: {$this->adminNotes}" : '')
            ],
            'type' => 'liquidation_status_update',
            'priority' => 'high',
            'sound_enabled' => true,
            'data' => [
                'liquidation_id' => $this->liquidationRequest->id,
                'amount' => $this->liquidationRequest->amount,
                'status' => $this->status,
                'admin_notes' => $this->adminNotes,
                'link' => '/user/liquidation-requests',
                'processed_at' => now()->toISOString(),
            ],
        ];
    }
} 