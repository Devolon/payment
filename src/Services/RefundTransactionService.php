<?php

namespace Devolon\Payment\Services;

use Devolon\Payment\Contracts\CanRefund;
use Devolon\Payment\Events\TransactionRefunded;
use Devolon\Payment\Exceptions\RefundTransactionFailed;
use Devolon\Payment\Exceptions\TransactionCanNotGetRefunded;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Repositories\TransactionRepository;

class RefundTransactionService
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private PaymentGatewayDiscoveryService $paymentGatewayDiscoveryService,
    ) {
    }

    public function __invoke(Transaction $transaction): Transaction
    {
        $gateway = $this->paymentGatewayDiscoveryService->get($transaction->payment_method);

        if (!$gateway instanceof CanRefund) {
            throw new TransactionCanNotGetRefunded();
        }

        /** @var  $gateway CanRefund */
        $verified = $gateway->refund($transaction);

        if (!$verified) {
            throw new RefundTransactionFailed();
        }

        $this->transactionRepository->update([
            'status' => Transaction::STATUS_REFUNDED,
        ], $transaction);
        TransactionRefunded::dispatch($transaction);

        return $transaction;
    }
}