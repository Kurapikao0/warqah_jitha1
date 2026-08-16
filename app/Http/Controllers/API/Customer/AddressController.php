<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Customer;
use App\Services\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct(
        protected AddressService $addressService
    ) {}

    /**
     * Display customer addresses
     */
    public function index(
        Request $request
    ): JsonResponse {

        /** @var Customer $customer */
        $customer = $request->user();

        $addresses =
            $this->addressService
                ->getCustomerAddresses($customer);

        return response()->json([

            'success' => true,

            'message' => 'Addresses retrieved successfully',

            'data' => AddressResource::collection(
                $addresses
            ),

            'errors' => null,

        ]);

    }

    /**
     * Store new address
     */
    public function store(
        StoreAddressRequest $request
    ): JsonResponse {

        /** @var Customer $customer */
        $customer = $request->user();

        $address =
            $this->addressService
                ->create(
                    $customer,
                    $request->validated()
                );

        return response()->json([

            'success' => true,

            'message' => 'Address created successfully',

            'data' => new AddressResource(
                $address
            ),

            'errors' => null,

        ], 201);

    }

    /**
     * Show address
     */
    public function show(
        Address $address
    ): JsonResponse {

        $this->authorize(
            'view',
            $address
        );

        return response()->json([

            'success' => true,

            'message' => 'Address retrieved successfully',

            'data' => new AddressResource(
                $address
            ),

            'errors' => null,

        ]);

    }

    /**
     * Update address
     */
    public function update(
        UpdateAddressRequest $request,
        Address $address
    ): JsonResponse {

        $this->authorize(
            'update',
            $address
        );

        $address =
            $this->addressService
                ->update(
                    $address,
                    $request->validated()
                );

        return response()->json([

            'success' => true,

            'message' => 'Address updated successfully',

            'data' => new AddressResource(
                $address
            ),

            'errors' => null,

        ]);

    }

    /**
     * Delete address
     */
    public function destroy(
        Address $address
    ): JsonResponse {

        $this->authorize(
            'delete',
            $address
        );

        $this->addressService
            ->delete($address);

        return response()->json([

            'success' => true,

            'message' => 'Address deleted successfully',

            'data' => null,

            'errors' => null,

        ]);

    }

    /**
     * Set default address
     */
    public function setDefault(
        Address $address
    ): JsonResponse {

        $this->authorize(
            'setDefault',
            $address
        );

        $address =
            $this->addressService
                ->setDefault($address);

        return response()->json([

            'success' => true,

            'message' => 'Default address updated successfully',

            'data' => new AddressResource(
                $address
            ),

            'errors' => null,

        ]);

    }
}
