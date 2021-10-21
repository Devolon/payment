<?php

namespace Devolon\Payment\Services;

use Devolon\Payment\Exceptions\TransactionVerificationFailed;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Events\TransactionDone;
use Devolon\Payment\Repositories\TransactionRepository;

class MakeTransactionDoneService
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private PaymentGatewayDiscoveryService $paymentGatewayDiscoveryService,
    ) {
    }

    public function __invoke(Transaction $transaction, array $paymentMethodData): Transaction
    {
        $gateway = $this->paymentGatewayDiscoveryService->get($transaction->payment_method);

        $verified = $gateway->verify($transaction, $paymentMethodData);

        if (!$verified) {
            throw new TransactionVerificationFailed();
        }

        $this->transactionRepository->update([
            'status' => Transaction::STATUS_DONE,
        ], $transaction);
        TransactionDone::dispatch($transaction, $paymentMethodData);

        return $transaction;
    }
}
