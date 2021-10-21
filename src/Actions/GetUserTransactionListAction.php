<?php

namespace Devolon\Payment\Actions;

use Devolon\Common\Tools\Setting;
use Devolon\Payment\Services\GetUserTransactionsService;
use Illuminate\Pagination\LengthAwarePaginator;

class GetUserTransactionListAction
{
    public function __construct(private GetUserTransactionsService $getUserTransactionsService)
    {
    }

    public function __invoke(int $userId, int $perPage = Setting::PAGE_SIZE): LengthAwarePaginator
    {
        return ($this->getUserTransactionsService)($userId, $perPage);
    }
}
