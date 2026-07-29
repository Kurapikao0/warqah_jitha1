<?php

namespace App\Http\Controllers\API\Admin;


use App\Services\OrderService;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;



class OrderController extends Controller
{


public function __construct(
protected OrderService $service
)
{}



public function index()
{

return OrderResource::collection(

$this->service->all()

);

}




public function show($id)
{

return new OrderResource(

$this->service->find($id)

);

}



}