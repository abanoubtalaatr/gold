# Enhanced Real-Time Notifications for Rental Workflow

## Overview

The rental workflow system now includes enhanced real-time notifications that provide instant updates to users when rental actions are performed. This ensures users receive immediate notifications for critical actions like rental completion, approvals, rejections, and more.

## Features

### 🚀 Real-Time Delivery Types

1. **Critical Notifications** - Highest priority, instant delivery
   - Rental approvals/rejections
   - Rental completions
   - New rental requests

2. **Sound Notifications** - Includes audio alerts
   - Piece sent notifications
   - Important status changes

3. **Standard Real-Time** - Fast delivery with queuing
   - Regular status updates
   - Confirmation notifications

### 🔧 Implementation

#### Enhanced RentalWorkflowService

The `RentalWorkflowService` now uses the new `RealTimeNotificationService` for better notification handling:

```php
// Critical notification for rental completion
$this->rentalWorkflowService->completeRental($order, $actor);

// Sound notification when piece is sent
$this->rentalWorkflowService->markAsSent($order, $actor);

// Instant notification for approvals
$this->rentalWorkflowService->approve($order, $branchId, $actor);
```

#### Using RealTimeNotificationService Directly

```php
use App\Services\RealTimeNotificationService;

class YourController extends Controller 
{
    protected $realTimeNotificationService;
    
    public function __construct(RealTimeNotificationService $realTimeNotificationService)
    {
        $this->realTimeNotificationService = $realTimeNotificationService;
    }
    
    public function completeRental($orderId)
    {
        $order = OrderRental::findOrFail($orderId);
        $oldStatus = $order->status;
        
        // Update status
        $order->update(['status' => OrderRental::STATUS_AVAILABLE]);
        
        // Send critical real-time notification
        $this->realTimeNotificationService->sendCriticalNotification(
            $order, 
            $oldStatus, 
            OrderRental::STATUS_AVAILABLE, 
            auth()->user()
        );
    }
}
```

### 📡 Broadcasting Configuration

Ensure your broadcasting is properly configured in `config/broadcasting.php`:

```php
'default' => env('BROADCAST_DRIVER', 'pusher'),

'connections' => [
    'pusher' => [
        'driver' => 'pusher',
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'encrypted' => true,
        ],
    ],
],
```

### 🎯 Queue Configuration

For optimal performance, configure multiple queue priorities in `config/queue.php`:

```php
'connections' => [
    'database' => [
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
        'after_commit' => true,
    ],
],
```

Run queue workers with priority:

```bash
# High priority queue for critical notifications
php artisan queue:work --queue=instant,high,default

# Or separate workers for different priorities
php artisan queue:work --queue=instant
php artisan queue:work --queue=high
php artisan queue:work --queue=default
```

### 🎵 Sound Notifications

Sound notifications are automatically enabled for important status changes:

- New rental requests (for vendors)
- Rental approvals/rejections
- Piece sent notifications
- Rental completions

### 📱 Frontend Integration

#### JavaScript/Vue.js Example

```javascript
// Listen for real-time events
Echo.private(`notifications.${userId}`)
    .listen('.rental.notification', (e) => {
        // Handle notification
        if (e.sound_enabled) {
            playNotificationSound();
        }
        
        showToast(e.message[userLanguage], e.priority);
        
        if (e.requires_action) {
            showActionButton(e.action_url);
        }
    });

// Listen for rental status updates
Echo.private(`notifications.${userId}`)
    .listen('.rental.status.updated', (e) => {
        updateRentalStatus(e.order_id, e.new_status);
        
        if (e.sound_enabled) {
            playNotificationSound();
        }
    });

function playNotificationSound() {
    const audio = new Audio('/sounds/notification.mp3');
    audio.play().catch(e => console.log('Audio play failed:', e));
}

function showToast(message, priority) {
    // Show toast based on priority
    const toastClass = priority === 'high' ? 'toast-critical' : 'toast-normal';
    // Implementation depends on your toast library
}
```

### 🔄 User Activity Tracking

Register the activity tracking middleware in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\TrackUserActivity::class,
    ]);
    
    $middleware->api(append: [
        \App\Http\Middleware\TrackUserActivity::class,
    ]);
})
```

### 🛠 Advanced Usage

#### Check User Online Status

```php
$isOnline = $this->realTimeNotificationService->isUserOnline($userId);

if ($isOnline) {
    // Send instant notification
    $this->realTimeNotificationService->sendCriticalNotification($order, $oldStatus, $newStatus, $actor);
} else {
    // Queue for later delivery
    $user->notify(new RentalStatusNotification($order, $oldStatus, $newStatus, $actor));
}
```

#### Custom Notification Channels

You can extend the notification system to include additional channels:

```php
// In RentalStatusNotification
public function via(object $notifiable): array
{
    $channels = ['database', 'broadcast'];
    
    // Add SMS for critical notifications
    if ($this->isCriticalStatus() && $notifiable->phone_verified) {
        $channels[] = 'sms';
    }
    
    // Add email for certain users
    if ($notifiable->email_notifications_enabled) {
        $channels[] = 'mail';
    }
    
    return $channels;
}
```

### 📊 Monitoring and Debugging

#### Logs

The system logs all notification activities:

```bash
# Check notification logs
tail -f storage/logs/laravel.log | grep "Real-time notification"

# Check critical notifications
tail -f storage/logs/laravel.log | grep "Critical rental notification"
```

#### Cache Monitoring

Monitor notification delivery:

```php
// Check if notification was sent
$wasSent = Cache::has("notification_sent:{$orderId}:{$userId}:{$status}");

// Get user activity
$lastActivity = Cache::get("user_online:{$userId}");
```

### 🚀 Production Deployment

1. **Queue Workers**: Ensure queue workers are running with proper priority
2. **Broadcasting**: Configure Pusher or Redis for WebSocket connections
3. **Caching**: Use Redis for better performance
4. **Monitoring**: Set up monitoring for failed jobs and broadcasting

#### Supervisor Configuration

```ini
[program:laravel-worker-high]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/app/artisan queue:work --queue=instant,high --sleep=3 --tries=3 --max-time=3600
directory=/path/to/your/app
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/your/app/storage/logs/worker.log
stopwaitsecs=3600
```

### 🎯 Best Practices

1. **Use appropriate notification types**:
   - Critical: Approvals, rejections, completions
   - Sound: Piece sent, important updates
   - Standard: Regular status updates

2. **Handle failures gracefully**:
   - The system includes fallback mechanisms
   - Failed notifications are logged for debugging

3. **Monitor performance**:
   - Queue processing times
   - Broadcasting success rates
   - User engagement with notifications

4. **Test thoroughly**:
   - Real-time delivery
   - Sound notifications
   - Different user scenarios

This enhanced system ensures that all rental workflow actions trigger appropriate real-time notifications, providing users with immediate updates about their rental activities. 