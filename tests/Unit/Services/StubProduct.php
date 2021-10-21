<?php

namespace Devolon\Payment\Tests\Unit\Services;

use Devolon\Payment\Contracts\HasPrice;
use Devolon\Payment\DTOs\CreateTransactionDTO;

class StubProduct implements HasPrice
{

    public static function calculatePrice(CreateTransactionDTO $createTransactionDTO): float
    {
        return 123.45;
    }
}
