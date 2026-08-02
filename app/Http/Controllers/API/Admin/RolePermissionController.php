<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RolePermission\StoreRolePermissionRequest;
use App\Http\Resources\RolePermissionResource;
use App\Models\Permission;
use App\Models\Role;
use App\Services\RolePermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class RolePermissionController extends Controller
{

    public function __construct(
        protected RolePermissionService $service
    ) {
    }


    public function index(
        Role $role
    ): AnonymousResourceCollection {


        $this->authorize(
            'view',
            $role
        );


        return RolePermissionResource::collection(
            $this->service->list($role)
        );
    }



    public function store(
        StoreRolePermissionRequest $request,
        Role $role
    ): JsonResponse {


        $this->authorize(
            'update',
            $role
        );


        $permission = Permission::findOrFail(
            $request->permission_id
        );


        $this->service->attach(
            $role,
            $permission
        );


        return response()->json([

            'success'=>true,

            'message'=>'Permission assigned successfully.',

        ], Response::HTTP_CREATED);
    }



    public function destroy(
        Role $role,
        Permission $permission
    ): JsonResponse {


        $this->authorize(
            'update',
            $role
        );


        $this->service->detach(
            $role,
            $permission
        );


        return response()->json([

            'success'=>true,

            'message'=>'Permission removed successfully.',

        ]);
    }
}