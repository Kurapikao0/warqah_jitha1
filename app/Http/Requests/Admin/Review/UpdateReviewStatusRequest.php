<?php

namespace App\Http\Requests\Admin\Review;

use App\Enums\ReviewStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReviewStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check() || auth()->check();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::enum(ReviewStatus::class)],
        ];
    }
}
