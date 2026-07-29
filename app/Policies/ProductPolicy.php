<?php

namespace App\Policies;


use App\Models\AdminUser;
use App\Models\Product;



class ProductPolicy
{


public function viewAny(AdminUser $user)
{
    return true;
}




public function view(
AdminUser $user,
Product $product
)
{
    return true;
}




public function create(AdminUser $user)
{

return $user
->role
->permissions()
->where(
'name',
'products.create'
)
->exists();

}





public function update(AdminUser $user)
{

return $user
->role
->permissions()
->where(
'name',
'products.update'
)
->exists();

}





public function delete(AdminUser $user)
{

return $user
->role
->permissions()
->where(
'name',
'products.delete'
)
->exists();

}



}