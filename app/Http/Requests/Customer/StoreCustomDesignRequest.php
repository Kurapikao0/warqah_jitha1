<?php

namespace App\Http\Requests\Customer;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomDesignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('customer')->check() && auth('customer')->user() instanceof Customer;
    }

    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:4000'],
        ];
    }
}
