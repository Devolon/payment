<?php

namespace Devolon\Payment\DTOs;

use Devolon\Common\Bases\DTO;

class PaymentGatewayListDTO extends DTO
{
    /**
     * @param array<string> $payment_gateways
     */
    public function __construct(public array $payment_gateways)
    {
    }
}
