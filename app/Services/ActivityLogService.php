<?php

namespace App\Services;

use App\Models\ActivityLog;

class ActivityLogService
{
    public function paginate(array $filters = [])
    {
        $query = ActivityLog::query()->with('adminUser');

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('entity_type', 'like', "%{$search}%")
                    ->orWhereHas('adminUser', function ($adminQuery) use ($search) {
                        $adminQuery->where('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $action = strtolower((string) ($filters['action'] ?? ''));
        if ($action !== '') {
            $query->where(function ($q) use ($action) {
                if ($action === 'created') {
                    $q->where('action', 'like', '%created%');
                } elseif ($action === 'updated') {
                    $q->where('action', 'like', '%updated%');
                } elseif ($action === 'deleted') {
                    $q->where('action', 'like', '%deleted%');
                }
            });
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest('created_at')->paginate();
    }

    public function show(
        ActivityLog $activityLog
    ): ActivityLog {
        return $activityLog->load('adminUser');
    }
}
