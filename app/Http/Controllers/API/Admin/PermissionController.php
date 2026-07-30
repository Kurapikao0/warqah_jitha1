<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Permission\StorePermissionRequest;
use App\Http\Requests\Admin\Permission\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class PermissionController extends Controller
{

    public function __construct(
        protected PermissionService $permissionService
    ) {
    }


    public function index(): AnonymousResourceCollection
    {
        $this->authorize(
            'viewAny',
            Permission::class
        );


        return PermissionResource::collection(
            $this->permissionService->paginate()
        );
    }



    public function store(
        StorePermissionRequest $request
    ): JsonResponse {


        $this->authorize(
            'create',
            Permission::class
        );


        $permission =
            $this->permissionService
                ->store($request->validated());


        return response()->json([
            'success'=>true,
            'message'=>'Permission created successfully.',
            'data'=>new PermissionResource($permission)
        ], Response::HTTP_CREATED);
    }



    public function show(
        Permission $permission
    ): JsonResponse {


        $this->authorize(
            'view',
            $permission
        );


        return response()->json([
            'success'=>true,
            'data'=>new PermissionResource(
                $permission
            )
        ]);
    }



    public function update(
        UpdatePermissionRequest $request,
        Permission $permission
    ): JsonResponse {


        $this->authorize(
            'update',
            $permission
        );


        $permission =
            $this->permissionService
                ->update(
                    $permission,
                    $request->validated()
                );


        return response()->json([
            'success'=>true,
            'message'=>'Permission updated successfully.',
            'data'=>new PermissionResource($permission)
        ]);
    }



    public function destroy(
        Permission $permission
    ): JsonResponse {


        $this->authorize(
            'delete',
            $permission
        );


        $this->permissionService
            ->delete($permission);


        return response()->json([
            'success'=>true,
            'message'=>'Permission deleted successfully.'
        ]);
    }
}