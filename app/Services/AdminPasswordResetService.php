<?php

namespace App\Services;

use App\Models\AdminUser;
use App\Models\AdminPasswordReset;
use Illuminate\Support\Str;

class AdminPasswordResetService
{

    public function create(
        AdminUser $adminUser
    ): AdminPasswordReset {


        return AdminPasswordReset::create([

            'admin_user_id'=>$adminUser->id,

            'code_or_token'=>Str::random(64),

            'contact_value'=>$adminUser->email,

            'expires_at'=>now()->addMinutes(15),

        ]);
    }



    public function consume(
        AdminPasswordReset $reset
    ): bool {


        if(
            $reset->consumed_at ||
            now()->greaterThan(
                $reset->expires_at
            )
        ){
            return false;
        }


        $reset->update([

            'consumed_at'=>now()

        ]);


        return true;
    }
}