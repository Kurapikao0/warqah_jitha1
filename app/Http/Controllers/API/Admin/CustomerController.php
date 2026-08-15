<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Customer\StoreCustomerRequest;
use App\Http\Requests\Admin\Customer\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerService $customerService
    ) {
    }

    /**
     * Display customers list
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Customer::class);

        $filters = [
            'search' => $request->search,
            'category' => $request->category,
            'verified' => $request->verified,
            'sort_by' => $request->sort_by,
            'sort_direction' => $request->sort_direction,
            'per_page' => $request->per_page ?? 15
        ];

        $customers = $this->customerService->getCustomers($filters);

        return response()->json([
            'success' => true,
            'message' => 'Customers fetched successfully',
            'data' => CustomerResource::collection($customers),
            'errors' => null
        ]);
    }

    /**
     * Store new customer
     */
    public function store(StoreCustomerRequest $request)
    {
        $this->authorize('create', Customer::class);


            $validated = $request->validated();

            // ✅ Service سيتعامل مع تشفير password إلى password_hash
            $customer = $this->customerService->createCustomer($validated);

            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'data' => new CustomerResource($customer),
                'errors' => null
            ], 201);
    }

    /**
     * Show customer details
     */
    public function show(Customer $customer)
    {
        $this->authorize('view', $customer);

        return response()->json([
            'success' => true,
            'message' => 'Customer details',
            'data' => new CustomerResource($customer),
            'errors' => null
        ]);
    }

    /**
     * Update customer
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $this->authorize('update', $customer);

            $validated = $request->validated();

            // ✅ Service سيتعامل مع تشفير password إلى password_hash
            $customer = $this->customerService->updateCustomer($customer, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully',
                'data' => new CustomerResource($customer),
                'errors' => null
            ]);
    }

    /**
     * Soft delete customer
     */
    public function destroy(Customer $customer)
    {
        $this->authorize('delete', $customer);

        $this->customerService->deleteCustomer($customer);

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully',
            'data' => null,
            'errors' => null
        ]);
    }

    /**
     * Restore deleted customer
     */
    public function restore(Customer $customer)
    {
        $this->authorize('restore', $customer);

        $this->customerService->restoreCustomer($customer);

        return response()->json([
            'success' => true,
            'message' => 'Customer restored successfully',
            'data' => null,
            'errors' => null
        ]);
    }

    /**
     * Change customer status
     */
    public function changeStatus(Request $request, Customer $customer)
    {
        $this->authorize('changeStatus', $customer);

        $customer = $this->customerService->changeStatus($customer, $request->status);

        return response()->json([
            'success' => true,
            'message' => 'Customer status updated',
            'data' => new CustomerResource($customer),
            'errors' => null
        ]);
    }

    /**
     * Verify customer
     */
    public function verify(Customer $customer)
    {
        $this->authorize('verify', $customer);

        $customer = $this->customerService->verifyCustomer($customer);

        return response()->json([
            'success' => true,
            'message' => 'Customer verified successfully',
            'data' => new CustomerResource($customer),
            'errors' => null
        ]);
    }
}
