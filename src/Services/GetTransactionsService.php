<?php

namespace Devolon\Payment\Services;

use Devolon\Common\Tools\Setting;
use Devolon\Payment\Repositories\TransactionRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class GetTransactionsService
{
    public function __construct(private TransactionRepository $transactionRepository)
    {
    }

    public function __invoke(int $perPage = Setting::PAGE_SIZE): LengthAwarePaginator
    {
        return $this->transactionRepository->getPaginated($perPage);
    }
}