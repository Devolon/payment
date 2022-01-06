<?php

namespace Devolon\Payment\Services;

use Devolon\Common\Tools\Setting;
use Devolon\Payment\Repositories\TransactionRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class GetUserTransactionsService
{
    public function __construct(private TransactionRepository $transactionRepository)
    {
    }

    public function __invoke(int $userId, int $perPage = Setting::PAGE_SIZE, ?array $statuses = null): LengthAwarePaginator
    {
        return $this->transactionRepository->getPaginatedForUser($userId, $perPage, $statuses);
    }
}
