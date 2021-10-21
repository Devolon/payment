<?php

namespace Devolon\Payment\Services;

use Devolon\Payment\Contracts\HasPrice;
use Devolon\Payment\DTOs\CreateTransactionDTO;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Payment;
use Devolon\Payment\Repositories\TransactionRepository;

class CreateTransactionService
{
    public function __construct(private TransactionRepository $transactionRepository)
    {
    }

    public function __invoke(CreateTransactionDTO $createTransactionDTO): Transaction
    {
        $moneyAmount = 0;
        $productClass = Payment::getProductClass($createTransactionDTO->product_type);
        if (isset(class_implements($productClass)[HasPrice::class])) {
            $moneyAmount = call_user_func(
                [$productClass, 'calculatePrice'],
                $createTransactionDTO,
            );
        }

        return $this->transactionRepository->create(array_merge($createTransactionDTO->toArray(), [
            'status' => Transaction::STATUS_IN_PROCESS,
            'money_amount' => $moneyAmount,
        ]));
    }
}
