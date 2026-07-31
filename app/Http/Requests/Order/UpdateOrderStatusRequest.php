<?php

namespace App\Http\Requests\Order;


use Illuminate\Foundation\Http\FormRequest;



class UpdateOrderStatusRequest extends FormRequest
{


    public function authorize(): bool
    {

        return auth()->check();

    }





    public function rules(): array
    {


        return [

            'status'=>
            [
                'required',
                'string',
                'in:
                received,
                confirmed,
                in_production,
                ready,
                delivered,
                cancelled'
            ],


            'note'=>
            [
                'nullable',
                'string'
            ]


        ];


    }



}