<?php

namespace Devolon\Payment\Events;

use Devolon\Payment\Models\Transaction;
use Illuminate\Foundation\Events\Dispatchable;

class TransactionDone
{
    use Dispatchable;

    public function __construct(
        public Transaction $transaction,
        public array $paymentMethodData,
    ) {
    }
}
