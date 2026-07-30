<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminPasswordResetResource;
use App\Models\AdminPasswordReset;
use App\Models\AdminUser;
use App\Services\AdminPasswordResetService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AdminPasswordResetController extends Controller
{

    public function __construct(
        protected AdminPasswordResetService $service
    ) {
    }



    public function store(
        AdminUser $adminUser
    ): JsonResponse {


        $this->authorize(
            'update',
            $adminUser
        );


        $reset =
            $this->service->create(
                $adminUser
            );


        return response()->json([

            'success'=>true,

            'message'=>'Password reset code generated.',

            'data'=>new AdminPasswordResetResource(
                $reset
            )

        ], Response::HTTP_CREATED);
    }




    public function destroy(
        AdminPasswordReset $reset
    ): JsonResponse {


        $this->authorize(
            'update',
            $reset->adminUser
        );


        $success =
            $this->service->consume(
                $reset
            );


        return response()->json([

            'success'=>$success,

            'message'=>$success
                ? 'Reset token consumed.'
                : 'Reset token invalid.'

        ]);
    }
}