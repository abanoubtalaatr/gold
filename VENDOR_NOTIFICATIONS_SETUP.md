# Vendor Real-Time Notifications Setup Guide

## Overview

Your Laravel application now has a comprehensive real-time notification system for vendors that includes:

- **Real-time Pusher broadcasting** to vendor-specific channels
- **Database notifications** for persistence 
- **Email notifications** for important alerts
- **Sound-enabled notifications** for immediate attention
- **Multi-language support** (Arabic/English)

## ✅ What's Already Implemented

### 1. Broadcasting Events
- `NewOrderForVendorEvent` - Broadcasts new rental/sale orders to vendors
- `NewGoldPieceEvent` - Broadcasts new gold piece availability
- `RentalStatusUpdatedEvent` - Real-time status updates

### 2. Notification Classes
- `NewOrderNotification` - Comprehensive vendor order notifications (database, broadcast, email)
- `NewGoldPieceAvailableNotification` - Gold piece availability alerts
- `RentalStatusNotification` - Status update notifications with sound

### 3. Services
- `VendorNotificationService` - Centralized vendor notification management
- `RealTimeNotificationService` - Enhanced real-time notification delivery

## 🔧 Environment Configuration

Add these variables to your `.env` file:

```bash
# Broadcasting
BROADCAST_DRIVER=pusher
QUEUE_CONNECTION=database

# Pusher Configuration
PUSHER_APP_ID=2004777
PUSHER_APP_KEY=2e7f2672772f33154050
PUSHER_APP_SECRET=4bd7f7567c3cca0b0522
PUSHER_APP_CLUSTER=mt1

# Vite Configuration for Frontend
VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# Email Configuration (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## 🚀 Getting Started

### 1. Install Required Packages (if not already installed)

```bash
composer require pusher/pusher-php-server
npm install --save laravel-echo pusher-js
```

### 2. Run Database Migrations

```bash
php artisan migrate
```

### 3. Set Up Queue Worker

The notification system uses queues for optimal performance:

```bash
# Run queue worker
php artisan queue:work --queue=instant,high,default

# Or use the provided script
./start-queue-worker.sh
```

### 4. Frontend Integration (Inertia + Laravel)

Add to your main JavaScript file:

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// Listen for vendor notifications
const vendorId = window.vendorId; // Set this from your Blade template

Echo.private(`vendor.${vendorId}`)
    .listen('.new.order.for.vendor', (e) => {
        // Handle new order notification
        if (e.sound_enabled) {
            playNotificationSound();
        }
        
        showToast(e.message.en, 'success'); // or e.message.ar for Arabic
        
        // Update dashboard counters
        updateNotificationCount();
        
        // Show action button if needed
        if (e.action_url) {
            showActionButton('View Order', e.action_url);
        }
    });

// Listen for gold piece availability
Echo.private(`vendor.notifications.${vendorId}`)
    .listen('.vendor.new.order', (e) => {
        // Handle notification
        console.log('New vendor notification:', e);
        updateDashboard(e);
    });

function playNotificationSound() {
    const audio = new Audio('/sounds/notification.mp3');
    audio.play().catch(e => console.log('Audio play failed:', e));
}

function showToast(message, type) {
    // Implement your toast notification
    // Example with simple alert (replace with your UI library)
    alert(message);
}

function updateNotificationCount() {
    // Update your notification counter in the UI
    fetch('/vendor/notifications/count')
        .then(response => response.json())
        .then(data => {
            document.querySelector('.notification-count').textContent = data.count;
        });
}
```

## 📡 Broadcasting Channels

The system uses these private channels:

### For Vendors:
- `vendor.{vendorId}` - Main vendor channel for all notifications
- `vendor.notifications.{vendorId}` - Specific notification channel

### For Users:
- `notifications.{userId}` - User-specific notifications
- `branch.{branchId}` - Branch-specific updates

## 🔔 Notification Types

### 1. New Order Notifications
**Triggered when:** API users create rental or sale orders

**Channels:** Database, Broadcast, Email
**Priority:** High
**Sound:** Enabled
**Data includes:**
- Order details (ID, type, customer info)
- Gold piece information
- Branch details
- Pricing information
- Action URLs

### 2. Gold Piece Availability
**Triggered when:** New gold pieces are added for rent/sale

**Channels:** Database, Broadcast
**Priority:** Medium
**Sound:** Enabled

### 3. Status Updates
**Triggered when:** Order status changes

**Channels:** Database, Broadcast
**Priority:** Variable (high for critical updates)
**Sound:** Conditional

## 🎵 Sound Notifications

Sound is automatically enabled for:
- New rental/sale orders
- Order approvals/rejections
- Piece sent notifications
- Critical status changes

Add a notification sound file to `public/sounds/notification.mp3`

## 📱 Mobile Integration

For mobile apps, listen to the same channels:

```javascript
// React Native / Mobile app
const echo = new Echo({
    broadcaster: 'pusher',
    key: 'your-pusher-key',
    cluster: 'mt1',
    forceTLS: true
});

echo.private(`vendor.${vendorId}`)
    .listen('.new.order.for.vendor', (notification) => {
        // Show push notification
        showPushNotification(notification.message.en);
        
        // Play sound
        if (notification.sound_enabled) {
            playNotificationSound();
        }
        
        // Update app state
        updateOrdersList();
    });
```

## 🧪 Testing the System

### 1. Test Pusher Connection

```bash
php artisan tinker
```

```php
// Test broadcasting
broadcast(new App\Events\NewOrderForVendorEvent(
    App\Models\OrderRental::first(), 
    1, // vendor ID
    'rental'
));
```

### 2. Test Vendor Notifications

```php
// Create a test order and check notifications
$order = App\Models\OrderRental::first();
$service = app(App\Services\VendorNotificationService::class);
$service->notifyVendorOfNewOrder($order, 'rental');
```

### 3. Monitor Logs

```bash
tail -f storage/logs/laravel.log
```

## 🔍 Troubleshooting

### Common Issues:

1. **Notifications not broadcasting:**
   - Check Pusher credentials in `.env`
   - Verify queue worker is running
   - Check Laravel logs for errors

2. **Frontend not receiving events:**
   - Verify JavaScript Echo configuration
   - Check browser console for connection errors
   - Ensure user is authenticated for private channels

3. **Email notifications not sending:**
   - Verify MAIL configuration in `.env`
   - Check queue worker is processing email queue

### Debug Commands:

```bash
# Check queue status
php artisan queue:work --once

# Test Pusher connection
php artisan tinker
>>> broadcast(new App\Events\NewOrderForVendorEvent(App\Models\OrderRental::first(), 1));

# Clear cache
php artisan cache:clear
php artisan config:clear
```

## 📊 Dashboard Integration

Add to your vendor dashboard blade file:

```html
<script>
    window.vendorId = {{ auth()->user()->id }};
</script>

<div id="notification-container">
    <!-- Your notification UI here -->
</div>
```

## 🔐 Security

- All channels are private and require authentication
- Vendor can only receive notifications for their own branches
- CSRF protection is maintained
- Sensitive data is not exposed in broadcasts

## 🎯 Next Steps

1. Add the environment variables to your `.env` file
2. Start the queue worker
3. Implement the frontend JavaScript code
4. Add notification sound file
5. Test with a new order creation

Your real-time vendor notification system is now ready to use! 🎉 