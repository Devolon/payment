<?php

namespace Devolon\Payment\Controllers\Administration;

use Devolon\Payment\Actions\Administration\GetRefundPaymentGatewaysAction;
use Devolon\Payment\Resources\PaymentGatewayListResource;

/**
 * swagger doc is in virtual directory (SwaggerPaths)
 */
class GetRefundPaymentGatewaysController
{
    public function __construct(private GetRefundPaymentGatewaysAction $getRefundPaymentGatewaysAction)
    {
    }

    public function __invoke(): PaymentGatewayListResource
    {
        $result = ($this->getRefundPaymentGatewaysAction)();

        return PaymentGatewayListResource::make($result);
    }
}
