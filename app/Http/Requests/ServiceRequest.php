namespace App\Http\Requests;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization will be handled by ServicePolicy
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(Service::TYPES)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'available_sessions_per_day' => ['required', 'integer', 'min:1', 'max:100'],
            'duration' => ['required', Rule::in(Service::DURATIONS)],
            'max_concurrent_requests' => ['required', 'integer', 'min:1', 'max:10'],
            'location_type' => ['required', Rule::in(Service::LOCATIONS)],
            'is_active' => ['boolean'],
            'branches' => ['required', 'array', 'min:1'],
            'branches.*' => ['required', 'exists:branches,id'],
            'images' => ['sometimes', 'array', 'max:5'],
            'images.*' => [
                'image',
                'mimes:jpeg,png,jpg',
                'max:5120', // 5MB
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
            ],
            'removed_images' => ['sometimes', 'array'],
            'removed_images.*' => ['required', 'exists:service_images,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => __('validation.services.type_required'),
            'type.in' => __('validation.services.type_invalid'),
            'name.required' => __('validation.services.name_required'),
            'name.max' => __('validation.services.name_max'),
            'description.max' => __('validation.services.description_max'),
            'price.required' => __('validation.services.price_required'),
            'price.numeric' => __('validation.services.price_numeric'),
            'price.min' => __('validation.services.price_min'),
            'price.max' => __('validation.services.price_max'),
            'available_sessions_per_day.required' => __('validation.services.sessions_required'),
            'available_sessions_per_day.integer' => __('validation.services.sessions_integer'),
            'available_sessions_per_day.min' => __('validation.services.sessions_min'),
            'available_sessions_per_day.max' => __('validation.services.sessions_max'),
            'duration.required' => __('validation.services.duration_required'),
            'duration.in' => __('validation.services.duration_invalid'),
            'max_concurrent_requests.required' => __('validation.services.concurrent_required'),
            'max_concurrent_requests.integer' => __('validation.services.concurrent_integer'),
            'max_concurrent_requests.min' => __('validation.services.concurrent_min'),
            'max_concurrent_requests.max' => __('validation.services.concurrent_max'),
            'location_type.required' => __('validation.services.location_required'),
            'location_type.in' => __('validation.services.location_invalid'),
            'branches.required' => __('validation.services.branches_required'),
            'branches.min' => __('validation.services.branches_min'),
            'branches.*.exists' => __('validation.services.branch_not_found'),
            'images.max' => __('validation.services.images_max'),
            'images.*.image' => __('validation.services.image_invalid'),
            'images.*.mimes' => __('validation.services.image_type'),
            'images.*.max' => __('validation.services.image_size'),
            'images.*.dimensions' => __('validation.services.image_dimensions'),
            'removed_images.*.exists' => __('validation.services.image_not_found'),
        ];
    }

    public function attributes(): array
    {
        return [
            'type' => __('services.type'),
            'name' => __('services.name'),
            'description' => __('services.description'),
            'price' => __('services.price'),
            'available_sessions_per_day' => __('services.available_sessions'),
            'duration' => __('services.duration'),
            'max_concurrent_requests' => __('services.max_concurrent'),
            'location_type' => __('services.location'),
            'branches' => __('services.branches'),
            'images' => __('services.images'),
            'removed_images' => __('services.removed_images'),
        ];
    }
} 