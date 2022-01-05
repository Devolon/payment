<?php

namespace Devolon\Payment\Actions\Administration;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Services\MakeTransactionRefundedService;

class RefundTransactionAction
{
    public function __construct(
        private MakeTransactionRefundedService $makeTransactionRefundedService,
    ) {
    }

    public function __invoke(Transaction $transaction): Transaction
    {
        return ($this->makeTransactionRefundedService)($transaction);
    }
}
