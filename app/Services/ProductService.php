<?php

namespace App\Services;


use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\ProductRepositoryInterface;



class ProductService
{

    public function __construct(
        protected ProductRepositoryInterface $repository
    )
    {}



    public function getAll()
    {
        return $this->repository->all();
    }




    public function getById($id)
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




    public function update(
        Product $product,
        array $data
    )
    {

        return DB::transaction(function()
        use($product,$data){

            return $this->repository
                ->update(
                    $product,
                    $data
                );

        });

    }





    public function delete(Product $product)
    {

        return DB::transaction(function()
        use($product){

            return $this->repository
                ->delete($product);

        });

    }



}