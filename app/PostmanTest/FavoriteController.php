<?php

namespace App\Http\Controllers\API\Customer;


use App\Http\Controllers\Controller;
use App\Services\FavoriteService;



class FavoriteController extends Controller
{


public function __construct(
protected FavoriteService $service
)
{}



public function toggle($productId)
{


$status =
$this->service
->toggle(
auth()->id(),
$productId
);



return response()->json([

'favorite'=>$status

]);


}



public function index()
{


return $this->service
->all(
auth()->id()
);


}


}