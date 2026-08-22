<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customization\StoreAdminCustomizationRequest;
use App\Http\Requests\Customization\UpdateCustomizationStatusRequest;
use Illuminate\Support\Str;
use App\Http\Resources\ProductCustomizationResource;
use App\Models\ProductCustomizationRequest;
use App\Services\CustomizationService;

class ProductCustomizationController extends Controller
{
    public function __construct(
        protected CustomizationService $service
    ) {}

    public function index()
    {

        return ProductCustomizationResource::collection(

            $this->service->all()

        );

    }

    public function store(StoreAdminCustomizationRequest $request)
    {
        $data = $request->validated();
        $data['request_code'] = $this->generateRequestCode();
        $data['status'] = 'pending_approval';

        $customization = $this->service->create($data);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء طلب التخصيص بنجاح.',
            'data' => new ProductCustomizationResource($customization->load(['customer', 'baseProduct', 'color', 'designPattern'])),
        ], 201);
    }

    protected function generateRequestCode(): string
    {
        do {
            $code = 'REQ-' . strtoupper(Str::random(5));
        } while (ProductCustomizationRequest::where('request_code', $code)->exists());

        return $code;
    }

    public function show($id)
    {

        return new ProductCustomizationResource(

            $this->service->find($id)

        );

    }

    public function updateStatus(
        UpdateCustomizationStatusRequest $request,
        ProductCustomizationRequest $customization
    ) {
        $this->service->updateStatus(
            $customization,
            $request->validated()
        );

        return response()->json([
            'message' => 'Customization status updated',
        ]);
    }

    public function destroy(ProductCustomizationRequest $customization)
    {
        $this->service->delete($customization);

        return response()->json([
            'message' => 'Customization deleted',
        ]);
    }
}
