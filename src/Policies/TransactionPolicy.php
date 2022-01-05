<?php

namespace Devolon\Payment\Policies;

use Devolon\Payment\Models\Transaction;

class TransactionPolicy {
    public function refund($user, Transaction $transaction): bool {
        return $transaction->status === Transaction::STATUS_DONE;
    }
}
