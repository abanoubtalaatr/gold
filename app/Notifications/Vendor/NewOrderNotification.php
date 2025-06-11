<?php

namespace App\Notifications\Vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $orderType;
    protected $branch;
    protected $branchCount;

    public function __construct($order, $branch, string $orderType = 'rental', int $branchCount = 1)
    {
        $this->order = $order;
        $this->branch = $branch;
        $this->orderType = $orderType;
        $this->branchCount = $branchCount;
        
        // Set high priority queue for real-time experience
        $this->onQueue('high');
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        $channels = ['database', 'broadcast'];
        
        // Add email for new orders if vendor has email notifications enabled
        if ($notifiable->email && ($notifiable->email_notifications ?? true)) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        $goldPiece = $this->order->goldPiece;
        $customer = $this->order->user;
        
        $typeAr = match($this->orderType) {
            'rental' => 'تأجير',
            'lease' => 'استئجار',
            default => 'شراء'
        };
        $typeEn = match($this->orderType) {
            'rental' => 'rental',
            'lease' => 'lease',
            default => 'purchase'
        };

        // Create message based on branch count
        $branchInfo = '';
        $branchInfoEn = '';
        if ($this->branchCount > 1) {
            $branchInfo = " (متاح في {$this->branchCount} فروع)";
            $branchInfoEn = " (available at {$this->branchCount} branches)";
        } else {
            $branchInfo = " في فرع {$this->branch->name}";
            $branchInfoEn = " at {$this->branch->name} branch";
        }

        return [
            'title' => [
                'ar' => "طلب {$typeAr} جديد",
                'en' => "New {$typeEn} order"
            ],
            'message' => [
                'ar' => "طلب {$typeAr} جديد للقطعة {$goldPiece->name} من العميل {$customer->name}{$branchInfo}",
                'en' => "New {$typeEn} request for {$goldPiece->name} from customer {$customer->name}{$branchInfoEn}"
            ],
            'type' => 'new_order',
            'priority' => 'high',
            'sound_enabled' => true,
            'data' => [
                'order_id' => $this->order->id,
                'order_type' => $this->orderType,
                'branch_id' => $this->branch->id,
                'branch_name' => $this->branch->name,
                'branch_count' => $this->branchCount,
                'gold_piece_id' => $goldPiece->id,
                'gold_piece_name' => $goldPiece->name,
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'price' => $this->orderType === 'rental' 
                    ? $goldPiece->rental_price_per_day 
                    : $goldPiece->sale_price,
                'action_url' => $this->orderType === 'rental' 
                    ? route('vendor.orders.rental.index') 
                    : route('vendor.orders.index'),
                'timestamp' => now()->toISOString(),
            ]
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        $data = $this->toDatabase($notifiable);
        
        // Add real-time specific data
        $data['notification_id'] = $this->id;
        $data['broadcast_timestamp'] = now()->toISOString();
        
        return new BroadcastMessage($data);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $goldPiece = $this->order->goldPiece;
        $customer = $this->order->user;
        
        $typeEn = $this->orderType === 'rental' ? 'rental' : 'purchase';
        
        // Create subject based on branch count
        if ($this->branchCount > 1) {
            $subject = "New {$typeEn} order available at {$this->branchCount} of your branches";
        } else {
            $subject = "New {$typeEn} order for your branch: {$this->branch->name}";
        }
        
        $price = $this->orderType === 'rental' 
            ? $goldPiece->rental_price_per_day . ' SAR/day'
            : $goldPiece->sale_price . ' SAR';

        $actionUrl = $this->orderType === 'rental' 
            ? route('vendor.orders.rental.index') 
            : route('vendor.orders.index');

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name}!")
            ->line("You have received a new {$typeEn} order:")
            ->line("**Gold Piece:** {$goldPiece->name}")
            ->line("**Customer:** {$customer->name} ({$customer->email})")
            ->line("**Weight:** {$goldPiece->weight}g")
            ->line("**Carat:** {$goldPiece->carat}K")
            ->line("**Price:** {$price}");

        // Add branch information based on count
        if ($this->branchCount > 1) {
            $mailMessage->line("**Available at:** {$this->branchCount} of your branches");
            $mailMessage->line("This order is available at multiple branches. Please check your dashboard to see all available locations.");
        } else {
            $mailMessage->line("**Branch:** {$this->branch->name}");
        }

        return $mailMessage
            ->action("View Order Details", $actionUrl)
            ->line("Please review and respond to this order as soon as possible.")
            ->line("Thank you for using our platform!");
    }

    /**
     * Get the channels the notification should broadcast on.
     */
    public function broadcastOn(): array
    {
        $vendorId = $this->branch->vendor_id ?? $this->branch->vendor->id ?? null;
        
        return $vendorId ? [
            "vendor.{$vendorId}",
            "vendor.notifications.{$vendorId}",
        ] : [];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'vendor.new.order';
    }
} 