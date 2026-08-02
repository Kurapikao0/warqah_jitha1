<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [

            'rating' => [
                'sometimes',
                'integer',
                'min:1',
                'max:5'
            ],

            'comment' => [
                'nullable',
                'string'
            ],

            'status' => [
                'sometimes',
                'string'
            ],

            'admin_reply' => [
                'nullable',
                'string'
            ],

            'admin_reply_at' => [
                'nullable',
                'date'
            ],
        ];
    }
}