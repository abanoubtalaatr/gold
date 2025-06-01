# Comprehensive Rental Request System

## Overview

This document describes the complete implementation of a comprehensive rental request system with real-time notifications, status management, and automated processes for the Laravel Gold Trading Platform.

## Features Implemented

### 🔄 Status Workflow Management
- **Actionable rental statuses** with approval workflow
- **Status transitions**: pending_approval → approved → piece_sent → rented → available
- **Validation** of status transitions to prevent invalid state changes
- **Service layer pattern** with `RentalWorkflowService` for centralized business logic

### 🔔 Real-time Notifications
- **Pusher integration** for real-time broadcasting
- **Sound notifications** for vendors receiving requests
- **Multi-language support** (Arabic/English)
- **Database and broadcast notifications** for all stakeholders
- **Toast notifications** in the frontend with auto-hide functionality

### ⏰ Automated Processes
- **Cron job** for automatic rental expiration handling
- **Daily processing** at midnight to mark expired rentals as available
- **Console command** with dry-run and detailed output options
- **Error handling** and comprehensive logging

### 👥 Multi-party Notifications
- **Users** (rental requesters) receive status updates
- **Vendors** receive new requests and status changes
- **Gold piece owners** (for rent-type orders) receive notifications
- **Branch-specific** notifications for targeted updates

## Technical Implementation

### 1. Backend Components

#### Events
- **`RentalStatusUpdatedEvent`** (`app/Events/RentalStatusUpdatedEvent.php`)
  - Broadcasts to private channels for users and branches
  - Multi-language status messages
  - Sound-enabled notifications
  - Actor tracking and comprehensive data payload

#### Notifications
- **`RentalStatusNotification`** (`app/Notifications/RentalStatusNotification.php`)
  - Queue-based notification supporting database and broadcast channels
  - Multi-language support
  - Different action URLs for users vs vendors
  - Comprehensive data structure

#### Services
- **`RentalWorkflowService`** (`app/Services/RentalWorkflowService.php`)
  - Centralized service for managing rental status transitions
  - Methods: `approve()`, `reject()`, `markAsSent()`, `confirmRental()`, `completeRental()`
  - Database transactions for consistency
  - Automatic notifications to all relevant parties
  - Status transition validation and special handling
  - Comprehensive logging

#### Console Commands
- **`ProcessExpiredRentals`** (`app/Console/Commands/ProcessExpiredRentals.php`)
  - Console command for cron job automation
  - Finds expired rentals (end_date < now, status = 'rented')
  - Automatically marks as 'available'
  - Dry-run and show-details options
  - Error handling and progress reporting

#### Controllers
- **Updated `RentalRequestController`** (`app/Http/Controllers/Vendor/RentalRequestController.php`)
  - Uses `RentalWorkflowService` for proper status management
  - Added methods: `markAsSent()`, `confirmRental()`, `completeRental()`
  - Enhanced error handling and logging
  - Action permissions based on current status

### 2. Frontend Components

#### Vue.js Enhancements
- **Real-time notification listening** with Pusher Echo integration
- **Dynamic action buttons** based on allowed status transitions
- **Toast notifications** with sound support
- **Multi-language message handling**
- **Comprehensive status tracking**

#### User Interface
- **Status-based action buttons**: Accept, Reject, Mark as Sent, Confirm Rental, Complete Rental
- **Real-time status updates** without page refresh
- **Visual status indicators** with color-coded badges
- **Notification toast** with auto-hide and sound support

### 3. Database Schema

#### OrderRental Model
- **Status constants** properly defined
- **Status workflow** validation
- **Type constants** for RENT_TYPE and LEASE_TYPE
- **Relationships** with User, GoldPiece, and Branch models

### 4. Routes
```php
// Additional status transition routes
Route::post('/rental-requests/{order}/mark-sent', [RentalRequestController::class, 'markAsSent'])->name('rental-requests.mark-sent');
Route::post('/rental-requests/{order}/confirm-rental', [RentalRequestController::class, 'confirmRental'])->name('rental-requests.confirm-rental');
Route::post('/rental-requests/{order}/complete-rental', [RentalRequestController::class, 'completeRental'])->name('rental-requests.complete-rental');
```

### 5. Scheduled Tasks
```php
// In routes/console.php
Schedule::command('rentals:process-expired')
    ->daily()
    ->at('00:00')
    ->withoutOverlapping()
    ->description('Process expired rental orders and mark them as available');
```

## Configuration Requirements

