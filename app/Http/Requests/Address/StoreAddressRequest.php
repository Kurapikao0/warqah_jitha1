<?php

namespace App\Http\Requests\Address;

use App\Rules\YemenPhoneRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }



    public function rules(): array
    {
        return [

            'recipient_name' => [
                'required',
                'string',
                'max:255'
            ],


            'phone' => [
                'required',
                'string',
                'max:20',
                new YemenPhoneRule()
            ],


            'country' => [
                'required',
                'string',
                'max:100'
            ],


            'city' => [
                'required',
                'string',
                'max:100'
            ],


            'district' => [
                'required',
                'string',
                'max:100'
            ],


            'street' => [
                'required',
                'string',
                'max:255'
            ],


            'postal_code' => [
                'nullable',
                'string',
                'max:20'
            ],


            'is_default' => [
                'nullable',
                'boolean'
            ],

        ];
    }



    public function messages(): array
    {

        return [

            'recipient_name.required' =>
                'Recipient name is required',


            'phone.required' =>
                'Phone number is required',


            'country.required' =>
                'Country is required',


            'city.required' =>
                'City is required',


            'district.required' =>
                'District is required',


            'street.required' =>
                'Street is required',

        ];

    }

}