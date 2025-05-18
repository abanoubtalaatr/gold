<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute field must be accepted.',
    'accepted_if' => 'The :attribute field must be accepted when :other is :value.',
    'active_url' => 'The :attribute field must be a valid URL.',
    'after' => 'The :attribute field must be a date after :date.',
    'after_or_equal' => 'The :attribute field must be a date after or equal to :date.',
    'alpha' => 'The :attribute field must only contain letters.',
    'alpha_dash' => 'The :attribute field must only contain letters, numbers, dashes, and underscores.',
    'alpha_num' => 'The :attribute field must only contain letters and numbers.',
    'array' => 'The :attribute field must be an array.',
    'ascii' => 'The :attribute field must only contain single-byte alphanumeric characters and symbols.',
    'before' => 'The :attribute field must be a date before :date.',
    'before_or_equal' => 'The :attribute field must be a date before or equal to :date.',
    'between' => [
        'array' => 'The :attribute field must have between :min and :max items.',
        'file' => 'The :attribute field must be between :min and :max kilobytes.',
        'numeric' => 'The :attribute field must be between :min and :max.',
        'string' => 'The :attribute field must be between :min and :max characters.',
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'can' => 'The :attribute field contains an unauthorized value.',
    'confirmed' => 'The :attribute field confirmation does not match.',
    'current_password' => 'The password is incorrect.',
    'date' => 'The :attribute field must be a valid date.',
    'date_equals' => 'The :attribute field must be a date equal to :date.',
    'date_format' => 'The :attribute field must match the format :format.',
    'decimal' => 'The :attribute field must have :decimal decimal places.',
    'declined' => 'The :attribute field must be declined.',
    'declined_if' => 'The :attribute field must be declined when :other is :value.',
    'different' => 'The :attribute field and :other must be different.',
    'digits' => 'The :attribute field must be :digits digits.',
    'digits_between' => 'The :attribute field must be between :min and :max digits.',
    'dimensions' => 'The :attribute field has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'doesnt_end_with' => 'The :attribute field must not end with one of the following: :values.',
    'doesnt_start_with' => 'The :attribute field must not start with one of the following: :values.',
    'email' => 'The :attribute field must be a valid email address.',
    'ends_with' => 'The :attribute field must end with one of the following: :values.',
    'enum' => 'The selected :attribute is invalid.',
    'exists' => 'The selected :attribute is invalid.',
    'extensions' => 'The :attribute field must have one of the following extensions: :values.',
    'file' => 'The :attribute field must be a file.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'array' => 'The :attribute field must have more than :value items.',
        'file' => 'The :attribute field must be greater than :value kilobytes.',
        'numeric' => 'The :attribute field must be greater than :value.',
        'string' => 'The :attribute field must be greater than :value characters.',
    ],
    'gte' => [
        'array' => 'The :attribute field must have :value items or more.',
        'file' => 'The :attribute field must be greater than or equal to :value kilobytes.',
        'numeric' => 'The :attribute field must be greater than or equal to :value.',
        'string' => 'The :attribute field must be greater than or equal to :value characters.',
    ],
    'hex_color' => 'The :attribute field must be a valid hexadecimal color.',
    'image' => 'The :attribute field must be an image.',
    'in' => 'The selected :attribute is invalid.',
    'in_array' => 'The :attribute field must exist in :other.',
    'integer' => 'The :attribute field must be an integer.',
    'ip' => 'The :attribute field must be a valid IP address.',
    'ipv4' => 'The :attribute field must be a valid IPv4 address.',
    'ipv6' => 'The :attribute field must be a valid IPv6 address.',
    'json' => 'The :attribute field must be a valid JSON string.',
    'lowercase' => 'The :attribute field must be lowercase.',
    'lt' => [
        'array' => 'The :attribute field must have less than :value items.',
        'file' => 'The :attribute field must be less than :value kilobytes.',
        'numeric' => 'The :attribute field must be less than :value.',
        'string' => 'The :attribute field must be less than :value characters.',
    ],
    'lte' => [
        'array' => 'The :attribute field must not have more than :value items.',
        'file' => 'The :attribute field must be less than or equal to :value kilobytes.',
        'numeric' => 'The :attribute field must be less than or equal to :value.',
        'string' => 'The :attribute field must be less than or equal to :value characters.',
    ],
    'mac_address' => 'The :attribute field must be a valid MAC address.',
    'max' => [
        'array' => 'The :attribute field must not have more than :max items.',
        'file' => 'The :attribute field must not be greater than :max kilobytes.',
        'numeric' => 'The :attribute field must not be greater than :max.',
        'string' => 'The :attribute field must not be greater than :max characters.',
    ],
    'max_digits' => 'The :attribute field must not have more than :max digits.',
    'mimes' => 'The :attribute field must be a file of type: :values.',
    'mimetypes' => 'The :attribute field must be a file of type: :values.',
    'min' => [
        'array' => 'The :attribute field must have at least :min items.',
        'file' => 'The :attribute field must be at least :min kilobytes.',
        'numeric' => 'The :attribute field must be at least :min.',
        'string' => 'The :attribute field must be at least :min characters.',
    ],
    'min_digits' => 'The :attribute field must have at least :min digits.',
    'missing' => 'The :attribute field must be missing.',
    'missing_if' => 'The :attribute field must be missing when :other is :value.',
    'missing_unless' => 'The :attribute field must be missing unless :other is :value.',
    'missing_with' => 'The :attribute field must be missing when :values is present.',
    'missing_with_all' => 'The :attribute field must be missing when :values are present.',
    'multiple_of' => 'The :attribute field must be a multiple of :value.',
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute field format is invalid.',
    'numeric' => 'The :attribute field must be a number.',
    'password' => [
        'letters' => 'The :attribute field must contain at least one letter.',
        'mixed' => 'The :attribute field must contain at least one uppercase and one lowercase letter.',
        'numbers' => 'The :attribute field must contain at least one number.',
        'symbols' => 'The :attribute field must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.',
    ],
    'present' => 'The :attribute field must be present.',
    'present_if' => 'The :attribute field must be present when :other is :value.',
    'present_unless' => 'The :attribute field must be present unless :other is :value.',
    'present_with' => 'The :attribute field must be present when :values is present.',
    'present_with_all' => 'The :attribute field must be present when :values are present.',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'The :attribute field prohibits :other from being present.',
    'regex' => 'The :attribute field format is invalid.',
    'required' => 'The :attribute field is required.',
    'required_array_keys' => 'The :attribute field must contain entries for: :values.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_if_accepted' => 'The :attribute field is required when :other is accepted.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute field must match :other.',
    'size' => [
        'array' => 'The :attribute field must contain :size items.',
        'file' => 'The :attribute field must be :size kilobytes.',
        'numeric' => 'The :attribute field must be :size.',
        'string' => 'The :attribute field must be :size characters.',
    ],
    'starts_with' => 'The :attribute field must start with one of the following: :values.',
    'string' => 'The :attribute field must be a string.',
    'timezone' => 'The :attribute field must be a valid timezone.',
    'unique' => 'The :attribute has already been taken.',
    'uploaded' => 'The :attribute failed to upload.',
    'uppercase' => 'The :attribute field must be uppercase.',
    'url' => 'The :attribute field must be a valid URL.',
    'ulid' => 'The :attribute field must be a valid ULID.',
    'uuid' => 'The :attribute field must be a valid UUID.',
    'duplicate_level_data'=>'This level already exists with the same number of labors and price.',
    'current_password' => 'The current password',
    'You must add at least one level.' => 'You must add at least one level.',
    'Each level must have a valid level type.' => 'Each level must have a valid level type.',
    'Each level must have a number of labors.' => 'Each level must have a number of labors.',
    'Each level must have a price.' => 'Each level must have a price.',
    'invalid_level_data' => 'Invalid level data.',
    "levels_required"=>  'Service Level Required',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',

        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'latitude' => 'Latitude',
        'longitude' => 'Longitude',
        'stars' => 'Stars',
        'job_role' => 'Job Role',
        'registration_number' => 'Registration Number',
        'tax_number' => 'Tax Number',
        'star_classification_certificate' => 'Star Classification Certificate',
        'compliance_proof' => 'Compliance Proof',
        'logo' => 'Logo',
        'features' => 'Features',
        'feature' => 'Feature',
        'images' => 'Images',
        'image' => 'Image',
        'social_links' => 'Social Links',
        'facebook_link' => 'Facebook Link',
        'instagram_link' => 'Instagram Link',
        'twitter_link' => 'Twitter Link',
        'linkedIn' => 'LinkedIn Link',
        'bank' => 'Bank',
        'bank_account_holder_name' => 'Bank Account Holder Name',
        'bank_account_number' => 'Bank Account Number',
        'iban' => 'IBAN',
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'mobile' => 'Mobile',
        'password' => 'Password',
        'address' => 'Address',
        'commercial_register' => 'Commercial Register',
    ],

    'regex' => [
        'phone' => 'Phone number must start with +9665 and be 12 digits long',
        'mobile' => 'Mobile number must start with +9665 and be 12 digits long',
        'password' => 'Password must contain at least one uppercase letter and one special character',
    ],

    'messages' => [
        'required' => 'The :attribute field is required',
        'string' => 'The :attribute must be a string',
        'max' => [
            'string' => 'The :attribute must not exceed :max characters',
            'file' => 'The :attribute must not exceed :max kilobytes',
        ],
        'min' => [
            'string' => 'The :attribute must be at least :min characters',
        ],
        'email' => 'The :attribute must be a valid email address',
        'unique' => 'The :attribute has already been taken',
        'confirmed' => 'The :attribute confirmation does not match',
        'numeric' => 'The :attribute must be a number',
        'between' => 'The :attribute must be between :min and :max',
        'exists' => 'The selected :attribute is invalid',
        'array' => 'The :attribute must be an array',
        'url' => 'The :attribute must be a valid URL',
        'file' => 'The :attribute must be a file',
        'mimes' => 'The :attribute must be a file of type: :values',
    ],
    'reasons'=>[
        'complete_profile'=>'The account must be complete.',
        'bank_info'=>'The account must be linked to bank information.',
    ],

    'services' => [
        'type_required' => 'Please select a service type.',
        'type_invalid' => 'The selected service type is invalid.',
        'name_required' => 'Please enter a service name.',
        'name_max' => 'Service name cannot exceed 255 characters.',
        'description_max' => 'Description cannot exceed 1000 characters.',
        'price_required' => 'Please enter a service price.',
        'price_numeric' => 'Price must be a number.',
        'price_min' => 'Price cannot be negative.',
        'price_max' => 'Price cannot exceed 99,999.99.',
        'sessions_required' => 'Please enter the number of available sessions per day.',
        'sessions_integer' => 'Available sessions must be a whole number.',
        'sessions_min' => 'Available sessions must be at least 1.',
        'sessions_max' => 'Available sessions cannot exceed 100 per day.',
        'duration_required' => 'Please select a service duration.',
        'duration_invalid' => 'The selected duration is invalid.',
        'concurrent_required' => 'Please enter the maximum number of concurrent requests.',
        'concurrent_integer' => 'Maximum concurrent requests must be a whole number.',
        'concurrent_min' => 'Maximum concurrent requests must be at least 1.',
        'concurrent_max' => 'Maximum concurrent requests cannot exceed 10.',
        'location_required' => 'Please select a service location.',
        'location_invalid' => 'The selected location is invalid.',
        'branches_required' => 'Please select at least one branch.',
        'branches_min' => 'Please select at least one branch.',
        'branch_not_found' => 'One or more selected branches do not exist.',
        'images_max' => 'You cannot upload more than 5 images.',
        'image_invalid' => 'Please upload valid images only.',
        'image_type' => 'Images must be in JPG or PNG format.',
        'image_size' => 'Each image must not exceed 5MB.',
        'image_dimensions' => 'Images must be between 100x100 and 2000x2000 pixels.',
        'image_not_found' => 'One or more images to be removed were not found.',
    ],
];