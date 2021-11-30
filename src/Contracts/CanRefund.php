<?php

namespace Devolon\Payment\Contracts;

use Devolon\Payment\Models\Transaction;

interface CanRefund
{
    public function refund(Transaction $transaction): bool;
}