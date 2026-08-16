<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUser\StoreAdminUserRequest;
use App\Http\Requests\Admin\AdminUser\UpdateAdminUserRequest;
use App\Http\Resources\AdminUserResource;
use App\Models\AdminUser;
use App\Services\AdminUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class AdminUserController extends Controller
{
    public function __construct(
        protected AdminUserService $service
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $this->authorize(
            'viewAny',
            AdminUser::class
        );

        return AdminUserResource::collection(
            $this->service->paginate()
        );
    }

    public function store(
        StoreAdminUserRequest $request
    ): JsonResponse {

        $this->authorize(
            'create',
            AdminUser::class
        );

        $adminUser =
            $this->service->create(
                $request->validated()
            );

        return response()->json([

            'success' => true,

            'message' => 'Admin user created successfully.',

            'data' => new AdminUserResource(
                $adminUser->load('role')
            ),

        ], Response::HTTP_CREATED);
    }

    public function show(
        AdminUser $adminUser
    ): JsonResponse {

        $this->authorize(
            'view',
            $adminUser
        );

        return response()->json([

            'success' => true,

            'data' => new AdminUserResource(
                $adminUser->load('role')
            ),

        ]);
    }

    public function update(
        UpdateAdminUserRequest $request,
        AdminUser $adminUser
    ): JsonResponse {

        $this->authorize(
            'update',
            $adminUser
        );

        $adminUser =
            $this->service->update(
                $adminUser,
                $request->validated()
            );

        return response()->json([

            'success' => true,

            'message' => 'Admin user updated successfully.',

            'data' => new AdminUserResource(
                $adminUser->load('role')
            ),

        ]);
    }

    public function destroy(
        AdminUser $adminUser
    ): JsonResponse {

        $this->authorize(
            'delete',
            $adminUser
        );

        $this->service->delete(
            $adminUser
        );

        return response()->json([

            'success' => true,

            'message' => 'Admin user deleted successfully.',

        ]);
    }
}
