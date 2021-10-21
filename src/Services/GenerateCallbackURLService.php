<?php

namespace Devolon\Payment\Services;

use Devolon\Payment\Models\Transaction;

class GenerateCallbackURLService
{
    public function __invoke(Transaction $transaction, $status)
    {
        $appBaseUrl = config('payment_highway.app_base_url');
        $path = route('app.payment.callback', ['transaction' => $transaction->id, 'status' => $status], false);

        return "{$appBaseUrl}{$path}";
    }
}
