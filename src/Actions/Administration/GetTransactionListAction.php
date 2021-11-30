<?php

namespace Devolon\Payment\Actions\Administration;

use Devolon\Common\Tools\Setting;
use Devolon\Payment\Services\GetTransactionsService;
use Illuminate\Pagination\LengthAwarePaginator;

class GetTransactionListAction
{
    public function __construct(private GetTransactionsService $getTransactionsService)
    {
    }

    public function __invoke(int $perPage = Setting::PAGE_SIZE): LengthAwarePaginator
    {
        return ($this->getTransactionsService)($perPage);
    }
}