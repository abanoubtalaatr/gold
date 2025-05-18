<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->nafath_verified;
    }

    public function rules()
    {
        $goldPiece = \App\Models\GoldPiece::find($this->gold_piece_id);

        $rules = [
            'gold_piece_id' => 'required|exists:gold_pieces,id',
        ];

        if ($goldPiece && $goldPiece->type === 'for_rent') {
            $rules['start_date'] = 'required|date|after_or_equal:today';
            $rules['end_date'] = 'required|date|after:start_date';
        }

        return $rules;
    }
}