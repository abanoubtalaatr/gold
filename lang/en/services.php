<?php

return [
    // General
    'services' => 'Services',
    'service' => 'Service',
    'type' => 'Type',
    'name' => 'Name',
    'description' => 'Description',
    'price' => 'Price',
    'available_sessions' => 'Available Sessions Per Day',
    'duration' => 'Duration',
    'max_concurrent' => 'Maximum Concurrent Requests',
    'location' => 'Location',
    'branches' => 'Branches',
    'images' => 'Images',
    'removed_images' => 'Removed Images',
    'status' => 'Status',
    'active' => 'Active',
    'inactive' => 'Inactive',
    'rating' => 'Rating',
    'rating_count' => 'Number of Ratings',

    // Types
    'type_cupping' => 'Cupping',
    'type_massage' => 'Massage',

    // Locations
    'location_home' => 'Home Visit',
    'location_center' => 'Center Only',
    'location_both' => 'Home & Center',

    // Actions
    'create' => 'Create Service',
    'edit' => 'Edit Service',
    'delete' => 'Delete Service',
    'save' => 'Save Service',
    'update' => 'Update Service',
    'back_to_list' => 'Back to Services',
    'add_images' => 'Add Images',
    'remove_image' => 'Remove Image',
    'toggle_status' => 'Toggle Status',

    // Messages
    'created_successfully' => 'Service created successfully.',
    'updated_successfully' => 'Service updated successfully.',
    'deleted_successfully' => 'Service deleted successfully.',
    'status_updated_successfully' => 'Service status updated successfully.',
    'cannot_delete_active_bookings' => 'Cannot delete service with active bookings.',

    // Placeholders
    'name_placeholder' => 'Enter service name',
    'description_placeholder' => 'Enter service description',
    'price_placeholder' => 'Enter service price',
    'sessions_placeholder' => 'Enter number of available sessions per day',
    'concurrent_placeholder' => 'Enter maximum concurrent requests',

    // Help text
    'image_help' => 'Upload up to 5 images (JPG, PNG, max 5MB each)',
    'branches_help' => 'Select at least one branch where this service is available',
    'duration_help' => 'Select the duration of the service',
    'location_help' => 'Select where the service can be provided',
    'sessions_help' => 'Maximum number of sessions you can provide per day',
    'concurrent_help' => 'Maximum number of concurrent booking requests allowed',

    // Confirmations
    'confirm_delete' => 'Are you sure you want to delete this service?',
    'confirm_delete_warning' => 'This action cannot be undone.',

    // Attributes
    'available_sessions_per_day' => 'Available Sessions Per Day',
    'max_concurrent_requests' => 'Maximum Concurrent Requests',
    'location_type' => 'Service Location',
    'images' => 'Service Images',
    'is_active' => 'Active Status',

    // Location Types
    'location_types' => [
        'home' => 'Home',
        'center' => 'Center',
        'both' => 'Both',
    ],

    // Messages
    'messages' => [
        'created' => 'Service added successfully.',
        'updated' => 'Service details updated successfully.',
        'deleted' => 'Service deleted successfully.',
        'status_updated' => 'Service status updated successfully.',
    ],

    // Validation
    'validation' => [
        'name_required' => 'Please enter the service name.',
        'price_required' => 'Please enter a valid service price.',
        'price_numeric' => 'Please enter a valid service price.',
        'image_required' => 'Please upload at least one service image.',
        'image_format' => 'Please upload a valid image format (PNG, JPG) with a maximum size of 5MB.',
        'image_size' => 'Image size should not exceed 5MB.',
        'duration_required' => 'Please select a service duration.',
        'duration_invalid' => 'Please select a valid service duration.',
        'type_required' => 'Please select a service type.',
        'type_invalid' => 'Please select a valid service type.',
        'location_type_required' => 'Please select a service location type.',
        'location_type_invalid' => 'Please select a valid service location type.',
        'sessions_required' => 'Please enter the number of available sessions per day.',
        'sessions_min' => 'Available sessions must be at least 1.',
        'max_requests_required' => 'Please enter the maximum number of concurrent requests.',
        'max_requests_min' => 'Maximum concurrent requests must be at least 1.',
    ],

    // Errors
    'errors' => [
        'has_active_bookings' => 'This service cannot be deleted due to active bookings.',
    ],

    // Page Titles and Headers
    'add_new_service' => 'Add New Service',
    'back_to_services' => 'Back to Services',
    'fill_in_details' => 'Fill in the details to create a new service offering',

    // Basic Information Section
    'basic_information' => 'Basic Information',
    'basic_information_desc' => 'Enter the basic details of your service',
    'select_type_desc' => 'Select the type of service you want to offer',
    'name_desc' => 'Enter a descriptive name for your service (3-255 characters)',
    'description_desc' => 'Provide a detailed description (10-1000 characters)',

    // Pricing and Duration Section
    'pricing_and_duration' => 'Pricing and Duration',
    'pricing_duration_desc' => 'Set your service pricing and duration details',
    'price_desc' => 'Set a competitive price for your service',
    'duration_desc' => 'Choose how long your service session will last',

    // Availability Settings Section
    'availability_settings' => 'Availability Settings',
    'availability_desc' => 'Configure your service availability and capacity',
    'sessions_desc' => 'Number of sessions you can handle per day (1-100)',
    'concurrent_desc' => 'Maximum simultaneous requests you can handle (1-50)',

    // Location Section
    'location_desc' => 'Specify service location type and available branches',
    'location_type_desc' => 'Select where this service will be provided',

    // Service Images Section
    'images_desc' => 'Upload images showcasing your service (max 5 images)',
    'images_upload_desc' => 'Upload high-quality images (JPG, PNG, max 5MB each)',

    // Types
    'types' => [
        'cupping' => 'Cupping',
        'massage' => 'Massage'
    ],

    // Form Actions
    'cancel' => 'Cancel',
    'create_service' => 'Create Service',
    'creating' => 'Creating...',

    // Messages
    'messages' => [
        'created' => 'Service created successfully',
        'updated' => 'Service updated successfully',
        'deleted' => 'Service deleted successfully',
        'status_updated' => 'Service status updated successfully',
        'create_error' => 'Failed to create service. Please check the form and try again.'
    ],

    // Validation Messages
    'validation' => [
        'required' => 'This field is required',
        'min_value' => 'Minimum value is :min',
        'max_value' => 'Maximum value is :max'
    ]
]; 