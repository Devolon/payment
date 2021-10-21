<?php

namespace Devolon\Payment\Requests;

use Devolon\Common\Bases\Request;
use Devolon\Payment\Payment;
use Devolon\Payment\Services\GetPurchaseDataRulesService;
use Devolon\Payment\Services\PaymentGatewayDiscoveryService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * swagger doc is in virtual directory (SwaggerRequestBodies)
 */
class CreateTransactionRequest extends Request
{
    protected function postRules(): array
    {
        /** @var PaymentGatewayDiscoveryService $gatewayDiscovery */
        $gatewayDiscovery = app(PaymentGatewayDiscoveryService::class);

        return [
            'product_type' => ['required', Rule::in(Payment::getProductTypes())],
            'payment_method' => ['required', Rule::in($gatewayDiscovery->getAllNames())],
            'payment_method_data' => ['filled', 'array'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($validator->failed()) {
                return;
            }
            /** @var GetPurchaseDataRulesService $getPurchaseDataRulesService */
            $getPurchaseDataRulesService = app(GetPurchaseDataRulesService::class);
            $paymentMethodDataRules = $getPurchaseDataRulesService($this->payment_method);

            $prefixedRules = self::appendPrefixToArrayKeys($paymentMethodDataRules, 'payment_method_data.');

            Validator::make($this->input(), $prefixedRules)->validate();
        });
    }
}
