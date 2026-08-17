<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {

        return [

            'success' => true,

            'data' => CustomerResource::collection(
                $this->collection
            ),

            'meta' => [

                'current_page' => $this->currentPage(),

                'last_page' => $this->lastPage(),

                'per_page' => $this->perPage(),

                'total' => $this->total(),

            ],

        ];

    }
}
