<?php

namespace App\Http\Requests\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait CustomResponse
{

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = [
            'message' => $errors->first(),
            'errors' => $errors->messages(),
        ];

        throw new HttpResponseException(response()->json($response, 422));
    }
}
