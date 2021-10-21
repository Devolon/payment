<?php

namespace Devolon\Payment\Resources;

use Devolon\Common\Bases\Resource;

/**
 * swagger doc is in virtual directory (SwaggerResponses)
 *
 * @property array<string> payment_gateways
 */
class PaymentGatewayListResource extends Resource
{
    public function toArray($request)
    {
        return [
            'payment_gateways' => $this->payment_gateways,
        ];
    }
}
