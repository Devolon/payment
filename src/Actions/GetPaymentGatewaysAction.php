<?php

namespace Devolon\Payment\Actions;

use Devolon\Payment\DTOs\PaymentGatewayListDTO;
use Devolon\Payment\Services\PaymentGatewayDiscoveryService;

class GetPaymentGatewaysAction
{
    public function __construct(private PaymentGatewayDiscoveryService $paymentGatewayDiscoveryService)
    {
    }

    public function __invoke(): PaymentGatewayListDTO
    {
        $gateways = $this->paymentGatewayDiscoveryService->getAllNames();

        return new PaymentGatewayListDTO($gateways);
    }
}
