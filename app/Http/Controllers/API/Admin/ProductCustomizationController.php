<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customization\UpdateCustomizationStatusRequest;
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
}
