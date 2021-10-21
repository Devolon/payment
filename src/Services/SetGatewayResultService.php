<?php

namespace Devolon\Payment\Services;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Repositories\TransactionRepository;

class SetGatewayResultService
{
    public function __construct(private TransactionRepository $transactionRepository)
    {
    }

    public function __invoke(Transaction $transaction, string $key, mixed $value): Transaction
    {
        $gatewayResults = $transaction->gateway_results;
        $gatewayResults[$key] = $value;

        return $this->transactionRepository->update([
            'gateway_results' => $gatewayResults
        ], $transaction);
    }
}
