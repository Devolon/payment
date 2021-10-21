<?php

namespace Devolon\Payment\Services;

use Devolon\Payment\Contracts\HasPurchaseData;

class GetPurchaseDataRulesService
{
    public function __construct(private PaymentGatewayDiscoveryService $paymentGatewayDiscoveryService)
    {
    }

    public function __invoke(string $paymentMethod): array
    {
        $gateway = ($this->paymentGatewayDiscoveryService)->get($paymentMethod);

        if (!$gateway instanceof HasPurchaseData) {
            return [];
        }

        return $gateway->purchaseDataRules();
    }
}
