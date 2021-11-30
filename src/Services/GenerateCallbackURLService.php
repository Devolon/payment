<?php

namespace Devolon\Payment\Services;

use Devolon\Payment\Models\Transaction;

class GenerateCallbackURLService
{
    public function __invoke(Transaction $transaction, $status)
    {
        // TODO: ＼(｀0´)／ stop using payment_highway config here
        $appBaseUrl = config('payment_highway.app_base_url');
        $publicRouteNamePrefix = config('payment.route_name_prefix.public');
        $path = route("$publicRouteNamePrefix.payment.callback", ['transaction' => $transaction->id, 'status' => $status], false);

        return "{$appBaseUrl}{$path}";
    }
}
