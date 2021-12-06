<?php

namespace Devolon\Payment\Services;

use Devolon\Payment\Contracts\HasProductData;

class GetProductDataRulesService
{
    public function __construct(private ProductTypeDiscoveryService $productTypeDiscoveryService)
    {
    }

    public function __invoke(string $productTypeName): array
    {
        $gateway = ($this->productTypeDiscoveryService)->get($productTypeName);

        if (!$gateway instanceof HasProductData) {
            return [];
        }

        return $gateway->productDataRules();
    }
}
