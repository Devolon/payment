<?php

namespace Devolon\Payment\Actions;

use Devolon\Payment\DTOs\UpdateTransactionDTO;
use Devolon\Payment\Exceptions\TransactionVerificationFailed;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Services\MakeTransactionDoneService;
use Devolon\Payment\Services\MakeTransactionFailedService;
use Illuminate\Support\Facades\Log;

class UpdateTransactionAction
{
    public function __construct(
        private MakeTransactionDoneService $makeTransactionDoneService,
        private MakeTransactionFailedService $makeTransactionFailedService,
    ) {
    }

    public function __invoke(Transaction $transaction, UpdateTransactionDTO $updateTransactionDTO): Transaction
    {
        try {
            return match ($updateTransactionDTO->status) {
                Transaction::STATUS_DONE => ($this->makeTransactionDoneService)(
                    $transaction,
                    $updateTransactionDTO->payment_method_data,
                ),
                Transaction::STATUS_FAILED => ($this->makeTransactionFailedService)(
                    $transaction,
                    $updateTransactionDTO->payment_method_data,
                )
            };
        } catch (TransactionVerificationFailed $e) {
            Log::error($e->getMessage());

            return ($this->makeTransactionFailedService)(
                $transaction,
                $updateTransactionDTO->payment_method_data,
            );
        }
    }
}
