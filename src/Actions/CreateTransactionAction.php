<?php

namespace Devolon\Payment\Actions;

use Devolon\Payment\DTOs\CreateTransactionDTO;
use Devolon\Payment\DTOs\TransactionResultDTO;
use Devolon\Payment\Services\CreateTransactionService;
use Devolon\Payment\Services\MakeTransactionDoneService;
use Devolon\Payment\Services\PurchaseTransactionService;

class CreateTransactionAction
{
    public function __construct(
        private CreateTransactionService $createTransactionService,
        private PurchaseTransactionService $purchaseTransactionService,
        private MakeTransactionDoneService $makeTransactionDoneService,
    ) {
    }

    public function __invoke(CreateTransactionDTO $createTransactionDTO): TransactionResultDTO
    {
        $transaction = ($this->createTransactionService)($createTransactionDTO);
        $purchaseResultDTO = ($this->purchaseTransactionService)($transaction);

        if (!$purchaseResultDTO->should_redirect) {
            $transaction = ($this->makeTransactionDoneService)(
                $transaction,
                $createTransactionDTO->payment_method_data,
            );
        }

        return TransactionResultDTO::fromArray(array_merge($purchaseResultDTO->toArray(), [
            'transaction' => $transaction,
        ]));
    }
}
