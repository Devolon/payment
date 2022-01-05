<?php

namespace Devolon\Payment\Services;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Events\TransactionRefunded;
use Devolon\Payment\Repositories\TransactionRepository;

class MakeTransactionRefundedService
{
    public function __construct(
        private TransactionRepository $transactionRepository,
    ) {
    }

    public function __invoke(Transaction $transaction): Transaction
    {

        $transaction = $this->transactionRepository->update([
            'status' => Transaction::STATUS_REFUNDED,
        ], $transaction);
        TransactionRefunded::dispatch($transaction);
        return $transaction;
    }
}
