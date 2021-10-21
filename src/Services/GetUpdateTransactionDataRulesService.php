<?php

namespace Devolon\Payment\Services;

use Devolon\Payment\Contracts\HasUpdateTransactionData;
use Devolon\Payment\Models\Transaction;

class GetUpdateTransactionDataRulesService
{
    public function __construct(
        private PaymentGatewayDiscoveryService $paymentGatewayDiscoveryService
    ) {
    }

    public function __invoke(Transaction $transaction, string $newStatus): array
    {
        $gateway = ($this->paymentGatewayDiscoveryService)->get($transaction->payment_method);

        if (!$gateway instanceof HasUpdateTransactionData) {
            return [];
        }

        return $gateway->updateTransactionDataRules($newStatus);
    }
}
