<?php

namespace App\Http\Requests\Api;

use App\Models\ContactUsMessage;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\CustomResponse;

class ContactUsRequest extends FormRequest
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
        return ContactUsMessage::$rules;
    }
}
