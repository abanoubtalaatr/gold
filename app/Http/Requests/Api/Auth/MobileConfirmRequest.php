<?php

namespace  App\Http\Requests\Api\Auth;

use App\Models\MobileConfirm;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\CustomResponse;

/**
 * Class MobileConfirmRequest.
 */
class MobileConfirmRequest extends FormRequest
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
        $rules = MobileConfirm::$rules;

        if (isset(request()->guest)) {
            $rules = array_merge($rules, ['mobile' => ['required', 'string', 'regex:/[0-9]/', 'min:9', 'max:9']]);
        } else {
            $rules = array_merge($rules, ['mobile' => [
                'required',
                'string',
                'regex:/[0-9]/',
                'min:9',
                'max:9',
                Rule::unique('users', 'mobile')->where(function ($query) {
                    $q = $query->where('dialling_code', request()->input('dialling_code'));

                    if (Auth::guard('api')->check())
                        return $q->where('id', '<>', Auth::guard('api')->id())->first();

                    return $q->first();
                })
            ]]);
        }

        return $rules;
    }
}