### 1. Pusher Configuration
Add these environment variables to your `.env` file:

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=1907964
PUSHER_APP_KEY=7125d79184ff70ea2808
PUSHER_APP_SECRET=d4cf97351e46c5b42a73
PUSHER_APP_CLUSTER=eu
```

### 2. Queue Configuration
Ensure your queue configuration is set up for notification processing:

```env
QUEUE_CONNECTION=database
# or
QUEUE_CONNECTION=redis
```

### 3. Sound Files
Add notification sound files to `public/sounds/`:
- `notification.mp3`
- `notification.wav`

## Usage Guide

### For Vendors

#### 1. Viewing Rental Requests
- Navigate to the Rental Requests page
- View all rental requests with current status
- Filter by status, branch, date range, etc.

#### 2. Managing Rental Requests

**Pending Approval Status:**
- Click "Accept" to approve a request (select branch)
- Click "Reject" to reject a request

**Approved Status:**
- Click "Mark as Sent" when the piece is sent to the user

**Piece Sent Status:**
- Click "Confirm Rental" when the user receives the piece

**Rented Status:**
- Click "Complete Rental" when the rental period ends
- Or wait for automatic processing via cron job

#### 3. Real-time Notifications
- Receive instant notifications for new requests
- Sound alerts for important status changes
- Toast notifications with status updates

### For System Administrators

#### 1. Monitoring Expired Rentals
```bash
# Check what would be processed (dry run)
php artisan rentals:process-expired --dry-run --show-details

# Process expired rentals
php artisan rentals:process-expired
```

#### 2. Cron Job Setup
Add to your system's crontab:
```bash
# Run Laravel scheduler every minute
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

#### 3. Queue Workers
Ensure queue workers are running for notification processing:
```bash
php artisan queue:work --daemon
```

## Status Workflow

```
pending_approval
    ↓ (vendor accepts)
approved
    ↓ (vendor marks as sent)
piece_sent
    ↓ (vendor confirms rental)
rented
    ↓ (vendor completes OR auto-expires)
available
```

**Alternative flows:**
- `pending_approval` → `rejected` (vendor rejects)
- `approved` → `rejected` (vendor cancels)
- `piece_sent` → `available` (if something goes wrong)

## Notification Channels

### 1. Private Channels
- **User notifications**: `notifications.{user_id}`
- **Branch notifications**: `branch.{branch_id}`

### 2. Notification Types
- **Database notifications**: Stored in database for history
- **Broadcast notifications**: Real-time via Pusher
- **Sound notifications**: Audio alerts for vendors

### 3. Multi-language Support
All notifications support Arabic and English:
```json
{
  "message": {
    "ar": "تم قبول طلب تأجير القطعة",
    "en": "Rental request has been approved"
  }
}
```

## Testing

### 1. Manual Testing
1. Create a rental request as a user
2. View the request in vendor dashboard
3. Test each status transition
4. Verify real-time notifications
5. Check expired rental processing

### 2. Command Testing
```bash
# Test expired rental processing
php artisan rentals:process-expired --dry-run --show-details

# Test with actual data
php artisan rentals:process-expired --show-details
```

### 3. Notification Testing
1. Ensure Pusher credentials are correct
2. Test real-time notifications in browser
3. Verify sound notifications work
4. Check database notifications are stored

## Security Considerations

### 1. Authorization
- Vendors can only manage rentals for their branches
- Status transitions are validated
- Actor tracking for all changes

### 2. Data Integrity
- Database transactions for status updates
- Validation of allowed status transitions
- Comprehensive error handling and logging

### 3. Real-time Security
- Private channels for sensitive notifications
- User and branch-specific broadcasting
- Secure Pusher configuration

## Performance Considerations

### 1. Queue Processing
- All notifications are queued for background processing
- Database transactions for consistency
- Efficient database queries with eager loading

### 2. Real-time Broadcasting
- Targeted channel broadcasting
- Minimal data payload
- Connection management in frontend

### 3. Scheduled Tasks
- Efficient query for expired rentals
- Batch processing with error handling
- Logging and monitoring capabilities

## Troubleshooting

### 1. Notifications Not Working
- Check Pusher credentials in `.env`
- Verify queue workers are running
- Check Laravel logs for errors

### 2. Status Transitions Failing
- Check `RentalWorkflowService` logs
- Verify database constraints
- Ensure proper authorization

### 3. Cron Job Not Running
- Verify crontab entry
- Check Laravel scheduler logs
- Test command manually

### 4. Real-time Updates Not Appearing
- Check browser console for errors
- Verify Echo/Pusher connection
- Check private channel authorization

## Conclusion

This comprehensive rental request system provides a complete solution for managing rental workflows with real-time notifications, automated processes, and robust status management. The implementation follows Laravel best practices with proper service layer architecture, event-driven design, and comprehensive error handling.

The system is designed to be scalable, maintainable, and user-friendly, providing a seamless experience for both vendors and users while ensuring data integrity and security throughout the rental process. 