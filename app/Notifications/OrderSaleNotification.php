<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderSaleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $type;
    protected $vendorName;
    protected $additionalData;

    /**
     * Create a new notification instance.
     *
     * @param $order - The OrderSale instance
     * @param string $type - The notification type (accepted, rejected, piece_sent, sold)
     * @param string $vendorName - The vendor's name
     * @param array $additionalData - Additional data for specific notification types
     */
    public function __construct($order, string $type, string $vendorName, array $additionalData = [])
    {
        $this->order = $order;
        $this->type = $type;
        $this->vendorName = $vendorName;
        $this->additionalData = $additionalData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $goldPiece = $this->order->goldPiece;
        $mailData = $this->getNotificationData();

        return (new MailMessage)
            ->subject($mailData['title']['en'])
            ->greeting('Hello ' . $notifiable->name)
            ->line($mailData['message']['en'])
            ->line('Gold Piece: ' . ($goldPiece->name ?? 'N/A'))
            ->line('Order ID: ' . $this->order->id)
            ->when(isset($mailData['action_text']), function ($message) use ($mailData) {
                return $message->action($mailData['action_text']['en'], $mailData['action_url'] ?? url('/'));
            })
            ->line('Thank you for using our platform!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->getNotificationData();
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->getNotificationData();
    }

    /**
     * Get notification data based on type
     */
    protected function getNotificationData(): array
    {
        $goldPiece = $this->order->goldPiece;
        $baseData = [
            'order_id' => $this->order->id,
            'gold_piece_id' => $goldPiece->id,
            'vendor_name' => $this->vendorName,
            'sound_enabled' => true,
            'priority' => 'high'
        ];

        return match ($this->type) {
            'accepted' => [
                'title' => [
                    'ar' => 'تم قبول طلب بيع القطعة الذهبية',
                    'en' => 'Sale Order Accepted'
                ],
                'message' => [
                    'ar' => "تم قبول طلب بيع قطعتك الذهبية {$goldPiece->name} من قبل المتجر {$this->vendorName}",
                    'en' => "Your sale order for gold piece {$goldPiece->name} has been accepted by {$this->vendorName}"
                ],
                'type' => 'sale_order_accepted',
                'data' => array_merge($baseData, [
                    'branch_name' => $this->additionalData['branch_name'] ?? null,
                    'status' => 'approved'
                ])
            ],

            'rejected' => [
                'title' => [
                    'ar' => 'تم رفض طلب بيع القطعة الذهبية',
                    'en' => 'Sale Order Rejected'
                ],
                'message' => [
                    'ar' => "تم رفض طلب بيع قطعتك الذهبية {$goldPiece->name} من قبل المتجر {$this->vendorName}",
                    'en' => "Your sale order for gold piece {$goldPiece->name} has been rejected by {$this->vendorName}"
                ],
                'type' => 'sale_order_rejected',
                'data' => array_merge($baseData, [
                    'rejection_reason' => $this->additionalData['reason'] ?? null,
                    'status' => 'rejected'
                ])
            ],

            'piece_sent' => [
                'title' => [
                    'ar' => 'تم إرسال القطعة الذهبية للعميل',
                    'en' => 'Gold Piece Sent to Customer'
                ],
                'message' => [
                    'ar' => "تم إرسال قطعتك الذهبية {$goldPiece->name} للعميل المهتم بالشراء",
                    'en' => "Your gold piece {$goldPiece->name} has been sent to the interested customer"
                ],
                'type' => 'sale_piece_sent',
                'data' => array_merge($baseData, [
                    'expected_sale_completion' => $this->additionalData['expected_completion'] ?? null,
                    'status' => 'piece_sent'
                ])
            ],

            'sold' => [
                'title' => [
                    'ar' => 'تم بيع القطعة الذهبية بنجاح',
                    'en' => 'Gold Piece Successfully Sold'
                ],
                'message' => [
                    'ar' => "تم بيع قطعتك الذهبية {$goldPiece->name} بنجاح! لقد تم إضافة العمولة إلى محفظتك",
                    'en' => "Your gold piece {$goldPiece->name} has been successfully sold! Commission has been added to your wallet"
                ],
                'type' => 'sale_completed',
                'action_text' => [
                    'ar' => 'عرض المحفظة',
                    'en' => 'View Wallet'
                ],
                'action_url' => route('vendor.wallet.index'),
                'data' => array_merge($baseData, [
                    'sale_amount' => $this->order->total_price,
                    'commission_amount' => $this->additionalData['commission_amount'] ?? null,
                    'status' => 'sold'
                ])
            ],

            default => [
                'title' => [
                    'ar' => 'تحديث على طلب البيع',
                    'en' => 'Sale Order Update'
                ],
                'message' => [
                    'ar' => "تم تحديث طلب بيع قطعتك الذهبية {$goldPiece->name}",
                    'en' => "Your sale order for gold piece {$goldPiece->name} has been updated"
                ],
                'type' => 'sale_order_update',
                'data' => $baseData
            ]
        };
    }
}
