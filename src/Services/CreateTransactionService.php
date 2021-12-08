<?php

namespace Devolon\Payment\Services;

use Devolon\Payment\Contracts\HasPrice;
use Devolon\Payment\DTOs\CreateTransactionDTO;
use Devolon\Payment\Exceptions\TransactionNotSupported;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Payment;
use Devolon\Payment\Repositories\TransactionRepository;
use InvalidArgumentException;

class CreateTransactionService
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private ProductTypeDiscoveryService $productTypeDiscoveryService,
    ) {
    }

    public function __invoke(CreateTransactionDTO $createTransactionDTO): Transaction
    {
        $moneyAmount = $this->calculatePrice($createTransactionDTO);

        return $this->transactionRepository->create(array_merge($createTransactionDTO->toArray(), [
            'status' => Transaction::STATUS_IN_PROCESS,
            'money_amount' => $moneyAmount,
        ]));
    }

    private function calculatePrice(CreateTransactionDTO $createTransactionDTO): float
    {
        try {
            $productType = $this->productTypeDiscoveryService->get($createTransactionDTO->product_type);
        } catch (InvalidArgumentException) {
            $productType = null;
        }

        if (null === $productType) {
            // Support product classes only for backward compatibility
            return $this->calculatePriceWithProductClass($createTransactionDTO);
        }

        if (!$productType->supports($createTransactionDTO)) {
            throw new TransactionNotSupported();
        }

        return $productType->calculatePrice($createTransactionDTO);
    }

    /**
     * @deprecated Will be removed from version v2.0
     */
    private function calculatePriceWithProductClass(CreateTransactionDTO $createTransactionDTO)
    {
        $moneyAmount = 0;
        $productClass = Payment::getProductClass($createTransactionDTO->product_type);
        if (isset(class_implements($productClass)[HasPrice::class])) {
            $moneyAmount = call_user_func(
                [$productClass, 'calculatePrice'],
                $createTransactionDTO,
            );
        }

        return $moneyAmount;
    }
}
