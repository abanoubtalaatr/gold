<?php

namespace App\Notifications\Vendor;

use App\Models\SettlementRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SettlementStatusNotification extends Notification
{
    use Queueable;

    protected $settlementRequest;
    protected $status;
    protected $adminNotes;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(SettlementRequest $settlementRequest, string $status, ?string $adminNotes = null)
    {
        $this->settlementRequest = $settlementRequest;
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
                'en' => $isApproved ? 'Settlement Request Approved' : 'Settlement Request Rejected'
            ],
            'message' => [
                'ar' => $isApproved 
                    ? "تمت الموافقة على طلب التصفية بقيمة {$this->settlementRequest->amount} ريال وتم تحويل المبلغ."
                    : "تم رفض طلب التصفية بقيمة {$this->settlementRequest->amount} ريال." . ($this->adminNotes ? " السبب: {$this->adminNotes}" : ''),
                'en' => $isApproved 
                    ? "Your settlement request for {$this->settlementRequest->amount} SAR has been approved and processed."
                    : "Your settlement request for {$this->settlementRequest->amount} SAR has been rejected." . ($this->adminNotes ? " Reason: {$this->adminNotes}" : '')
            ],
            'type' => 'settlement_status_update',
            'priority' => 'high',
            'sound_enabled' => true,
            'data' => [
                'settlement_id' => $this->settlementRequest->id,
                'amount' => $this->settlementRequest->amount,
                'status' => $this->status,
                'admin_notes' => $this->adminNotes,
                'link' => '/vendor/settlement-requests',
                'processed_at' => now()->toISOString(),
            ],
        ];
    }
} 