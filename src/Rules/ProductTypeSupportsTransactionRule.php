<?php

namespace Devolon\Payment\Rules;

use Devolon\Payment\DTOs\CreateTransactionDTO;
use Devolon\Payment\Services\ProductTypeDiscoveryService;
use Illuminate\Contracts\Validation\Rule;
use InvalidArgumentException;

class ProductTypeSupportsTransactionRule implements Rule
{
    public function __construct(private CreateTransactionDTO $createTransactionDTO)
    {
    }

    public function passes($attribute, $value)
    {
        /** @var ProductTypeDiscoveryService $productTypeDiscovery */
        $productTypeDiscovery =  app(ProductTypeDiscoveryService::class);
        try {
            $productType = $productTypeDiscovery->get($value);
        } catch (InvalidArgumentException) {
            return true;
        }

        return $productType->supports($this->createTransactionDTO);
    }

    public function message()
    {
        return ':attribute does not support transaction with given data';
    }
}
