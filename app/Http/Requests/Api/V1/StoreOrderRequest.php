<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gold_piece_id' => [
                'required',
                'exists:gold_pieces,id'
            ],
            'start_date' => [
                'required',
                'date',
                'after_or_equal:today'
            ],
            'end_date' => [
                'required', 
                'date',
                'after:start_date'
            ],
            'total_price' => [
                'required',
                'numeric',
                'min:0'
            ]
        ];
    }
}
