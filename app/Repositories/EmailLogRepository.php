<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\EmailLog;
use App\Repositories\Contracts\EmailLogRepositoryInterface;

final class EmailLogRepository implements EmailLogRepositoryInterface
{
    public function create(array $data): EmailLog
    {
        return EmailLog::create($data);
    }

    public function update(
        EmailLog $emailLog,
        array $data
    ): bool {
        return $emailLog->update($data);
    }

    public function findById(int $id): ?EmailLog
    {
        return EmailLog::find($id);
    }

    public function findByIdWithOwner(int $id): ?EmailLog
    {
        return EmailLog::query()
            ->with('owner')
            ->find($id);
    }
}
