<?php

namespace Devolon\Payment\Tests\Stubs;

use Devolon\Payment\Contracts\HasProductData;
use Devolon\Payment\Contracts\ProductTypeInterface;
use Devolon\Payment\DTOs\CreateTransactionDTO;
use Illuminate\Validation\Rule;

class SampleProductType implements ProductTypeInterface, HasProductData
{
    public function calculatePrice(CreateTransactionDTO $createTransactionDTO): float
    {
        return 10.5;
    }

    public function getName(): string
    {
        return 'sample_product';
    }

    public function supports(CreateTransactionDTO $createTransactionDTO): bool
    {
        return true;
    }

    public function productDataRules(): array
    {
        return [
            'key' => ['required', Rule::in(['value'])]
        ];
    }
}
