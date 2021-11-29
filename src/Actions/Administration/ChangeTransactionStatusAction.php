<?php

namespace Devolon\Payment\Actions\Administration;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Services\RefundTransactionService;
use LogicException;

class ChangeTransactionStatusAction
{
    public function __construct(private RefundTransactionService $refundTransactionService)
    {
    }

    public function __invoke(Transaction $transaction, string $status)
    {
        match ($status) {
            Transaction::STATUS_REFUNDED => ($this->refundTransactionService)($transaction, $status),
            default => throw new LogicException(sprintf("Status %s is not valid to update.", $status)),
        };
    }
}