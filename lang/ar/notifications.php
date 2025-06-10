<?php

return [
    // Notification messages
    'new_rental_request' => 'قام :user بطلب استئجار :piece',
    'payment_confirmed' => 'تم تأكيد الدفع للطلب رقم #:order_id',
    'complaint_response' => 'لقد تلقيت رداً على شكواك رقم #:complaint_id',
    'rental_accepted' => 'تم قبول طلب الإيجار الخاص بك لـ :piece',
    'purchase_accepted' => 'تم قبول طلب الشراء الخاص بك لـ :piece',
    'new_rental_booking' => 'لديك حجز إيجار جديد لقطعتك :piece',
    'piece_status_updated' => 'تم تحديث حالة قطعتك :piece إلى :status',
    'liquidation_accepted' => 'تم قبول طلب التصفية الخاص بك',
    'wallet_profits' => 'لقد تلقيت أرباحاً بقيمة :amount في محفظتك',

    // Wallet notifications
    'wallet_balance_updated' => 'تم تحديث رصيد محفظتك',
    'wallet_credit_added' => 'تم إضافة :amount ريال إلى محفظتك',
    'wallet_debit_deducted' => 'تم خصم :amount ريال من محفظتك',
    'wallet_transaction_approved' => 'تم قبول معاملة محفظتك',
    'wallet_transaction_rejected' => 'تم رفض معاملة محفظتك',
    'wallet_balance_current' => 'رصيدك الحالي: :balance ريال',

    // API responses
    'fetched_successfully' => 'تم جلب الإشعارات بنجاح',
    'count_fetched_successfully' => 'تم جلب عدد الإشعارات غير المقروءة بنجاح',
    'marked_as_read' => 'تم تحديد الإشعار كمقروء بنجاح',
    'all_marked_as_read' => 'تم تحديد جميع الإشعارات كمقروءة بنجاح',
    'deleted_successfully' => 'تم حذف الإشعار بنجاح',
    'all_deleted_successfully' => 'تم حذف جميع الإشعارات بنجاح',
]; 