<?php

namespace App\Http\Controllers\API\Customer;


use App\Models\ProductCustomizationRequest;
use App\Http\Controllers\Controller;
use App\Services\CustomizationService;
use App\Http\Resources\ProductCustomizationResource;
use App\Http\Requests\Customization\StoreCustomizationRequest;



class ProductCustomizationController extends Controller
{


public function __construct(
protected CustomizationService $service
)
{}




public function index()
{


$customerId =
auth()->id();



return ProductCustomizationResource::collection(

ProductCustomizationRequest::where(
'customer_id',
$customerId
)
->paginate()

);


}





public function store(
StoreCustomizationRequest $request
)
{


$data=$request->validated();


$data['customer_id']=auth()->id();


$customization=
$this->service->create($data);



return new ProductCustomizationResource(
$customization
);


}





public function show($id)
{
    $customization = $this->service->find($id);

    $this->authorize('view', $customization);

    return new ProductCustomizationResource(
        $customization
    );
}



}