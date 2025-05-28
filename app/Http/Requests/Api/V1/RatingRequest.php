<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class RatingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'sometimes|string|max:500',
            'gold_piece_id' => [
                'required',
                'exists:gold_pieces,id',
                Rule::unique('ratings')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })->ignore($this->route('rating')),
            ],
        ];
    }
} 