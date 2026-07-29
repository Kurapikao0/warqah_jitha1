<?php

namespace App\Services;


use Illuminate\Support\Facades\DB;
use App\Models\ProductCustomizationRequest;
use App\Repositories\Contracts\CustomizationRepositoryInterface;



class CustomizationService
{


public function __construct(
    protected CustomizationRepositoryInterface $repository
)
{}




public function all()
{

return $this->repository->getAll();

}





public function find($id)
{

return $this->repository->findById($id);

}





public function create(array $data)
{


return DB::transaction(function() use($data){


    return $this->repository
        ->create($data);


});


}






public function updateStatus(
ProductCustomizationRequest $request,
array $data
)
{


return DB::transaction(function()
use($request,$data){


return $this->repository
    ->update(
        $request,
        $data
    );


});


}






public function delete(
ProductCustomizationRequest $request
)
{


return DB::transaction(function()
use($request){


return $this->repository
    ->delete($request);


});


}



}