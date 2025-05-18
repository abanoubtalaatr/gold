<?php

return [
    // General
    'services' => 'الخدمات',
    'service' => 'خدمة',
    'type' => 'النوع',
    'name' => 'الاسم',
    'description' => 'الوصف',
    'price' => 'السعر',
    'available_sessions' => 'الجلسات المتاحة يومياً',
    'duration' => 'المدة',
    'max_concurrent' => 'الحد الأقصى للطلبات المتزامنة',
    'location' => 'الموقع',
    'branches' => 'الفروع',
    'images' => 'الصور',
    'removed_images' => 'الصور المحذوفة',
    'status' => 'الحالة',
    'active' => 'نشط',
    'inactive' => 'غير نشط',
    'rating' => 'التقييم',
    'rating_count' => 'عدد التقييمات',

    // Types
    'type_cupping' => 'الحجامة',
    'type_massage' => 'المساج',

    // Locations
    'location_home' => 'زيارة منزلية',
    'location_center' => 'في المركز فقط',
    'location_both' => 'منزل ومركز',

    // Actions
    'create' => 'إنشاء خدمة',
    'edit' => 'تعديل الخدمة',
    'delete' => 'حذف الخدمة',
    'save' => 'حفظ الخدمة',
    'update' => 'تحديث الخدمة',
    'back_to_list' => 'العودة إلى الخدمات',
    'add_images' => 'إضافة صور',
    'remove_image' => 'حذف الصورة',
    'toggle_status' => 'تغيير الحالة',

    // Messages
    'created_successfully' => 'تم إنشاء الخدمة بنجاح.',
    'updated_successfully' => 'تم تحديث الخدمة بنجاح.',
    'deleted_successfully' => 'تم حذف الخدمة بنجاح.',
    'status_updated_successfully' => 'تم تحديث حالة الخدمة بنجاح.',
    'cannot_delete_active_bookings' => 'لا يمكن حذف الخدمة لوجود حجوزات نشطة.',

    // Placeholders
    'name_placeholder' => 'أدخل اسم الخدمة',
    'description_placeholder' => 'أدخل وصف الخدمة',
    'price_placeholder' => 'أدخل سعر الخدمة',
    'sessions_placeholder' => 'أدخل عدد الجلسات المتاحة يومياً',
    'concurrent_placeholder' => 'أدخل الحد الأقصى للطلبات المتزامنة',

    // Help text
    'image_help' => 'قم بتحميل حتى 5 صور (JPG، PNG، بحد أقصى 5 ميجابايت لكل صورة)',
    'branches_help' => 'اختر فرع واحد على الأقل حيث تتوفر هذه الخدمة',
    'duration_help' => 'اختر مدة الخدمة',
    'location_help' => 'اختر مكان تقديم الخدمة',
    'sessions_help' => 'الحد الأقصى لعدد الجلسات التي يمكنك تقديمها يومياً',
    'concurrent_help' => 'الحد الأقصى المسموح به لطلبات الحجز المتزامنة',

    // Confirmations
    'confirm_delete' => 'هل أنت متأكد من حذف هذه الخدمة؟',
    'confirm_delete_warning' => 'لا يمكن التراجع عن هذا الإجراء.',

    // Attributes
    'available_sessions_per_day' => 'الجلسات المتاحة في اليوم',
    'max_concurrent_requests' => 'الحد الأقصى للطلبات المتزامنة',
    'location_type' => 'موقع الخدمة',
    'images' => 'صور الخدمة',
    'is_active' => 'الحالة',

    // Types
    'types' => [
        'cupping' => 'حجامة',
        'massage' => 'مساج',
    ],

    // Location Types
    'location_types' => [
        'home' => 'المنزل',
        'center' => 'المركز',
        'both' => 'كلاهما',
    ],

    // Messages
    'messages' => [
        'created' => 'تمت إضافة الخدمة بنجاح.',
        'updated' => 'تم تحديث بيانات الخدمة بنجاح.',
        'deleted' => 'تم حذف الخدمة بنجاح.',
        'status_updated' => 'تم تحديث حالة الخدمة بنجاح.',
    ],

    // Validation
    'validation' => [
        'name_required' => 'يرجى إدخال اسم الخدمة.',
        'price_required' => 'يرجى إدخال سعر صحيح للخدمة.',
        'price_numeric' => 'يرجى إدخال سعر صحيح للخدمة.',
        'image_required' => 'يرجى تحميل صورة على الأقل للخدمة.',
        'image_format' => 'يرجى تحميل صورة بصيغة صحيحة (PNG, JPG) بحجم لا يتجاوز 5MB.',
        'image_size' => 'يجب ألا يتجاوز حجم الصورة 5MB.',
        'duration_required' => 'يرجى اختيار مدة الخدمة.',
        'duration_invalid' => 'يرجى اختيار مدة صحيحة للخدمة.',
        'type_required' => 'يرجى اختيار نوع الخدمة.',
        'type_invalid' => 'يرجى اختيار نوع صحيح للخدمة.',
        'location_type_required' => 'يرجى اختيار موقع الخدمة.',
        'location_type_invalid' => 'يرجى اختيار موقع صحيح للخدمة.',
        'sessions_required' => 'يرجى إدخال عدد الجلسات المتاحة في اليوم.',
        'sessions_min' => 'يجب أن يكون عدد الجلسات المتاحة 1 على الأقل.',
        'max_requests_required' => 'يرجى إدخال الحد الأقصى للطلبات المتزامنة.',
        'max_requests_min' => 'يجب أن يكون الحد الأقصى للطلبات المتزامنة 1 على الأقل.',
    ],

    // Errors
    'errors' => [
        'has_active_bookings' => 'لا يمكن حذف هذه الخدمة لوجود حجوزات نشطة.',
    ],

    // Page Titles and Headers
    'add_new_service' => 'إضافة خدمة جديدة',
    'back_to_services' => 'العودة إلى الخدمات',
    'fill_in_details' => 'املأ التفاصيل لإنشاء خدمة جديدة',

    // Basic Information Section
    'basic_information' => 'المعلومات الأساسية',
    'basic_information_desc' => 'أدخل التفاصيل الأساسية لخدمتك',
    'select_type_desc' => 'اختر نوع الخدمة التي تريد تقديمها',
    'name_desc' => 'أدخل اسمًا وصفيًا لخدمتك (3-255 حرف)',
    'description_desc' => 'قدم وصفًا مفصلاً (10-1000 حرف)',

    // Pricing and Duration Section
    'pricing_and_duration' => 'السعر والمدة',
    'pricing_duration_desc' => 'حدد تفاصيل السعر والمدة لخدمتك',
    'price_desc' => 'حدد سعراً تنافسياً لخدمتك',
    'duration_desc' => 'اختر مدة جلسة خدمتك',

    // Availability Settings Section
    'availability_settings' => 'إعدادات التوفر',
    'availability_desc' => 'قم بتكوين توفر خدمتك وسعتها',
    'sessions_desc' => 'عدد الجلسات التي يمكنك التعامل معها يومياً (1-100)',
    'concurrent_desc' => 'الحد الأقصى للطلبات المتزامنة التي يمكنك التعامل معها (1-50)',

    // Location Section
    'location_desc' => 'حدد نوع موقع الخدمة والفروع المتاحة',
    'location_type_desc' => 'حدد أين سيتم تقديم هذه الخدمة',

    // Service Images Section
    'images_desc' => 'قم بتحميل صور تعرض خدمتك (5 صور كحد أقصى)',
    'images_upload_desc' => 'قم بتحميل صور عالية الجودة (JPG, PNG، 5 ميجابايت كحد أقصى لكل صورة)',

    // Form Actions
    'cancel' => 'إلغاء',
    'create_service' => 'إنشاء الخدمة',
    'creating' => 'جاري الإنشاء...',

    // Validation Messages
    'validation' => [
        'required' => 'هذا الحقل مطلوب',
        'min_value' => 'الحد الأدنى للقيمة هو :min',
        'max_value' => 'الحد الأقصى للقيمة هو :max'
    ]
]; 