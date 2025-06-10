<?php

return [
    // Notification messages
    'new_rental_request' => ':user has requested to rent :piece',
    'payment_confirmed' => 'Payment confirmed for order #:order_id',
    'complaint_response' => 'You have received a response to your complaint #:complaint_id',
    'rental_accepted' => 'Your rental request for :piece has been accepted',
    'purchase_accepted' => 'Your purchase request for :piece has been accepted',
    'new_rental_booking' => 'You have a new rental booking for your piece :piece',
    'piece_status_updated' => 'Your piece :piece status has been updated to :status',
    'liquidation_accepted' => 'Your liquidation request has been accepted',
    'wallet_profits' => 'You have received profits of :amount in your wallet',

    // Wallet notifications
    'wallet_balance_updated' => 'Your wallet balance has been updated',
    'wallet_credit_added' => ':amount SAR has been added to your wallet',
    'wallet_debit_deducted' => ':amount SAR has been deducted from your wallet',
    'wallet_transaction_approved' => 'Your wallet transaction has been approved',
    'wallet_transaction_rejected' => 'Your wallet transaction has been rejected',
    'wallet_balance_current' => 'Your current balance: :balance SAR',

    // API responses
    'fetched_successfully' => 'Notifications fetched successfully',
    'count_fetched_successfully' => 'Unread notifications count fetched successfully',
    'marked_as_read' => 'Notification marked as read successfully',
    'all_marked_as_read' => 'All notifications marked as read successfully',
    'deleted_successfully' => 'Notification deleted successfully',
    'all_deleted_successfully' => 'All notifications deleted successfully',
]; 