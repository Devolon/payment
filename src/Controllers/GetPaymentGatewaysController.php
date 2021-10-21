<?php

namespace Devolon\Payment\Controllers;

use Devolon\Payment\Actions\GetPaymentGatewaysAction;
use Devolon\Payment\Resources\PaymentGatewayListResource;

/**
 * swagger doc is in virtual directory (SwaggerPaths)
 */
class GetPaymentGatewaysController
{
    public function __construct(private GetPaymentGatewaysAction $getPaymentGatewaysAction)
    {
    }

    public function __invoke(): PaymentGatewayListResource
    {
        $result = ($this->getPaymentGatewaysAction)();

        return PaymentGatewayListResource::make($result);
    }
}
