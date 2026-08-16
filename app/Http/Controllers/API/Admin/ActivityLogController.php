<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ActivityLogController extends Controller
{
    public function __construct(
        protected ActivityLogService $service
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = [
            'search' => $request->query('search'),
            'action' => $request->query('action'),
            'date_from' => $request->query('date_from'),
            'date_to' => $request->query('date_to'),
        ];

        $logs = $this->service->paginate($filters);

        return ActivityLogResource::collection($logs);
    }

    public function show(
        ActivityLog $activityLog
    ): ActivityLogResource {
        $log = $this->service->show($activityLog);

        return new ActivityLogResource($log);
    }
}
