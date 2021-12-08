<?php

namespace Devolon\Payment\Tests\Stubs;

use Devolon\Payment\Contracts\ProductTypeInterface;
use Devolon\Payment\DTOs\CreateTransactionDTO;

class OnlyDummyGatewayProductType implements ProductTypeInterface
{
    public const NAME = 'only_dummy_gateway_product';

    public function calculatePrice(CreateTransactionDTO $createTransactionDTO): float
    {
        return 10.5;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function supports(CreateTransactionDTO $createTransactionDTO): bool
    {
        return $createTransactionDTO->payment_method === 'dummy';
    }
}
