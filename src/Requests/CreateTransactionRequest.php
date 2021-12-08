<?php

namespace Devolon\Payment\Requests;

use Devolon\Common\Bases\Request;
use Devolon\Payment\DTOs\CreateTransactionDTO;
use Devolon\Payment\Payment;
use Devolon\Payment\Rules\ProductTypeSupportsTransactionRule;
use Devolon\Payment\Services\GetProductDataRulesService;
use Devolon\Payment\Services\GetPurchaseDataRulesService;
use Devolon\Payment\Services\PaymentGatewayDiscoveryService;
use Devolon\Payment\Services\ProductTypeDiscoveryService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

/**
 * swagger doc is in virtual directory (SwaggerRequestBodies)
 */
class CreateTransactionRequest extends Request
{
    protected function postRules(): array
    {
        /** @var PaymentGatewayDiscoveryService $gatewayDiscovery */
        $gatewayDiscovery = app(PaymentGatewayDiscoveryService::class);
        /** @var ProductTypeDiscoveryService $productTypeDiscovery */
        $productTypeDiscovery =  app(ProductTypeDiscoveryService::class);

        return [
            'product_type' => ['required', Rule::in([...Payment::getProductTypes(), ...$productTypeDiscovery->getAllNames()])],
            'payment_method' => [
                'required',
                Rule::in($gatewayDiscovery->getAllNames()),
            ],
            'payment_method_data' => ['filled', 'array'],
            'product_data' => ['filled', 'array'],
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

        $validator->after(function ($validator) {
            if ($validator->failed()) {
                return;
            }
            /** @var GetProductDataRulesService $getProductDataRulesService */
            $getProductDataRulesService = app(GetProductDataRulesService::class);
            try {
                $paymentMethodDataRules = $getProductDataRulesService($this->product_type);
            } catch (InvalidArgumentException) {
                return;
            }

            $prefixedRules = self::appendPrefixToArrayKeys($paymentMethodDataRules, 'product_data.');

            Validator::make($this->input(), $prefixedRules)->validate();
        });

        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            if ($validator->failed()) {
                return;
            }

            Validator::make(
                $this->input(),
                [
                    'product_type' => new ProductTypeSupportsTransactionRule($this->getCreateTransactionDTO()),
                ]
            )->validate();
        });
    }

    public function getCreateTransactionDTO(): CreateTransactionDTO
    {
        return CreateTransactionDTO::fromArray(
            array_merge(
                $this->validated(),
                [
                    'user_id' => $this->user()->id,
                ],
            )
        );
    }
}
