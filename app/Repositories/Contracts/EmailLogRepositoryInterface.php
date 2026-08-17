<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\EmailLog;

interface EmailLogRepositoryInterface
{
    public function create(array $data): EmailLog;

    public function update(
        EmailLog $emailLog,
        array $data
    ): bool;

    public function findById(int $id): ?EmailLog;

    public function findByIdWithOwner(int $id): ?EmailLog;
}
