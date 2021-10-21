<?php

namespace Devolon\Payment\Services;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Events\TransactionFailed;
use Devolon\Payment\Repositories\TransactionRepository;

class MakeTransactionFailedService
{
    public function __construct(private TransactionRepository $transactionRepository)
    {
    }

    public function __invoke(Transaction $transaction, array $paymentMethodData): Transaction
    {
        $this->transactionRepository->update([
            'status' => Transaction::STATUS_FAILED,
        ], $transaction);
        TransactionFailed::dispatch($transaction, $paymentMethodData);

        return $transaction;
    }
}
