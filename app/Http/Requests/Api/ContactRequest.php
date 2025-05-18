<?php

namespace App\Http\Requests\Api;

use App\Enums\ContactTypeEnum;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\CustomResponse;

class ContactRequest extends FormRequest
{

    use CustomResponse;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:5|max:200',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'message' => 'required|max:500',
            'topic' => 'nullable|max:200',
            'city' => 'nullable|max:200',
            'country' => 'nullable|max:200',
            'subject' => 'nullable|max:200',
            'subject' => 'nullable|max:200',
            'subject' => 'nullable|max:200',
            'user_id' => 'nullable|exists:users,id',
            'company_id' => 'nullable|exists:companies,id',
            'time' => 'nullable|date_format:H:i',
            'date' => 'nullable|date_format:Y-m-d|after:today',
            'contact_type' => ['required', 'in:'.implode(",", ContactTypeEnum::values())],
        ];
    }
}
