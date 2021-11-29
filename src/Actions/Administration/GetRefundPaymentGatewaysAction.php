<?php

namespace Devolon\Payment\Actions\Administration;

use Devolon\Payment\DTOs\PaymentGatewayListDTO;
use Devolon\Payment\Services\PaymentGatewayDiscoveryService;

class GetRefundPaymentGatewaysAction
{
    public function __construct(private PaymentGatewayDiscoveryService $paymentGatewayDiscoveryService)
    {
    }

    public function __invoke(): PaymentGatewayListDTO
    {
        $gateways = $this->paymentGatewayDiscoveryService->getGatewaysWithRefund();

        return new PaymentGatewayListDTO($gateways);
    }
}