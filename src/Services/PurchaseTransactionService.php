<?php

namespace Devolon\Payment\Services;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\DTOs\PurchaseResultDTO;

class PurchaseTransactionService
{
    public function __construct(private PaymentGatewayDiscoveryService $discoverPaymentGatewayService)
    {
    }

    public function __invoke(Transaction $transaction): PurchaseResultDTO
    {
        $paymentGateway = $this->discoverPaymentGatewayService->get($transaction->payment_method);

        return $paymentGateway->purchase($transaction);
    }
}
