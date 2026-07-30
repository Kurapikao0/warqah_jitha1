<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreAddressRequest;
use App\Http\Requests\Customer\UpdateAddressRequest;
use App\Models\Address;
use App\Models\Customer;
use App\Services\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct(
        protected AddressService $addressService
    ) {
    }


    /**
     * Display customer addresses
     */
    public function index(Request $request): JsonResponse
    {
        /** @var Customer $customer */
        $customer = $request->user();

        return response()->json([
            'data' => $this->addressService
                ->getCustomerAddresses($customer)
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

        $address = $this->addressService->create(
            $customer,
            $request->validated()
        );

        return response()->json([
            'message' => 'Address created successfully',
            'data' => $address
        ], 201);
    }


    /**
     * Show address
     */
    public function show(
        Request $request,
        Address $address
    ): JsonResponse {

        $this->authorizeAddress($request, $address);

        return response()->json([
            'data' => $address
        ]);
    }


    /**
     * Update address
     */
    public function update(
        UpdateAddressRequest $request,
        Address $address
    ): JsonResponse {

        $this->authorizeAddress($request, $address);

        $address = $this->addressService->update(
            $address,
            $request->validated()
        );

        return response()->json([
            'message' => 'Address updated successfully',
            'data' => $address
        ]);
    }


    /**
     * Delete address
     */
    public function destroy(
        Request $request,
        Address $address
    ): JsonResponse {

        $this->authorizeAddress($request, $address);

        $this->addressService->delete($address);

        return response()->json([
            'message' => 'Address deleted successfully'
        ]);
    }

    /**
 * Set default address
 */
public function setDefault(
    Request $request,
    Address $address
): JsonResponse {

    $this->authorizeAddress($request, $address);

    $address = $this->addressService
        ->setDefault($address);

    return response()->json([
        'message' => 'Default address updated successfully',
        'data' => $address
    ]);
}
    protected function authorizeAddress(
        Request $request,
        Address $address
    ): void {

        abort_if(
            $address->customer_id !== $request->user()->id,
            403,
            'Unauthorized address access'
        );
    }

    
}