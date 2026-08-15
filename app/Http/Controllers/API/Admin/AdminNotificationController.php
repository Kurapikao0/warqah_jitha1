<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminNotificationResource;
use App\Models\AdminNotification;
use App\Models\AdminUser;
use App\Services\AdminNotificationService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

class AdminNotificationController extends Controller
{

    public function __construct(
        protected AdminNotificationService $service
    ) {
    }



    public function index(
        AdminUser $adminUser
    ): AnonymousResourceCollection {
        // TODO: عزل افتراضي آمن — قد يحتاج تعديل مستقبلًا إذا احتاج Super Admin رؤية إشعارات كل المدراء، يحتاج قرار من صاحبة المشروع
        abort_if(
            $adminUser->id !== auth('admin')->id(),
            403
        );

        return AdminNotificationResource::collection(
            $this->service->paginate(
                $adminUser
            )
        );
    }



    public function read(
        AdminNotification $notification
    ): JsonResponse {
        // TODO: عزل افتراضي آمن — قد يحتاج تعديل مستقبلًا إذا احتاج Super Admin رؤية إشعارات كل المدراء، يحتاج قرار من صاحبة المشروع
        abort_if(
            $notification->admin_user_id !== auth('admin')->id(),
            403
        );

        $notification =
            $this->service->markAsRead(
                $notification
            );


        return response()->json([

            'success'=>true,

            'message'=>'Notification marked as read.',

            'data'=>new AdminNotificationResource(
                $notification
            )

        ]);
    }
}